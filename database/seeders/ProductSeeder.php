<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'nama' => 'Produk Unggulan 1',
                'foto' => 'placeholder-product.svg',
                'harga' => 150000,
                'deskripsi' => 'Ini adalah produk unggulan pertama kami dengan kualitas terbaik. Dibuat dengan bahan premium dan proses yang teliti.',
                'stok' => 50,
            ],
            [
                'nama' => 'Produk Unggulan 2',
                'foto' => 'placeholder-product.svg',
                'harga' => 200000,
                'deskripsi' => 'Produk unggulan kedua dengan inovasi terbaru. Sangat cocok untuk kebutuhan harian Anda.',
                'stok' => 30,
            ],
            [
                'nama' => 'Produk Unggulan 3',
                'foto' => 'placeholder-product.svg',
                'harga' => 175000,
                'deskripsi' => 'Produk unggulan ketiga dengan desain yang menarik dan fungsi yang optimal.',
                'stok' => 40,
            ],
            [
                'nama' => 'Produk Eksklusif A',
                'foto' => 'placeholder-product.svg',
                'harga' => 300000,
                'deskripsi' => 'Produk eksklusif dengan kualitas premium dan desain yang elegan.',
                'stok' => 20,
            ],
            [
                'nama' => 'Produk Ekonomis B',
                'foto' => 'placeholder-product.svg',
                'harga' => 100000,
                'deskripsi' => 'Produk dengan harga terjangkau namun tetap berkualitas tinggi.',
                'stok' => 100,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['nama' => $product['nama']],
                $product
            );
        }
    }
}
