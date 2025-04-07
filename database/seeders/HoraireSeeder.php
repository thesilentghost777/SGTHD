<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HoraireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Assuming there are existing users in the 'users' table
        $userIds = DB::table('users')->pluck('id')->toArray();

        for ($i = 0; $i < 1000; $i++) {
            $arrive = $faker->dateTimeBetween('2025-01-20 02:00:00', '2025-01-30 23:00:00');
            $depart = (clone $arrive)->modify('+7 hours');

            // Ensure 'depart' does not exceed 23:00
            if ((int)$depart->format('H') > 23) {
                $depart = $faker->dateTimeBetween('2025-01-20 02:00:00', '2025-01-30 23:00:00');
                $arrive = (clone $depart)->modify('-7 hours');
            }

            DB::table('Horaire')->insert([
                'employe' => $faker->randomElement($userIds),
                'arrive' => $arrive,
                'depart' => $depart,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
