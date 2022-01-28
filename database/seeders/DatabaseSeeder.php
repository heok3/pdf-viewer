<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pdf_files')->insert([
            [
                'original_file_name' => 'helloworld',
                'unique_file_name' => '5485dbe8-1aef-4841-861b-20a1c8eecf60',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'original_file_name' => 'random',
                'unique_file_name' => '0874985b-45d1-4767-afad-20f86f05673c',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'original_file_name' => 'resume',
                'unique_file_name' => '8db98674-84b8-497e-b360-b5fa53777c6e',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
