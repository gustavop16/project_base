<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrador',
                'email'    => 'admin@aquasat.com',
                'password' => Hash::make('password'),
                'type'     => 'admin',
            ],
            [
                'name'     => 'agent',
                'email'    => 'agent@aquasat.com',
                'password' => Hash::make('password'),
                'type'     => 'agent',
            ],
            [
                'name'     => 'shipowner',
                'email'    => 'navio@aquasat.com',
                'password' => Hash::make('password'),
                'type'     => 'shipowner',
            ],
            /*
            [
                'name'     => 'Cliente',
                'email'    => 'customer@aquasat.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
            ],*/
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], $data);
            $user->syncRoles([$data['type']]);
        }
    }
}
