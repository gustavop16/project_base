<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Usuários
            'users.viewAny', 'users.view', 'users.create', 'users.update', 'users.delete',
            // Anexos
            'attachments.viewAny', 'attachments.view', 'attachments.create', 'attachments.delete',
            //navios
            'ships.viewAny', 'ships.view', 'ships.create', 'ships.delete',
            //parametros
            'certified_parameters.viewAny', 'certified_parameters.view', 'certified_parameters.create', 'certified_parameters.delete',
            //templapes 
            'certified_template.viewAny', 'certified_template.view', 'certified_template.create', 'certified_template.delete',
            //certificates
            'certificates.viewAny', 'certificates.view', 'certificates.create',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin — acesso total
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());
        
        // Agent 
        $agent = Role::firstOrCreate(['name' => 'agent']);
        $agent->syncPermissions([
            'certificates.viewAny', 'certificates.view', 'certificates.create',
        ]);

        
        // SHIPOWNER 
        $shipowner = Role::firstOrCreate(['name' => 'shipowner']);
        $shipowner->syncPermissions([
            'certificates.viewAny', 'certificates.view', 'certificates.create',
        ]);
        

        /*
        // Customer — visualiza dados do próprio cliente
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->syncPermissions([
            'attachments.viewAny', 'attachments.view',
        ]);
        */
    }
}
