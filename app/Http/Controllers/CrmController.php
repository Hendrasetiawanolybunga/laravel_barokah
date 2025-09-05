<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\PersonalDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CrmController extends Controller
{
    /**
     * Display CRM dashboard with customer segments
     */
    public function index()
    {
        // Pelanggan Loyal (Total belanja > 5.000.000)
        $loyalCustomers = User::where('role', 'customer')
            ->whereHas('orders', function ($query) {
                $query->where('status', 'shipped')
                      ->selectRaw('user_id, SUM(total) as total_spending')
                      ->groupBy('user_id')
                      ->havingRaw('SUM(total) > 4000000');
            })
            ->with(['customer', 'orders' => function ($query) {
                $query->where('status', 'shipped');
            }])
            ->get()
            ->map(function ($user) {
                $user->total_spending = $user->orders->sum('total');
                return $user;
            });

        // Pelanggan Ulang Tahun Bulan Ini
        $birthdayCustomers = User::where('role', 'customer')
            ->whereHas('customer', function ($customerQuery) {
                $customerQuery->whereNotNull('tgl_lahir')
                             ->whereRaw("strftime('%m', tgl_lahir) = ?", [now()->setTimezone('Asia/Jakarta')->format('m')]);
            })
            ->with(['customer', 'orders' => function ($query) {
                $query->where('status', 'shipped');
            }])
            ->get()
            ->map(function ($user) {
                $user->total_spending = $user->orders->sum('total');
                $user->birthday_date = $user->customer && $user->customer->tgl_lahir ? $user->customer->tgl_lahir->translatedFormat('d M') : '-';
                $user->is_birthday_today = $user->customer && $user->customer->tgl_lahir && $user->customer->tgl_lahir->format('m-d') === now()->setTimezone('Asia/Jakarta')->format('m-d');
                return $user;
            });

        // Pelanggan Loyal & Ulang Tahun
        $loyalWithBirthday = User::where('role', 'customer')
            ->whereHas('customer', function ($customerQuery) {
                $customerQuery->whereNotNull('tgl_lahir')
                             ->whereRaw("strftime('%m', tgl_lahir) = ?", [now()->setTimezone('Asia/Jakarta')->format('m')]);
            })
            ->whereHas('orders', function ($query) {
                $query->where('status', 'shipped')
                      ->selectRaw('user_id, SUM(total) as total_spending')
                      ->groupBy('user_id')
                      ->havingRaw('SUM(total) > 4000000');
            })
            ->with(['customer', 'orders' => function ($query) {
                $query->where('status', 'shipped');
            }])
            ->get()
            ->map(function ($user) {
                $user->total_spending = $user->orders->sum('total');
                $user->birthday_date = $user->customer && $user->customer->tgl_lahir ? $user->customer->tgl_lahir->translatedFormat('d M') : '-';
                $user->is_birthday_today = $user->customer && $user->customer->tgl_lahir && $user->customer->tgl_lahir->format('m-d') === now()->setTimezone('Asia/Jakarta')->format('m-d');
                return $user;
            });

        return view('admin.crm.index', compact('loyalCustomers', 'birthdayCustomers', 'loyalWithBirthday'));
    }

    /**
     * Send message to customer
     */
    public function sendMessage(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan tidak boleh kosong dan maksimal 1000 karakter.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $user->update([
                'message' => $request->message
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pesan berhasil dikirim ke {$user->name}."
                ]);
            }

            return back()->with('success', "Pesan berhasil dikirim ke {$user->name}.");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengirim pesan.'
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengirim pesan.']);
        }
    }

    /**
     * Set personal discount for customer
     */
    public function setDiscount(Request $request, User $user)
    {
        // Log incoming request for debugging
        Log::info('CRM setDiscount method called', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'route_params' => $request->route()->parameters()
        ]);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'persen_diskon' => 'required|numeric|min:1|max:100',
            'admin_note' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:today',
            'auto_message' => 'nullable|boolean',
            'custom_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('CRM setDiscount validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            // Verify user exists and is accessible
            if (!$user || !$user->id) {
                Log::error('CRM setDiscount: User not found or invalid', ['user' => $user]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer tidak ditemukan.'
                    ], 404);
                }
                return back()->withErrors(['error' => 'Customer tidak ditemukan.']);
            }

            // Find product with additional validation
            $product = Product::find($request->product_id);
            if (!$product) {
                Log::error('CRM setDiscount: Product not found', ['product_id' => $request->product_id]);
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk tidak ditemukan.'
                    ], 404);
                }
                return back()->withErrors(['error' => 'Produk tidak ditemukan.']);
            }
            
            // Set default expiration to 2 days if not provided dengan timezone Indonesia
            $expiresAt = $request->expires_at ? 
                        Carbon::parse($request->expires_at)->setTimezone('Asia/Jakarta') : 
                        Carbon::now()->setTimezone('Asia/Jakarta')->addDays(2);
            
            Log::info('CRM setDiscount: Creating/updating discount', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'persen_diskon' => $request->persen_diskon,
                'expires_at' => $expiresAt->toDateTimeString()
            ]);
            
            // Update or create discount
            $discount = PersonalDiscount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $request->product_id
                ],
                [
                    'persen_diskon' => $request->persen_diskon,
                    'admin_note' => $request->admin_note,
                    'is_active' => true,
                    'expires_at' => $expiresAt,
                ]
            );

            Log::info('CRM setDiscount: Discount created/updated successfully', [
                'discount_id' => $discount->id,
                'discount_data' => $discount->toArray()
            ]);

            // Generate automated message if requested
            if ($request->auto_message || $request->custom_message) {
                $message = $request->custom_message;
                
                if ($request->auto_message && !$message) {
                    $message = $this->generateDiscountMessage($user, $product, $request->persen_diskon, $expiresAt);
                }
                
                if ($message) {
                    $user->update(['message' => $message]);
                    Log::info('CRM setDiscount: Message updated for user', [
                        'user_id' => $user->id,
                        'message_length' => strlen($message)
                    ]);
                }
            }

            $responseData = [
                'success' => true,
                'message' => "Diskon {$request->persen_diskon}% berhasil ditetapkan untuk {$user->name} pada produk {$product->nama}.",
                'discount' => [
                    'id' => $discount->id,
                    'persen_diskon' => $discount->persen_diskon,
                    'expires_at' => $discount->expires_at->format('d/m/Y H:i'),
                ],
                'expires_at' => $expiresAt->format('d/m/Y H:i'),
            ];

            // Add generated message to response if auto-generated
            if ($request->auto_message && !$request->custom_message) {
                $responseData['generated_message'] = $this->generateDiscountMessage($user, $product, $request->persen_diskon, $expiresAt);
            }

            Log::info('CRM setDiscount: Success response prepared', ['response_data' => $responseData]);

            if ($request->ajax()) {
                return response()->json($responseData);
            }

            return back()->with('success', $responseData['message']);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('CRM setDiscount: Database error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A'
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan database saat menetapkan diskon.',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan database saat menetapkan diskon.']);
            
        } catch (\Exception $e) {
            Log::error('CRM setDiscount: General error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menetapkan diskon.',
                    'debug' => config('app.debug') ? [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ] : null
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menetapkan diskon.']);
        }
    }

    /**
     * Generate automated discount message dengan format waktu Indonesia
     */
    private function generateDiscountMessage(User $user, Product $product, $discountPercent, Carbon $expiresAt): string
    {
        $expireDateFormatted = $expiresAt->setTimezone('Asia/Jakarta')->translatedFormat('d F Y');
        $expireTimeFormatted = $expiresAt->setTimezone('Asia/Jakarta')->format('H:i');
        
        $messages = [
            "🎉 Selamat {$user->name}! Anda mendapat diskon khusus {$discountPercent}% untuk produk {$product->nama}. Diskon berlaku hingga {$expireDateFormatted} pukul {$expireTimeFormatted} WIB. Jangan sampai terlewat!",
            
            "⭐ Halo {$user->name}, ada kabar baik untuk Anda! Diskon spesial {$discountPercent}% untuk {$product->nama} telah kami berikan. Berlaku sampai {$expireDateFormatted} {$expireTimeFormatted} WIB.",
            
            "🛒 {$user->name}, dapatkan {$product->nama} dengan diskon {$discountPercent}%! Penawaran terbatas sampai {$expireDateFormatted} pukul {$expireTimeFormatted} WIB. Segera manfaatkan kesempatan ini!",
            
            "💎 Pelanggan terhormat {$user->name}, kami memberikan diskon eksklusif {$discountPercent}% untuk {$product->nama}. Masa berlaku: hingga {$expireDateFormatted} {$expireTimeFormatted} WIB."
        ];
        
        // Return random message or first one for consistency
        return $messages[0];
    }

    /**
     * Preview automated discount message (AJAX)
     */
    public function previewDiscountMessage(Request $request)
    {
        Log::info('CRM previewDiscountMessage called', $request->all());
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'persen_diskon' => 'required|numeric|min:1|max:100',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            Log::warning('CRM previewDiscountMessage validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($request->user_id);
            $product = Product::findOrFail($request->product_id);
            
            $expiresAt = $request->expires_at ? 
                        Carbon::parse($request->expires_at)->setTimezone('Asia/Jakarta') : 
                        Carbon::now()->setTimezone('Asia/Jakarta')->addDays(2);
            
            $message = $this->generateDiscountMessage($user, $product, $request->persen_diskon, $expiresAt);
            
            Log::info('CRM previewDiscountMessage success', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'message_length' => strlen($message)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'expires_at' => $expiresAt->format('d/m/Y H:i')
            ]);
            
        } catch (\Exception $e) {
            Log::error('CRM previewDiscountMessage error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat preview pesan.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get products for discount selection (AJAX)
     */
    public function getProducts()
    {
        try {
            $products = Product::select('id', 'nama', 'harga')
                ->orderBy('nama')
                ->get();

            Log::info('CRM getProducts success', ['count' => $products->count()]);

            return response()->json([
                'success' => true,
                'products' => $products
            ]);
            
        } catch (\Exception $e) {
            Log::error('CRM getProducts error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat produk.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get customer's existing discounts (AJAX)
     */
    public function getCustomerDiscounts(User $user)
    {
        $discounts = $user->personalDiscounts()
            ->active()
            ->with('product:id,nama,price')
            ->get();

        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }

    /**
     * Remove customer discount
     */
    public function removeDiscount(Request $request, PersonalDiscount $discount)
    {
        try {
            $customerName = $discount->user->name;
            $productName = $discount->product->nama;
            
            $discount->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Diskon untuk {$customerName} pada produk {$productName} berhasil dihapus."
                ]);
            }

            return back()->with('success', "Diskon untuk {$customerName} pada produk {$productName} berhasil dihapus.");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus diskon.'
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus diskon.']);
        }
    }
}