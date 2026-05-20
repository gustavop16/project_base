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
                'email'    => 'admin@oceansafer.com',
                'password' => Hash::make('password'),
                'type'     => 'admin',
            ],
           /* [
                'name'     => 'Gerente',
                'email'    => 'manager@oceansafer.com',
                'password' => Hash::make('password'),
                'role'     => 'manager',
            ],
            [
                'name'     => 'Técnico',
                'email'    => 'technician@oceansafer.com',
                'password' => Hash::make('password'),
                'role'     => 'technician',
            ],
            [
                'name'     => 'Cliente',
                'email'    => 'customer@oceansafer.com',
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
