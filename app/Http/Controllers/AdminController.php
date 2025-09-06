<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\PersonalDiscount;

class AdminController extends Controller
{
    /**
     * Show admin dashboard with statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total'),
        ];

        $recent_orders = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }

    // ============ PRODUCT MANAGEMENT ============

    /**
     * Show all products
     */
    public function products()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(5);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Store new product (AJAX)
     */
    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'required|string',
            'stok' => 'required|integer|min:0',
        ], [
            'nama.required' => 'Nama produk wajib diisi.',
            'foto.required' => 'Foto produk wajib diupload.',
            'foto.image' => 'File harus berupa gambar.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Upload photo
            $fotoPath = $request->file('foto')->store('products', 'public');

            Product::create([
                'nama' => $request->nama,
                'foto' => $fotoPath,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'stok' => $request->stok,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan.'
                ]);
            }
            return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan produk.'
                ]);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan produk.'])->withInput();
        }
    }

    /**
     * Update product (AJAX)
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'required|string',
            'stok' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'stok' => $request->stok,
            ];

            // Update photo if uploaded
            if ($request->hasFile('foto')) {
                // Delete old photo
                if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                    Storage::disk('public')->delete($product->foto);
                }
                $data['foto'] = $request->file('foto')->store('products', 'public');
            }

            $product->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil diperbarui.'
                ]);
            }
            return redirect()->route('admin.products')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui produk.'
                ]);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui produk.'])->withInput();
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product)
    {
        try {
            // Delete photo
            if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                Storage::disk('public')->delete($product->foto);
            }

            $product->delete();

            return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus produk.']);
        }
    }

    /**
     * Get product data for AJAX requests
     */
    public function getProduct(Product $product)
    {
        try {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => $product->harga,
                    'stok' => $product->stok,
                    'deskripsi' => $product->deskripsi,
                    'foto' => $product->foto
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }
    }

    // ============ CUSTOMER MANAGEMENT (CRM) ============

    /**
     * Show all customers with CRM features
     */
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Update customer loyalty status
     */
    public function updateLoyalty(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'is_loyal' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $user->update([
                'is_loyal' => $request->is_loyal
            ]);

            $message = $request->is_loyal ? 'Customer berhasil ditandai sebagai loyal.' : 'Status loyal customer berhasil dihapus.';
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status loyalty.']);
        }
    }

    /**
     * Update customer message
     */
    public function updateMessage(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $user->update([
                'message' => $request->message
            ]);

            return back()->with('success', 'Pesan untuk customer berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui pesan.']);
        }
    }

    /**
     * Store new customer
     */
    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'alamat' => 'required|string|max:500',
            'tgl_lahir' => 'required|date',
            'no_hp' => 'required|string|max:20',
            'pekerjaan' => 'required|string|max:100',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'customer',
            ]);

            // Create customer profile
            Customer::create([
                'user_id' => $user->id,
                'alamat' => $request->alamat,
                'tgl_lahir' => $request->tgl_lahir,
                'no_hp' => $request->no_hp,
                'pekerjaan' => $request->pekerjaan,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil ditambahkan.',
                    'customer' => $user->load('customer')
                ]);
            }

            return back()->with('success', 'Customer berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menambahkan customer.'
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan customer.'])->withInput();
        }
    }

    /**
     * Update customer data
     */
    public function updateCustomer(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'alamat' => 'required|string|max:500',
            'tgl_lahir' => 'required|date',
            'no_hp' => 'required|string|max:20',
            'pekerjaan' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update or create customer profile
            $user->customer()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'alamat' => $request->alamat,
                    'tgl_lahir' => $request->tgl_lahir,
                    'no_hp' => $request->no_hp,
                    'pekerjaan' => $request->pekerjaan,
                ]
            );

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data customer berhasil diperbarui.',
                    'customer' => $user->fresh()->load('customer')
                ]);
            }

            return back()->with('success', 'Data customer berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data customer.'
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data customer.'])->withInput();
        }
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(User $user)
    {
        try {
            // Delete customer profile first
            if ($user->customer) {
                $user->customer()->delete();
            }
            
            // Delete user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus customer.'
            ], 500);
        }
    }

    // ============ ORDER MANAGEMENT ============

    /**
     * Store new order (manual creation by admin)
     */
    public function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,paid,shipped,delivered,canceled',
        ], [
            'user_id.required' => 'Customer wajib dipilih.',
            'user_id.exists' => 'Customer tidak ditemukan.',
            'items.required' => 'Produk wajib dipilih.',
            'items.min' => 'Minimal harus ada 1 produk.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.required' => 'Kuantitas wajib diisi.',
            'items.*.quantity.min' => 'Kuantitas minimal 1.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $total = 0;
            $orderItems = [];

            // Calculate total and prepare order items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Produk tidak ditemukan.');
                }

                $subtotal = $product->harga * $item['quantity'];
                $total += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'jumlah_item' => $item['quantity'],
                    'sub_total' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Create order
            $order = Order::create([
                'user_id' => $request->user_id,
                'total' => $total,
                'status' => $request->status,
                'bukti_bayar' => null, // Manual orders don't need payment proof
            ]);

            // Create order items
            foreach ($orderItems as &$orderItem) {
                $orderItem['order_id'] = $order->id;
            }
            $order->orderItems()->createMany($orderItems);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat.',
                    'order' => $order->load(['user', 'orderItems.product'])
                ]);
            }

            return back()->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Get products for order creation (AJAX)
     */
    public function getProducts(Request $request)
    {
        $products = Product::where('stok', '>', 0)
            ->select('id', 'nama', 'harga', 'stok')
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Get customers for order creation (AJAX)
     */
    public function getCustomers(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->with('customer')
            ->select('id', 'name', 'email', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);
    }

    /**
     * Show all orders
     */
    public function orders()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,paid,shipped,canceled',
        ]);

        if ($validator->fails()) {
            // Handle AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $order->update([
                'status' => $request->status
            ]);

            // Handle AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status pesanan berhasil diperbarui.',
                    'order' => $order->fresh()
                ]);
            }

            return back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui status pesanan.'
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status pesanan.']);
        }
    }

    // ============ REPORTS ============

    /**
     * Show reports page
     */
    public function reports()
    {
        // Monthly sales for last 12 months
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = Order::whereYear('created_at', $date->year)
                         ->whereMonth('created_at', $date->month)
                         ->where('status', 'paid')
                         ->sum('total');
            
            $monthlySales[] = [
                'month' => $date->format('M Y'),
                'total' => $total
            ];
        }

        // Top selling products
        $topProducts = Product::withSum('orderItems', 'jumlah_item')
            ->orderBy('order_items_sum_jumlah_item', 'desc')
            ->take(10)
            ->get();

        // Recent sales statistics
        $stats = [
            'total_sales_this_month' => Order::whereYear('created_at', now()->year)
                                           ->whereMonth('created_at', now()->month)
                                           ->where('status', 'paid')
                                           ->sum('total'),
            'total_orders_this_month' => Order::whereYear('created_at', now()->year)
                                            ->whereMonth('created_at', now()->month)
                                            ->count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total'),
            'total_orders' => Order::count(),
        ];

        return view('admin.reports.index', compact('monthlySales', 'topProducts', 'stats'));
    }

    // ============ CRM HISTORY ============

    /**
     * Show CRM history page with messages and discounts
     */
    public function crmHistory(Request $request)
    {
        // Get users with messages (not null) - paginated
        $messageQuery = User::whereNotNull('message')
            ->select('id', 'name', 'email', 'message', 'updated_at')
            ->orderBy('updated_at', 'desc');

        // Get personal discounts with product info - paginated
        $discountQuery = PersonalDiscount::with(['user', 'product'])
            ->orderBy('created_at', 'desc');

        // Since we're combining two different collections, we'll get all records
        // and then manually paginate the combined result
        $messageHistory = $messageQuery->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'type' => 'message',
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'content' => $user->message,
                    'created_at' => $user->updated_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB',
                    'updated_at' => $user->updated_at
                ];
            });

        $discountHistory = $discountQuery->get()
            ->map(function ($discount) {
                $productName = $discount->product ? $discount->product->nama : 'Unknown Product';
                return [
                    'id' => $discount->id,
                    'type' => 'discount',
                    'customer_name' => $discount->user->name ?? 'Unknown',
                    'customer_email' => $discount->user->email ?? 'Unknown',
                    'content' => "{$discount->persen_diskon}% untuk {$productName}",
                    'created_at' => $discount->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB',
                    'updated_at' => $discount->updated_at,
                    'expires_at' => $discount->expires_at ? $discount->expires_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB' : 'Tidak ada',
                    'is_active' => $discount->isValid(),
                    'admin_note' => $discount->admin_note
                ];
            });

        // Combine and sort all history items by date
        $allHistory = $messageHistory->merge($discountHistory)
            ->sortByDesc('updated_at')
            ->values();

        // Manual pagination
        $perPage = 5;
        $currentPage = $request->get('page', 1);
        $currentPageItems = $allHistory->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedHistory = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allHistory->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('admin.crm.history', compact('paginatedHistory'));
    }

    /**
     * Delete CRM history item
     */
    public function deleteCrmHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|in:message,discount',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.'
            ], 422);
        }

        try {
            if ($request->type === 'message') {
                $user = User::findOrFail($request->id);
                $user->update(['message' => null]);
                $message = 'Pesan berhasil dihapus dari riwayat.';
            } else {
                $discount = PersonalDiscount::findOrFail($request->id);
                $discount->delete();
                $message = 'Diskon berhasil dihapus dari riwayat.';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus riwayat.'
            ], 500);
        }
    }

    /**
     * Update CRM history item (for messages)
     */
    public function updateCrmHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|in:message',
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->type === 'message') {
                $user = User::findOrFail($request->id);
                $user->update(['message' => $request->content]);
                $message = 'Pesan berhasil diperbarui.';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pesan yang dapat diperbarui.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui riwayat.'
            ], 500);
        }
    }
}
