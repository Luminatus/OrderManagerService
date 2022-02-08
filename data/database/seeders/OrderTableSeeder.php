<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 500) as $idx) {
            Order::factory()
                ->for(Address::all()->random(), 'billingAddress')
                ->for(Address::all()->random(), 'shippingAddress')
                ->has(
                    OrderItem::factory()
                        ->count(mt_rand(1, 10))
                        ->state(new Sequence(fn ($sequence) => ['product_id' => Product::all()->random()])))
                ->create();
        }
    }
}
