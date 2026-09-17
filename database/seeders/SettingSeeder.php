<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'deadline_arsip'], ['value' => '3']);
        Setting::updateOrCreate(['key' => 'deadline_seksi1'], ['value' => '3']);
        Setting::updateOrCreate(['key' => 'deadline_seksi2'], ['value' => '3']);
    }
}
