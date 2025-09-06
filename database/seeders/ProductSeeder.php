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
        $existingProducts = [
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

        // Add 10 more realistic products
        $additionalProducts = [
            [
                'nama' => 'Meja Kantor Modern',
                'foto' => 'placeholder-product.svg',
                'harga' => 750000,
                'deskripsi' => 'Meja kantor dengan desain modern dan bahan kayu jati berkualitas tinggi. Dilengkapi dengan laci penyimpanan.',
                'stok' => 15,
            ],
            [
                'nama' => 'Kursi Ergonomis',
                'foto' => 'placeholder-product.svg',
                'harga' => 450000,
                'deskripsi' => 'Kursi ergonomis dengan sandaran punggung yang dapat disesuaikan. Sangat nyaman untuk digunakan dalam jangka waktu lama.',
                'stok' => 25,
            ],
            [
                'nama' => 'Lemari Pakaian Kayu Jati',
                'foto' => 'placeholder-product.svg',
                'harga' => 1200000,
                'deskripsi' => 'Lemari pakaian berbahan kayu jati solid dengan 3 pintu dan 2 laci. Kapasitas penyimpanan yang luas.',
                'stok' => 8,
            ],
            [
                'nama' => 'Sofa Minimalis',
                'foto' => 'placeholder-product.svg',
                'harga' => 950000,
                'deskripsi' => 'Sofa dengan desain minimalis dan bahan kain berkualitas tinggi. Nyaman untuk ruang tamu keluarga.',
                'stok' => 12,
            ],
            [
                'nama' => 'Tempat Tidur King Size',
                'foto' => 'placeholder-product.svg',
                'harga' => 2500000,
                'deskripsi' => 'Tempat tidur ukuran king dengan frame kayu jati dan headboard elegan. Sangat kokoh dan tahan lama.',
                'stok' => 5,
            ],
            [
                'nama' => 'Meja Makan Bundar',
                'foto' => 'placeholder-product.svg',
                'harga' => 800000,
                'deskripsi' => 'Meja makan bundar dengan ukuran 120cm, cocok untuk keluarga dengan 4-6 orang. Terbuat dari kayu jati pilihan.',
                'stok' => 10,
            ],
            [
                'nama' => 'Rak Buku Kayu',
                'foto' => 'placeholder-product.svg',
                'harga' => 350000,
                'deskripsi' => 'Rak buku dengan 5 tingkat dan desain minimalis. Cocok untuk menyimpan buku, dekorasi, dan barang lainnya.',
                'stok' => 20,
            ],
            [
                'nama' => 'Kursi Tamu Vintage',
                'foto' => 'placeholder-product.svg',
                'harga' => 275000,
                'deskripsi' => 'Kursi tamu dengan desain vintage dan bahan kayu jati. Memberikan kesan klasik pada ruang tamu Anda.',
                'stok' => 18,
            ],
            [
                'nama' => 'Meja Samping Kayu',
                'foto' => 'placeholder-product.svg',
                'harga' => 180000,
                'deskripsi' => 'Meja samping dengan ukuran compact dan desain elegan. Cocok diletakkan di samping tempat tidur atau sofa.',
                'stok' => 30,
            ],
            [
                'nama' => 'Buffet Kayu Jati',
                'foto' => 'placeholder-product.svg',
                'harga' => 1650000,
                'deskripsi' => 'Buffet dengan 3 pintu dan 2 laci, dilengkapi dengan kaca display. Sangat cocok untuk menyimpan piring dan gelas.',
                'stok' => 7,
            ],
        ];

        $allProducts = array_merge($existingProducts, $additionalProducts);

        foreach ($allProducts as $product) {
            Product::updateOrCreate(
                ['nama' => $product['nama']],
                $product
            );
        }
        
        echo "Created/updated " . count($allProducts) . " products.\n";
    }
}