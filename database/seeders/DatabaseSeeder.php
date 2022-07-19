<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingType;
use App\Models\Stock;
use App\Models\Variation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'id'   => 1,
                'name' => 'Brands',
                'slug' => 'brands',
                'parent_id' => null,
            ],
            [
                'id'   => 2,
                'name' => 'Nike',
                'slug' => 'nike',
                'parent_id' => 1,
            ],
            [
                'id'   => 3,
                'name' => 'Adidas',
                'slug' => 'adidas',
                'parent_id' => 1,
            ],
            [
                'id'   => 4,
                'name' => 'Seasons',
                'slug' => 'seasons',
                'parent_id' => null,
            ],
            [
                'id'   => 5,
                'name' => 'Summer',
                'slug' => 'summer',
                'parent_id' => 4,
            ],
            [
                'id'   => 6,
                'name' => 'Winter',
                'slug' => 'winter',
                'parent_id' => 4,
            ],
        ];
        Category::insert($categories);

        $products = [
            [
                'name' => 'Nike Air Force 1',
                'slug' => 'nike-air-force-1',
                'description' => 'This is a example product',
                'price' => 7500,
                'live_at' => now(),
            ],
        ];
        Product::insert($products);

        $product1 = Product::find(1);
        $product1->categories()->attach(2);

        $shipping_types = [
            [
                'name' => 'UPS Free',
                'price' => 0,
            ],
            [
                'name' => 'UPS Standart',
                'price' => 2000,
            ],
        ];
        ShippingType::insert($shipping_types);

        $variations = [
            [
                'product_id' => 1,
                'name' => 'Black',
                'price' => 7500,
                'type' => 'color',
                'sku' => 'BLACK',
            ],
        ];
        Variation::insert($variations);

        $stocks = [
            [
                'variation_id' => 1,
                'amount' => 20,
            ],
        ];
        Stock::insert($stocks);

         \App\Models\User::factory()->create([
             'name' => 'Super Admin',
             'email' => 'admin@admin.com',
         ]);
    }
}
