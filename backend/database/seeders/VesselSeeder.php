<?php

namespace Database\Seeders;

use App\Models\Vessel;
use Illuminate\Database\Seeder;

class VesselSeeder extends Seeder
{
    public function run(): void
    {
        $vessels = [
            [
                'name'             => 'Rio Amazonas',
                'call_sign'        => 'PPRA1',
                'port_of_registry' => 'Santos - SP',
                'gross_tonnage'    => 12540.75,
                'built_at'         => '2008-03-15',
                'imo_number'       => '9412305',
                'active'           => true,
            ],
            [
                'name'             => 'Estrela do Sul',
                'call_sign'        => 'PPSE2',
                'port_of_registry' => 'Rio de Janeiro - RJ',
                'gross_tonnage'    => 8320.50,
                'built_at'         => '2013-07-22',
                'imo_number'       => '9587641',
                'active'           => true,
            ],
            [
                'name'             => 'Nordeste Pioneer',
                'call_sign'        => 'PPNP3',
                'port_of_registry' => 'Fortaleza - CE',
                'gross_tonnage'    => 21080.00,
                'built_at'         => '2017-11-09',
                'imo_number'       => '9734892',
                'active'           => true,
            ],
        ];

        foreach ($vessels as $data) {
            Vessel::firstOrCreate(
                ['imo_number' => $data['imo_number']],
                $data
            );
        }
    }
}
