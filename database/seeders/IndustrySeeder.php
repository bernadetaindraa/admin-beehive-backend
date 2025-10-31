<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Industry;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            'Agriculture',
            'Forestry',
            'Mining',
            'Construction',
            'Logistics',
            'Defense & Security',
        ];

        foreach ($industries as $industry) {
            Industry::create(['name' => $industry]);
        }
    }
}
