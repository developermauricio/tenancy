<?php

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
        // $this->call(UserSeeder::class);
        factory(\App\User::class, 1)->create([
            "name" => "Admin",
            "email" => "admin@admin.co",
            "role" => "admin",
            "password" => bcrypt("password")
        ]);

        factory(\App\User::class, 1)->create([
            "name" => "Cliente Inquilino",
            "email" => "app@cliente.co",
            "role" => "tenancy",
            "password" => bcrypt("password")
        ]);
    }
}
