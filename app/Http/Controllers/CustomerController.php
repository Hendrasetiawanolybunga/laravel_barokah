<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PersonalDiscount;
use App\Models\User;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Show customer home with admin message, discount notifications, and product catalog
     */
    public function home()
    {
        $user = auth()->user();
        $products = Product::where('stok', '>', 0)->paginate(12);
        $cartCount = $this->getCartItemCount();
        
        // Get user's active personal discounts
        $activeDiscounts = PersonalDiscount::where('user_id', $user->id)
            ->active()
            ->with('product:id,nama,harga')
            ->get();
        
        // Check if today is user's birthday using customer relationship dengan timezone Indonesia
        $isBirthday = $user->customer && 
                     $user->customer->tgl_lahir && 
                     $user->customer->tgl_lahir->format('m-d') === now()->setTimezone('Asia/Jakarta')->format('m-d');
        
        // Get user's spending for loyalty status
        $totalSpending = $user->orders()
            ->where('status', 'delivered')
            ->sum('total');
        
        $isLoyalCustomer = $totalSpending > 5000000; // 5M threshold for loyal customer

        // Cek apakah ada diskon baru yang belum ditampilkan sebagai alert
        $this->checkAndSetNewDiscountAlert($activeDiscounts);

        return view('customer.home', compact(
            'user', 'products', 'cartCount', 'activeDiscounts', 
            'isBirthday', 'totalSpending', 'isLoyalCustomer'
        ));
    }

    /**
     * Cek dan set alert untuk diskon baru (hanya sekali per sesi)
     */
    private function checkAndSetNewDiscountAlert($activeDiscounts)
    {
        // Ambil ID diskon yang sudah pernah ditampilkan dari sesi
        $shownDiscountIds = session()->get('shown_discount_alerts', []);
        
        $newDiscounts = [];
        
        foreach ($activeDiscounts as $discount) {
            // Jika diskon belum pernah ditampilkan sebagai alert
            if (!in_array($discount->id, $shownDiscountIds)) {
                $newDiscounts[] = $discount;
                
                // Tandai diskon ini sudah ditampilkan
                $shownDiscountIds[] = $discount->id;
            }
        }
        
        // Jika ada diskon baru, tampilkan alert
        if (count($newDiscounts) > 0) {
            $alertMessage = count($newDiscounts) === 1 
                ? "Anda mendapat diskon baru {$newDiscounts[0]->persen_diskon}% untuk {$newDiscounts[0]->product->nama}!"
                : "Anda mendapat " . count($newDiscounts) . " diskon baru! Cek halaman notifikasi untuk detail.";
            
            session()->flash('new_discount_alert', $alertMessage);
        }
        
        // Update sesi dengan ID diskon yang sudah ditampilkan
        session()->put('shown_discount_alerts', $shownDiscountIds);
    }

    // ============ NOTIFICATION FUNCTIONALITY ============

    /**
     * Show customer notifications page
     */
    public function notifications()
    {
        $user = auth()->user();
        
        // Get user's active personal discounts
        $activeDiscounts = PersonalDiscount::where('user_id', $user->id)
            ->active()
            ->with('product:id,nama,harga')
            ->get();
        
        return view('customer.notifications', compact('user', 'activeDiscounts'));
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function markNotificationRead(Request $request)
    {
        $user = auth()->user();
        
        try {
            if ($request->type === 'message') {
                $user->markMessageAsRead();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllNotificationsRead(Request $request)
    {
        $user = auth()->user();
        
        try {
            $user->markMessageAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    /**
     * Delete a notification (AJAX)
     */
    public function deleteNotification(Request $request)
    {
        $user = auth()->user();
        
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'type' => 'required|string|in:discount,message',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid'
                ], 422);
            }
            
            if ($request->type === 'discount') {
                // Delete personal discount
                $discount = PersonalDiscount::where('id', $request->id)
                    ->where('user_id', $user->id)
                    ->first();
                
                if ($discount) {
                    $discount->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Notifikasi diskon berhasil dihapus'
                    ]);
                }
            } elseif ($request->type === 'message') {
                // Clear admin message
                $user->update(['message' => null]);
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan admin berhasil dihapus'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus notifikasi'
            ], 500);
        }
    }

    /**
     * Get notifications data for modal (AJAX)
     */
    public function getNotificationsData(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get user's personal discounts with timezone Indonesia
            $discounts = PersonalDiscount::where('user_id', $user->id)
                ->with('product:id,nama,harga')
                ->get()
                ->map(function ($discount) {
                    $isValid = $discount->isValid();
                    return [
                        'id' => $discount->id,
                        'type' => 'discount',
                        'title' => "Diskon {$discount->persen_diskon}% - {$discount->product->nama}",
                        'message' => $discount->admin_note ?: "Diskon {$discount->persen_diskon}% berlaku untuk produk {$discount->product->nama}",
                        'created_at' => $discount->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB',
                        'expires_at' => $discount->expires_at ? $discount->expires_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB' : null,
                        'is_active' => $isValid,
                        'icon' => 'fas fa-percent',
                        'color' => $isValid ? 'success' : 'danger'
                    ];
                });
            
            $notifications = [];
            
            // Add admin message as notification if exists
            if ($user->message) {
                $notifications[] = [
                    'id' => 'admin_message',
                    'type' => 'message',
                    'title' => 'Pesan dari Admin',
                    'message' => $user->message,
                    'created_at' => $user->updated_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y H:i') . ' WIB',
                    'expires_at' => null,
                    'is_active' => true,
                    'icon' => 'fas fa-bullhorn',
                    'color' => 'info'
                ];
            }
            
            // Add birthday notification if applicable
            $isBirthday = $user->customer && 
                         $user->customer->tgl_lahir && 
                         $user->customer->tgl_lahir->format('m-d') === now()->setTimezone('Asia/Jakarta')->format('m-d');
            
            if ($isBirthday) {
                $notifications[] = [
                    'id' => 'birthday',
                    'type' => 'birthday',
                    'title' => 'Selamat Ulang Tahun! 🎉',
                    'message' => "Selamat ulang tahun, {$user->name}! Terima kasih telah menjadi bagian dari keluarga UD. Barokah Jaya Beton.",
                    'created_at' => now()->setTimezone('Asia/Jakarta')->translatedFormat('d M Y'),
                    'expires_at' => null,
                    'is_active' => true,
                    'icon' => 'fas fa-birthday-cake',
                    'color' => 'warning'
                ];
            }
            
            // Merge and sort notifications
            $allNotifications = collect($notifications)->concat($discounts)
                ->sortByDesc('created_at')
                ->values()
                ->all();
            
            return response()->json([
                'success' => true,
                'notifications' => $allNotifications,
                'total_count' => count($allNotifications),
                'unread_count' => count($notifications) + $discounts->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat notifikasi'
            ], 500);
        }
    }

    // ============ PROFILE MANAGEMENT ============

    /**
     * Show profile edit form (returns JSON for modal)
     */
    public function editProfile()
    {
        $user = auth()->user();
        $customer = $user->customer;
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'customer' => $customer ? [
                    'alamat' => $customer->alamat,
                    'no_hp' => $customer->no_hp,
                    'tgl_lahir' => $customer->tgl_lahir ? $customer->tgl_lahir->format('Y-m-d') : null,
                ] : null
            ]
        ]);
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'alamat' => 'nullable|string|max:500',
            'no_hp' => 'nullable|string|max:20',
            'tgl_lahir' => 'nullable|date|before:today',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'tgl_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            
            if (!empty($request->password)) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $user->update($userData);

            // Update or create customer profile
            if ($user->customer) {
                $user->customer->update([
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                    'tgl_lahir' => $request->tgl_lahir ? Carbon::parse($request->tgl_lahir) : null,
                ]);
            } else {
                $user->customer()->create([
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                    'tgl_lahir' => $request->tgl_lahir ? Carbon::parse($request->tgl_lahir) : null,
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui.'
                ]);
            }
            
            return back()->with('success', 'Profil berhasil diperbarui.');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui profil.'
                ], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profil.']);
        }
    }

    // ============ SHOPPING CART FUNCTIONALITY ============

    /**
     * Add product to cart
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $product = Product::find($request->product_id);
        
        if (!$product || !$product->isAvailable()) {
            return response()->json(['success' => false, 'message' => 'Produk tidak tersedia.']);
        }

        if ($request->quantity > $product->stok) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.']);
        }

        $cart = session()->get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $request->quantity;
            if ($newQuantity > $product->stok) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.']);
            }
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $cart[$productId] = [
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'cartCount' => $this->getCartItemCount()
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $product = Product::find($request->product_id);
        
        if ($request->quantity > $product->stok) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.']);
        }

        $cart = session()->get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui.',
            'cartCount' => $this->getCartItemCount()
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $productId = $request->product_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang.',
            'cartCount' => $this->getCartItemCount()
        ]);
    }

    /**
     * Show cart with personal discounts applied
     */
    public function showCart()
    {
        $cartSession = session()->get('cart', []);
        
        if (empty($cartSession)) {
            return view('customer.cart', ['cart' => [], 'total' => 0, 'originalTotal' => 0, 'totalDiscount' => 0]);
        }
        
        // Get product IDs from cart
        $productIds = array_keys($cartSession);
        
        // Fetch complete product data from database
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        // Get user's active personal discounts for cart products with explicit expiration check
        $userDiscounts = PersonalDiscount::where('user_id', auth()->id())
            ->whereIn('product_id', $productIds)
            ->active()
            ->with('product:id,nama')
            ->get()
            ->filter(function ($discount) {
                return $discount->isValid(); // Double-check expiration
            })
            ->keyBy('product_id');
        
        $cart = [];
        $total = 0;
        $originalTotal = 0;
        $totalDiscount = 0;
        
        foreach ($cartSession as $productId => $sessionItem) {
            $product = $products->get($productId);
            
            if ($product) {
                $originalPrice = $product->harga;
                $finalPrice = $originalPrice;
                $discountPercent = 0;
                $hasDiscount = false;
                
                // Check if user has personal discount for this product
                if ($userDiscounts->has($productId)) {
                    $discount = $userDiscounts->get($productId);
                    if ($discount->isValid()) {
                        $finalPrice = $discount->applyDiscount($originalPrice);
                        $discountPercent = $discount->persen_diskon;
                        $hasDiscount = true;
                    }
                }
                
                $itemOriginalTotal = $originalPrice * $sessionItem['quantity'];
                $itemFinalTotal = $finalPrice * $sessionItem['quantity'];
                
                $cart[$productId] = [
                    'nama' => $product->nama,
                    'deskripsi' => $product->deskripsi,
                    'foto' => $product->foto,
                    'price' => $finalPrice,
                    'original_price' => $originalPrice,
                    'quantity' => $sessionItem['quantity'],
                    'stok' => $product->stok,
                    'has_discount' => $hasDiscount,
                    'discount_percent' => $discountPercent,
                    'item_discount_amount' => $itemOriginalTotal - $itemFinalTotal,
                ];
                
                $total += $itemFinalTotal;
                $originalTotal += $itemOriginalTotal;
            }
        }
        
        $totalDiscount = $originalTotal - $total;
        
        // Clean up cart session if any products no longer exist
        if (count($cart) !== count($cartSession)) {
            $cleanCart = [];
            foreach ($cart as $productId => $item) {
                $cleanCart[$productId] = [
                    'quantity' => $item['quantity']
                ];
            }
            session()->put('cart', $cleanCart);
        }

        return view('customer.cart', compact('cart', 'total', 'originalTotal', 'totalDiscount'));
    }

    // ============ CHECKOUT FUNCTIONALITY ============

    /**
     * Show checkout page with personal discounts applied
     */
    public function checkout()
    {
        $cartSession = session()->get('cart', []);
        
        if (empty($cartSession)) {
            return redirect()->route('customer.home')->with('error', 'Keranjang belanja kosong.');
        }

        // Get product IDs from cart
        $productIds = array_keys($cartSession);
        
        // Fetch complete product data from database
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        // Get user's active personal discounts for cart products with explicit expiration check
        $userDiscounts = PersonalDiscount::where('user_id', auth()->id())
            ->whereIn('product_id', $productIds)
            ->active()
            ->with('product:id,nama')
            ->get()
            ->filter(function ($discount) {
                return $discount->isValid(); // Double-check expiration
            })
            ->keyBy('product_id');
        
        $cart = [];
        $total = 0;
        $originalTotal = 0;
        $totalDiscount = 0;
        $appliedDiscounts = [];
        
        foreach ($cartSession as $productId => $sessionItem) {
            $product = $products->get($productId);
            
            if ($product) {
                $originalPrice = $product->harga;
                $finalPrice = $originalPrice;
                $discountPercent = 0;
                $hasDiscount = false;
                
                // Check if user has personal discount for this product
                if ($userDiscounts->has($productId)) {
                    $discount = $userDiscounts->get($productId);
                    if ($discount->isValid()) {
                        $finalPrice = $discount->applyDiscount($originalPrice);
                        $discountPercent = $discount->persen_diskon;
                        $hasDiscount = true;
                        
                        $appliedDiscounts[] = [
                            'product_name' => $product->nama,
                            'discount_percent' => $discountPercent,
                            'savings' => ($originalPrice - $finalPrice) * $sessionItem['quantity']
                        ];
                    }
                }
                
                $itemOriginalTotal = $originalPrice * $sessionItem['quantity'];
                $itemFinalTotal = $finalPrice * $sessionItem['quantity'];
                
                $cart[$productId] = [
                    'nama' => $product->nama,
                    'deskripsi' => $product->deskripsi,
                    'foto' => $product->foto,
                    'price' => $finalPrice,
                    'original_price' => $originalPrice,
                    'quantity' => $sessionItem['quantity'],
                    'stok' => $product->stok,
                    'has_discount' => $hasDiscount,
                    'discount_percent' => $discountPercent,
                ];
                
                $total += $itemFinalTotal;
                $originalTotal += $itemOriginalTotal;
            }
        }
        
        $totalDiscount = $originalTotal - $total;

        return view('customer.checkout', compact('cart', 'total', 'originalTotal', 'totalDiscount', 'appliedDiscounts'));
    }

    /**
     * Process checkout
     */
    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'bukti_bayar.required' => 'Bukti pembayaran wajib diupload.',
            'bukti_bayar.image' => 'File harus berupa gambar.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cartSession = session()->get('cart', []);
        
        if (empty($cartSession)) {
            return redirect()->route('customer.home')->with('error', 'Keranjang belanja kosong.');
        }

        // Get product IDs from cart
        $productIds = array_keys($cartSession);
        
        // Fetch complete product data from database
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        try {
            // Get user's active personal discounts for cart products
            $userDiscounts = PersonalDiscount::where('user_id', auth()->id())
                ->whereIn('product_id', $productIds)
                ->active()
                ->get()
                ->keyBy('product_id');
            
            // Calculate total with discounts applied
            $total = 0;
            $originalTotal = 0;
            $appliedDiscounts = [];
            
            foreach ($cartSession as $productId => $sessionItem) {
                $product = $products->get($productId);
                if ($product) {
                    $originalPrice = $product->harga;
                    $finalPrice = $originalPrice;
                    
                    // Check if user has personal discount for this product
                    if ($userDiscounts->has($productId)) {
                        $discount = $userDiscounts->get($productId);
                        if ($discount->isValid()) {
                            $finalPrice = $discount->applyDiscount($originalPrice);
                            $appliedDiscounts[$productId] = [
                                'discount_id' => $discount->id,
                                'original_price' => $originalPrice,
                                'discounted_price' => $finalPrice,
                                'discount_percent' => $discount->persen_diskon
                            ];
                        }
                    }
                    
                    $total += $finalPrice * $sessionItem['quantity'];
                    $originalTotal += $originalPrice * $sessionItem['quantity'];
                }
            }

            // Upload payment proof
            $buktiPath = $request->file('bukti_bayar')->store('payment-proofs', 'public');

            // Create order with discounted total menggunakan timezone Indonesia
            $order = Order::create([
                'tanggal' => Carbon::now()->setTimezone('Asia/Jakarta'),
                'total' => $total,
                'bukti_bayar' => $buktiPath,
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);

            // Create order items with discount information
            foreach ($cartSession as $productId => $sessionItem) {
                $product = $products->get($productId);
                if ($product) {
                    $originalPrice = $product->harga;
                    $finalPrice = $originalPrice;
                    
                    // Apply discount if available
                    if (isset($appliedDiscounts[$productId])) {
                        $finalPrice = $appliedDiscounts[$productId]['discounted_price'];
                    }
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'jumlah_item' => $sessionItem['quantity'],
                        'sub_total' => $finalPrice * $sessionItem['quantity'],
                    ]);

                    // Update product stock
                    $product->decrement('stok', $sessionItem['quantity']);
                }
            }

            // Clear cart
            session()->forget('cart');

            return redirect()->route('customer.orders')->with('success', 'Pesanan berhasil dibuat. Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses pesanan.'])->withInput();
        }
    }

    // ============ ORDER MANAGEMENT ============

    /**
     * Show customer orders
     */
    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        // Check if order belongs to current user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['orderItems.product']);
        return view('customer.order-detail', compact('order'));
    }

    /**
     * Submit product review
     */
    public function submitReview(Request $request, OrderItem $orderItem)
    {
        // Check if order item belongs to current user
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk review ini.');
        }

        // Check if order can be reviewed
        if (!$orderItem->canBeReviewed()) {
            return back()->withErrors(['error' => 'Produk tidak dapat direview atau sudah direview.']);
        }

        $validator = Validator::make($request->all(), [
            'ulasan' => 'required|string|max:1000',
        ], [
            'ulasan.required' => 'Ulasan wajib diisi.',
            'ulasan.max' => 'Ulasan maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $orderItem->update([
                'ulasan' => $request->ulasan
            ]);

            return back()->with('success', 'Review berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengirim review.']);
        }
    }

    // ============ HELPER METHODS ============

    /**
     * Get cart item count
     */
    private function getCartItemCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        
        return $count;
    }
}
