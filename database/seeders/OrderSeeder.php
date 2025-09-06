<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Arr;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers and products
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();
        
        if ($customers->isEmpty() || $products->isEmpty()) {
            echo "Warning: No customers or products found. Please run CustomerSeeder and ProductSeeder first.\n";
            return;
        }

        // Define possible order statuses
        $statuses = ['pending', 'paid', 'shipped', 'canceled'];

        // Create 10 orders
        for ($i = 1; $i <= 10; $i++) {
            // Select a random customer
            $customer = $customers->random();
            
            // Create order
            $order = Order::create([
                'tanggal' => now()->subDays(rand(1, 30)), // Random date within last 30 days
                'total' => 0, // Will be updated after adding items
                'status' => Arr::random($statuses),
                'user_id' => $customer->id,
            ]);

            // Add random order items (1-5 items per order)
            $total = 0;
            $itemCount = rand(1, 5);
            
            for ($j = 1; $j <= $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $subtotal = $product->harga * $quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'jumlah_item' => $quantity,
                    'sub_total' => $subtotal,
                ]);
                
                $total += $subtotal;
            }
            
            // Update order total
            $order->update(['total' => $total]);
        }
        
        echo "Created 10 dummy orders.\n";
    }
}