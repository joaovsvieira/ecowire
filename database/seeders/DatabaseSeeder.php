<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
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
                'name' => 'Product 1',
                'slug' => 'product-1',
                'description' => 'This is a example product',
                'price' => 100,
            ],
        ];
        Product::insert($products);

         \App\Models\User::factory()->create([
             'name' => 'Super Admin',
             'email' => 'admin@admin.com',
         ]);
    }
}
