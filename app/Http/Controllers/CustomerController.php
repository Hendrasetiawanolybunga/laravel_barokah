<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Show customer home with admin message and product catalog
     */
    public function home()
    {
        $user = auth()->user();
        $products = Product::where('stok', '>', 0)->paginate(12);
        $cartCount = $this->getCartItemCount();

        return view('customer.home', compact('user', 'products', 'cartCount'));
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
     * Show cart
     */
    public function showCart()
    {
        $cartSession = session()->get('cart', []);
        
        if (empty($cartSession)) {
            return view('customer.cart', ['cart' => [], 'total' => 0]);
        }
        
        // Get product IDs from cart
        $productIds = array_keys($cartSession);
        
        // Fetch complete product data from database
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        $cart = [];
        $total = 0;
        
        foreach ($cartSession as $productId => $sessionItem) {
            $product = $products->get($productId);
            
            if ($product) {
                $cart[$productId] = [
                    'nama' => $product->nama,
                    'deskripsi' => $product->deskripsi,
                    'foto' => $product->foto,
                    'price' => $product->harga,
                    'quantity' => $sessionItem['quantity'],
                    'stok' => $product->stok,
                ];
                
                $total += $product->harga * $sessionItem['quantity'];
            }
        }
        
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

        return view('customer.cart', compact('cart', 'total'));
    }

    // ============ CHECKOUT FUNCTIONALITY ============

    /**
     * Show checkout page
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
        
        $cart = [];
        $total = 0;
        
        foreach ($cartSession as $productId => $sessionItem) {
            $product = $products->get($productId);
            
            if ($product) {
                $cart[$productId] = [
                    'nama' => $product->nama,
                    'deskripsi' => $product->deskripsi,
                    'foto' => $product->foto,
                    'price' => $product->harga,
                    'quantity' => $sessionItem['quantity'],
                    'stok' => $product->stok,
                ];
                
                $total += $product->harga * $sessionItem['quantity'];
            }
        }

        return view('customer.checkout', compact('cart', 'total'));
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
            // Calculate total
            $total = 0;
            foreach ($cartSession as $productId => $sessionItem) {
                $product = $products->get($productId);
                if ($product) {
                    $total += $product->harga * $sessionItem['quantity'];
                }
            }

            // Upload payment proof
            $buktiPath = $request->file('bukti_bayar')->store('payment-proofs', 'public');

            // Create order
            $order = Order::create([
                'tanggal' => Carbon::now(),
                'total' => $total,
                'bukti_bayar' => $buktiPath,
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);

            // Create order items
            foreach ($cartSession as $productId => $sessionItem) {
                $product = $products->get($productId);
                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'jumlah_item' => $sessionItem['quantity'],
                        'sub_total' => $product->harga * $sessionItem['quantity'],
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
