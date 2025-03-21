<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Command;
use App\Models\User;
use App\Models\Burger;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CommandSeeder extends Seeder
{
    public function run()
    {
        // Get some users and burgers for seeding
        $users = User::where('role', 'client')->get();
        $burgers = Burger::all();

        if ($users->isEmpty() || $burgers->isEmpty()) {
            $this->command->info('Please seed users and burgers first!');
            return;
        }

        // Create 10 sample commands
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $totalAmount = 0;
            $burgerCount = rand(1, 3); // Random number of burgers per command

            // Create a payment first
            $payment = Payment::create([
                'type' => fake()->randomElement(['cash', 'card', 'mobile']),
                'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
            ]);

            // Create the command
            $command = Command::create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'total' => '0', // Will be updated after adding burgers
                'statut' => fake()->randomElement(['en attente', 'en préparation', 'prête', 'payée']),
                'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
            ]);

            // Add random burgers to the command
            for ($j = 0; $j < $burgerCount; $j++) {
                $burger = $burgers->random();
                $quantity = rand(1, 3);
                
                $command->burgers()->attach($burger->id, [
                    'quantity' => $quantity,
                    'created_at' => $command->created_at,
                    'updated_at' => $command->updated_at,
                ]);

                $totalAmount += $burger->price * $quantity;
            }

            // Update the total amount
            $command->update(['total' => (string)$totalAmount]);
        }
    }
} 