<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		 $this->call(JudgeStatuses::class);
		 $this->call(Usertypes::class);
        // $this->call(UsersTableSeeder::class);
    }
}
