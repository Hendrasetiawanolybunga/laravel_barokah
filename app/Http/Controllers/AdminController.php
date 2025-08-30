<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;

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
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show create product form
     */
    public function createProduct()
    {
        return view('admin.products.create');
    }

    /**
     * Store new product
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

            return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan produk.'])->withInput();
        }
    }

    /**
     * Show edit product form
     */
    public function editProduct(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update product
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

            return redirect()->route('admin.products')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
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

    // ============ CUSTOMER MANAGEMENT (CRM) ============

    /**
     * Show all customers with CRM features
     */
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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

    // ============ ORDER MANAGEMENT ============

    /**
     * Show all orders
     */
    public function orders()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
            return back()->withErrors($validator);
        }

        try {
            $order->update([
                'status' => $request->status
            ]);

            return back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status pesanan.']);
        }
    }
}
