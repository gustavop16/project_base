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
            'vessel.viewAny', 'vessel.view', 'vessel.create', 'vessel.update', 'vessel.delete',
            //questões
            'question.viewAny', 'question.view', 'question.create', 'question.update', 'question.delete',
            //formulários de certificado
            'certificate_form.viewAny', 'certificate_form.view', 'certificate_form.create', 'certificate_form.update', 'certificate_form.delete',
            //certificates
            'certificates.viewAny', 'certificates.view', 'certificates.create', 'certificates.delete', 'certificates.approve',
            'form_response.viewAny', 'form_response.view', 'form_response.create',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin — acesso total
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());
        
        $nonAdminPermissions = [
            'vessel.viewAny', 'vessel.view', 'vessel.create', 'vessel.update', 'vessel.delete',
            'certificate_form.viewAny', 'certificate_form.view',
            'certificates.viewAny', 'certificates.view', 'certificates.create',
            'form_response.viewAny', 'form_response.view', 'form_response.create',
        ];

        // Agent
        $agent = Role::firstOrCreate(['name' => 'agent']);
        $agent->syncPermissions($nonAdminPermissions);

        // Shipowner
        $shipowner = Role::firstOrCreate(['name' => 'shipowner']);
        $shipowner->syncPermissions($nonAdminPermissions);
        

        /*
        // Customer — visualiza dados do próprio cliente
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->syncPermissions([
            'attachments.viewAny', 'attachments.view',
        ]);
        */
    }
}
