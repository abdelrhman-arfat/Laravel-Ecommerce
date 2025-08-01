<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(40)->create();
        ProductVariant::factory()->count(90)->create();
        Payment::factory()->count(100)->create();

        $admin = User::factory()->create([
            "name" => "Admin",
            "email" => "admin@example",
            "password" => Hash::make("admin123"),
            "role" => "admin"
        ]);
        User::factory()->count(10)->create();

        $adminOrder = Order::factory()->create([
            "user_id" => $admin->id
        ]);

        OrderItem::factory()->count(10)->create([
            "order_id" => $adminOrder->id
        ]);

        Order::factory()->count(20)->create();
        OrderItem::factory()->count(40)->create();
    }
}
