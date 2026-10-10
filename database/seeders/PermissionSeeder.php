<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private const RESOURCES = [
        'users',
        'customer-profiles',
        'customer-addresses',
        'tax-profiles',
        'designs',
        'sticker-sizes',
        'sticker-finishes',
        'design-variants',
        'orders',
        'order-items',
        'order-status-history',
        'payments',
        'design-requests',
        'order-invoice-requests',
    ];

    /**
     * @var list<string>
     */
    private const ACTIONS = [
        'view',
        'create',
        'update',
        'delete',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS as $action) {
                Permission::findOrCreate("{$resource}.{$action}", 'web');
            }
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(
            Permission::query()
                ->where('guard_name', 'web')
                ->get(),
        );

        Role::findOrCreate('super-admin', 'web');
        Role::findOrCreate('vendedor', 'web');
        Role::findOrCreate('cliente', 'web');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
