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
        for ($i = 0 ; $i <= 15 ; $i++)
        {
            DB::table('employees')->insert([
                'f_name' => 'admin',
                'l_name' => 'admin',
                'email' => 'admin'.$i.'@admin.com',
                'password' => Hash::make('RandomPassword123!'),
                'section_id' => 1, //جمعيات
                'governorate' => 'العاصمة',
                'district' => 'الجامعة',
                'city' => 'شفا بدران',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
