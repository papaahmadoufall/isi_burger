<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BurgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('burgers')->insert([
            [
                'name' => 'Classic Burger',
                'price' => 9.99,
                'stock' => 50,
                'description' => 'A simple and delicious classic burger.',
                'image' => 'images/classic_burger.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cheese Burger',
                'price' => 11.99,
                'stock' => 40,
                'description' => 'A burger loaded with melted cheese.',
                'image' => 'images/cheese_burger.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chicken Burger',
                'price' => 12.49,
                'stock' => 30,
                'description' => 'A crispy chicken burger with lettuce and mayo.',
                'image' => 'images/chicken_burger.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Veggie Burger',
                'price' => 8.99,
                'stock' => 20,
                'description' => 'A delicious vegetarian burger with fresh vegetables.',
                'image' => 'images/veggie_burger.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deluxe Burger',
                'price' => 14.99,
                'stock' => 15,
                'description' => 'A double-patty burger with special gourmet toppings.',
                'image' => 'images/deluxe_burger.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
