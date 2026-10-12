<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesWithMenus = [
            [
                'name' => 'Coffee',
                'slug' => 'coffee',
                'menus' => [
                    [
                        'name' => 'Kopi Susu Gula Aren',
                        'description' => 'Espresso house blend dipadu dengan susu segar dan manisnya gula aren murni.',
                        'price' => 18000,
                        'variants' => [
                            ['name' => 'Less Sugar', 'extra_price' => 0],
                            ['name' => 'Extra Shot', 'extra_price' => 4000],
                            ['name' => 'Oat Milk Sub', 'extra_price' => 6000],
                        ],
                    ],
                    [
                        'name' => 'Americano',
                        'description' => 'Double shot espresso dengan air mineral pilihan, disajikan panas atau dingin.',
                        'price' => 15000,
                        'variants' => [
                            ['name' => 'Hot', 'extra_price' => 0],
                            ['name' => 'Iced', 'extra_price' => 0],
                            ['name' => 'Extra Shot', 'extra_price' => 4000],
                        ],
                    ],
                    [
                        'name' => 'Cafe Latte',
                        'description' => 'Kombinasi lembut espresso dengan steamed milk berbusa halus.',
                        'price' => 20000,
                        'variants' => [
                            ['name' => 'Vanilla Syrup', 'extra_price' => 3000],
                            ['name' => 'Caramel Syrup', 'extra_price' => 3000],
                            ['name' => 'Hazelnut Syrup', 'extra_price' => 3000],
                        ],
                    ],
                    [
                        'name' => 'Caramel Macchiato',
                        'description' => 'Espresso kaya rasa berpadu saus karamel lezat dan vanilla milk.',
                        'price' => 24000,
                        'variants' => [
                            ['name' => 'Extra Caramel Drizzle', 'extra_price' => 3000],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Non-Coffee',
                'slug' => 'non-coffee',
                'menus' => [
                    [
                        'name' => 'Signature Chocolate',
                        'description' => 'Cokelat Belgia pekat dengan susu segar premium.',
                        'price' => 20000,
                        'variants' => [
                            ['name' => 'Less Sweet', 'extra_price' => 0],
                            ['name' => 'Marshmallow Topping', 'extra_price' => 3000],
                        ],
                    ],
                    [
                        'name' => 'Matcha Latte',
                        'description' => 'Teh hijau Jepang asli beraroma harum dipadukan susu creamy.',
                        'price' => 22000,
                        'variants' => [
                            ['name' => 'Less Sweet', 'extra_price' => 0],
                            ['name' => 'Oat Milk Sub', 'extra_price' => 6000],
                        ],
                    ],
                    [
                        'name' => 'Lychee Tea',
                        'description' => 'Teh segar dengan rasa buah leci manis dan buah leci utuh.',
                        'price' => 16000,
                        'variants' => [],
                    ],
                ],
            ],
            [
                'name' => 'Snack',
                'slug' => 'snack',
                'menus' => [
                    [
                        'name' => 'French Fries Crispy',
                        'description' => 'Kentang goreng renyah disajikan dengan saus sambal dan mayones.',
                        'price' => 15000,
                        'variants' => [
                            ['name' => 'Bumbu Balado', 'extra_price' => 2000],
                            ['name' => 'Bumbu Keju', 'extra_price' => 2000],
                        ],
                    ],
                    [
                        'name' => 'Roti Bakar Coklat Keju',
                        'description' => 'Roti panggang mentega dengan taburan coklat meses dan keju cheddar berlimpah.',
                        'price' => 18000,
                        'variants' => [],
                    ],
                    [
                        'name' => 'Cireng Crispy Bumbu Rujak',
                        'description' => 'Cireng kenyal gurih dengan cocolan saus rujak pedas manis.',
                        'price' => 14000,
                        'variants' => [],
                    ],
                ],
            ],
            [
                'name' => 'Main Course',
                'slug' => 'main-course',
                'menus' => [
                    [
                        'name' => 'Nasi Goreng Spesial Brader',
                        'description' => 'Nasi goreng racikan bumbu khas Brader dengan telur mata sapi, ayam suwir, dan kerupuk.',
                        'price' => 25000,
                        'variants' => [
                            ['name' => 'Pedas Sedang', 'extra_price' => 0],
                            ['name' => 'Pedas Banget', 'extra_price' => 0],
                            ['name' => 'Tambah Telur', 'extra_price' => 4000],
                        ],
                    ],
                    [
                        'name' => 'Chicken Rice Bowl Teriyaki',
                        'description' => 'Nasi hangat dengan potongan ayam renyah dilumuri saus teriyaki gurih manis.',
                        'price' => 27000,
                        'variants' => [],
                    ],
                ],
            ],
        ];

        foreach ($categoriesWithMenus as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                ['name' => $categoryData['name']]
            );

            foreach ($categoryData['menus'] as $menuData) {
                $menu = Menu::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $menuData['name'],
                    ],
                    [
                        'description' => $menuData['description'],
                        'price' => $menuData['price'],
                        'image' => null,
                        'is_available' => true,
                    ]
                );

                if (! empty($menuData['variants'])) {
                    foreach ($menuData['variants'] as $variantData) {
                        MenuVariant::firstOrCreate(
                            [
                                'menu_id' => $menu->id,
                                'name' => $variantData['name'],
                            ],
                            [
                                'extra_price' => $variantData['extra_price'],
                            ]
                        );
                    }
                }
            }
        }
    }
}
