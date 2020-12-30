<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Section;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $section = Section::create([
            'name' => 'default section'
        ]);

        DB::table('employees')->insert([
            'f_name' => 'Mahmoud',
            'l_name' => 'Tarik',
            'email' => 'mt.alshahat@gmail.com',
            'password' => Hash::make('RandomPassword123!'),
            'section_id' => $section->id,
            'region' => 'العاصمة',
            'city' => 'الجامعة',
            'town' => 'شفا بدران',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
