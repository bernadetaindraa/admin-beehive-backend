<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductService;

class ProductServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'Drone Technology',
            'Agriculture Monitoring',
            'Mapping & Survey',
            'AI Analytics',
            'Security Surveillance',
            'Training & Consultation',
        ];

        foreach ($services as $service) {
            ProductService::create(['name' => $service]);
        }
    }
}
