<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = [
            'Synergy Group',
            'Synergy Dentsu',
            'Synite Digital',
            'Syntax Communications',
            'Synergy Marketing',
            'Synchronize Media',
            'Syntinel',
            'BrandSynario',
            'Synergyzer',
            'AK Work',
            'LKMWT',
        ];

        foreach ($companies as $name) {
            Company::create([
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }
}
