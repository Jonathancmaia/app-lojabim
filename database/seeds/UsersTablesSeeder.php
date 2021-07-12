<?php

use Illuminate\Database\Seeder;

class UsersTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Jonathan Maia',
            'email' => 'jonathancmaia'.'@gmail.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
