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
        //assigning one employee to section
        for ($i = 1 ; $i <= 15 ; $i++)
        {
            DB::table('employees')->insert([
                'f_name' => 'admin',
                'l_name' => 'admin',
                'email' => 'admin'.$i.'@admin.com',
                'password' => Hash::make('RandomPassword123!'),
                'section_id' => $i,
                'governorate' => 'العاصمة',
                'district' => 'الجامعة',
                'city' => 'شفا بدران',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
