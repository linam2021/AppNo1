<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections = [
            "الجمعيات"
            ,"الأسرة والحماية"
            ,"سجل الجمعيات",
            "الأشخاص ذوي الإعاقة ومراكزها"
            ,"المكارم الملكية"
            ,"الأحداث والأمن المجتمعي"
            ,"سوء تصرف موظف/ مساعدات"
            ,"التطوير المؤسسي"
            ,"عدم اختصاص"
            ,"تعزيز الإنتاجية"
            ,"وحدة التسول"
            ,"الموارد البشرية تعيينات"
            ,"المساعدات"
            ,"متفرقات"
            ,"المساكن"];

        foreach($sections as $section)
        {
            Section::create([
                'name' => $section
            ]);
        }
    }
}
