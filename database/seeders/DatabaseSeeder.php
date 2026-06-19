<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@cimuning.test'],
            [
                'name' => 'Admin Cimuning',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );

        $categories = collect([
            ['name' => 'Kuliner', 'description' => 'Makanan, minuman, kue, katering, dan pesanan rumahan.'],
            ['name' => 'Fashion', 'description' => 'Pakaian, hijab, aksesoris, dan produk gaya hidup.'],
            ['name' => 'Jasa', 'description' => 'Layanan rumah tangga, servis, desain, dan kebutuhan harian.'],
            ['name' => 'Toko Harian', 'description' => 'Warung, sembako, perlengkapan harian, dan toko sekitar rumah.'],
            ['name' => 'Kecantikan', 'description' => 'Salon, skincare, makeup, dan perawatan diri.'],
            ['name' => 'Digital', 'description' => 'Desain, percetakan, konten, dan jasa berbasis digital.'],
            ['name' => 'Otomotif', 'description' => 'Bengkel, servis kendaraan, dan perlengkapan motor.'],
            ['name' => 'Produk Kreatif', 'description' => 'Kerajinan, souvenir, hampers, dan produk custom.'],
            ['name' => 'Pendidikan', 'description' => 'Kursus, les, bimbingan belajar, dan layanan edukasi.'],
            ['name' => 'Kesehatan', 'description' => 'Klinik, terapi, herbal, alat kesehatan, dan layanan kebugaran.'],
            ['name' => 'Laundry', 'description' => 'Cuci setrika, dry clean, laundry kiloan, dan layanan kebersihan pakaian.'],
            ['name' => 'Elektronik', 'description' => 'Servis gadget, komputer, perangkat elektronik, dan aksesoris.'],
            ['name' => 'Agribisnis', 'description' => 'Tanaman, hasil kebun, pangan lokal, dan kebutuhan pertanian rumahan.'],
            ['name' => 'Properti/Rumah', 'description' => 'Kontrakan, renovasi, perbaikan rumah, dan kebutuhan properti.'],
            ['name' => 'Event & Catering', 'description' => 'Katering, dekorasi, dokumentasi, dan kebutuhan acara.'],
            ['name' => 'Anak & Bayi', 'description' => 'Perlengkapan anak, mainan, dan kebutuhan bayi.'],
        ])->mapWithKeys(function (array $category, int $index) {
            $model = Category::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );

            return [$category['name'] => $model];
        });

        $umkmRows = [
            [
                'name' => 'Dapur Ibu Sari',
                'category' => 'Kuliner',
                'owner' => 'Sari Mulyani',
                'email' => 'sari@cimuning.test',
                'description' => 'Aneka nasi box, kue basah, dan pesanan harian untuk warga sekitar Cimuning.',
                'rw' => 'RW 03',
                'address' => 'Jl. Cimuning Raya, Cimuning, Mustikajaya, Kota Bekasi',
                'whatsapp' => '081234567890',
                'services' => ['delivery' => true, 'cod' => true, 'custom' => true, 'store' => false],
                'featured' => true,
                'products' => [
                    ['name' => 'Nasi Box Rumahan', 'description' => 'Paket nasi box untuk acara keluarga dan kantor.', 'price' => 25000],
                    ['name' => 'Kue Basah Campur', 'description' => 'Aneka kue basah untuk arisan dan rapat warga.', 'price' => 3500],
                ],
            ],
            [
                'name' => 'Bengkel Berkah Motor',
                'category' => 'Otomotif',
                'owner' => 'Ahmad Fauzi',
                'email' => 'berkahmotor@cimuning.test',
                'description' => 'Servis ringan, ganti oli, dan perawatan motor dengan layanan cepat dan ramah.',
                'rw' => 'RW 06',
                'address' => 'Jl. Raya Mustikajaya, Cimuning, Kota Bekasi',
                'whatsapp' => '082112345678',
                'services' => ['delivery' => false, 'cod' => false, 'custom' => false, 'store' => true],
                'featured' => true,
                'products' => [
                    ['name' => 'Servis Ringan Motor', 'description' => 'Pemeriksaan rutin, setelan rem, rantai, dan kelistrikan ringan.', 'price' => 45000],
                    ['name' => 'Ganti Oli Motor', 'description' => 'Layanan ganti oli dengan pilihan oli harian.', 'price' => 55000],
                ],
            ],
            [
                'name' => 'Kriya Cimuning',
                'category' => 'Produk Kreatif',
                'owner' => 'Maya Lestari',
                'email' => 'kriya@cimuning.test',
                'description' => 'Kerajinan lokal, souvenir custom, dan hampers untuk acara keluarga maupun komunitas.',
                'rw' => 'RW 01',
                'address' => 'Cimuning, Mustikajaya, Kota Bekasi',
                'whatsapp' => '085712345678',
                'services' => ['delivery' => true, 'cod' => true, 'custom' => true, 'store' => false],
                'featured' => true,
                'products' => [
                    ['name' => 'Hampers Custom', 'description' => 'Paket hampers custom untuk acara dan hadiah keluarga.', 'price' => 85000],
                    ['name' => 'Souvenir Rajut', 'description' => 'Souvenir kecil handmade untuk event komunitas.', 'price' => 15000],
                ],
            ],
            [
                'name' => 'Warung Makmur Cimuning',
                'category' => 'Toko Harian',
                'owner' => 'Rudi Hartono',
                'email' => 'warungmakmur@cimuning.test',
                'description' => 'Warung kebutuhan harian, sembako, minuman dingin, dan perlengkapan rumah.',
                'rw' => 'RW 04',
                'address' => 'Perumahan Cimuning Indah, Kota Bekasi',
                'whatsapp' => '087812345678',
                'services' => ['delivery' => true, 'cod' => true, 'custom' => false, 'store' => true],
                'featured' => false,
                'products' => [
                    ['name' => 'Paket Sembako Hemat', 'description' => 'Paket beras, minyak, gula, dan kebutuhan dapur.', 'price' => 120000],
                ],
            ],
        ];

        foreach ($umkmRows as $row) {
            $owner = User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['owner'],
                    'password' => Hash::make('password'),
                    'role' => 'umkm_owner',
                ],
            );

            $umkm = Umkm::query()->updateOrCreate(
                ['slug' => Str::slug($row['name'])],
                [
                    'user_id' => $owner->id,
                    'category_id' => $categories[$row['category']]->id,
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'owner_name' => $row['owner'],
                    'phone' => $row['whatsapp'],
                    'whatsapp' => $row['whatsapp'],
                    'email' => $row['email'],
                    'address' => $row['address'],
                    'rw' => $row['rw'],
                    'status' => 'verified',
                    'is_featured' => $row['featured'],
                    'is_active' => true,
                    'service_delivery' => $row['services']['delivery'],
                    'service_cod' => $row['services']['cod'],
                    'service_custom_order' => $row['services']['custom'],
                    'has_physical_store' => $row['services']['store'],
                ],
            );

            $umkm->contacts()->updateOrCreate(
                ['type' => 'whatsapp', 'value' => $row['whatsapp']],
                ['label' => 'WhatsApp utama', 'is_primary' => true],
            );

            $umkm->socialLinks()->updateOrCreate(
                ['platform' => 'instagram'],
                ['url' => 'https://instagram.com/'.Str::slug($row['name'], ''), 'sort_order' => 1],
            );

            foreach ($row['products'] as $productRow) {
                Product::query()->updateOrCreate(
                    ['slug' => Str::slug($productRow['name'])],
                    [
                        'umkm_id' => $umkm->id,
                        'category_id' => $categories[$row['category']]->id,
                        'name' => $productRow['name'],
                        'description' => $productRow['description'],
                        'price' => $productRow['price'],
                        'is_active' => true,
                    ],
                );
            }
        }

        $admin->update(['role' => 'admin']);
    }
}
