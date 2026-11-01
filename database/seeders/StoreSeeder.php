<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name'    => 'Warung Makan Bu Yati',
                'address' => 'Jl. Pahlawan No. 12, Kota Faster',
                'phone'   => '08121234001',
                'is_open' => true,
                'menus'   => [
                    ['name' => 'Nasi Goreng Spesial',    'price' => 18000, 'description' => 'Nasi goreng dengan telur, ayam, dan sayuran segar.'],
                    ['name' => 'Mie Goreng Pedas',        'price' => 15000, 'description' => 'Mie goreng dengan cabe rawit dan topping bakso.'],
                    ['name' => 'Ayam Bakar Kecap',        'price' => 25000, 'description' => 'Ayam bakar dengan bumbu kecap manis khas rumahan.'],
                    ['name' => 'Es Teh Manis',            'price' =>  5000, 'description' => 'Teh manis dingin segar.'],
                    ['name' => 'Jus Jeruk',               'price' =>  8000, 'description' => 'Jus jeruk segar tanpa pengawet.'],
                ],
            ],
            [
                'name'    => 'Bakso & Soto Pak Bejo',
                'address' => 'Jl. Merdeka No. 45, Kota Faster',
                'phone'   => '08121234002',
                'is_open' => true,
                'menus'   => [
                    ['name' => 'Bakso Urat',              'price' => 20000, 'description' => 'Bakso urat sapi asli dengan kuah gurih.'],
                    ['name' => 'Soto Ayam Kampung',       'price' => 18000, 'description' => 'Soto ayam kampung dengan kuah bening.'],
                    ['name' => 'Bakso Telur',             'price' => 22000, 'description' => 'Bakso berisi telur puyuh.'],
                    ['name' => 'Minuman Jeruk Hangat',    'price' =>  6000, 'description' => 'Jeruk hangat menyegarkan.'],
                ],
            ],
            [
                'name'    => 'Kedai Kopi & Snack "Santai"',
                'address' => 'Jl. Sudirman No. 88, Kota Faster',
                'phone'   => '08121234003',
                'is_open' => true,
                'menus'   => [
                    ['name' => 'Kopi Susu Gula Aren',    'price' => 15000, 'description' => 'Kopi susu dengan gula aren asli.'],
                    ['name' => 'Matcha Latte',            'price' => 18000, 'description' => 'Matcha latte creamy dengan susu segar.'],
                    ['name' => 'Roti Bakar Coklat Keju', 'price' => 12000, 'description' => 'Roti bakar tebal dengan topping coklat dan keju.'],
                    ['name' => 'Pisang Goreng Crispy',   'price' =>  8000, 'description' => 'Pisang goreng crispy dengan taburan keju.'],
                    ['name' => 'Es Coklat',              'price' => 12000, 'description' => 'Minuman coklat dingin dengan topping choco chips.'],
                ],
            ],
        ];

        foreach ($stores as $storeData) {
            $menus = $storeData['menus'];
            unset($storeData['menus']);

            $store = Store::updateOrCreate(
                ['name' => $storeData['name']],
                $storeData
            );

            foreach ($menus as $menuData) {
                Menu::updateOrCreate(
                    ['store_id' => $store->id, 'name' => $menuData['name']],
                    array_merge($menuData, ['store_id' => $store->id, 'is_available' => true])
                );
            }
        }

        $this->command->info('✅ 3 toko sample dan ' . (array_sum(array_map(fn($s) => count($s['menus']), $stores))) . ' menu berhasil dibuat.');
    }
}
