<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        Role::create(['name' => 'admin',    'guard_name' => 'web']);
        Role::create(['name' => 'operador', 'guard_name' => 'web']);
        Role::create(['name' => 'usuario',  'guard_name' => 'web']);

        // Admin
        $admin = User::create([
            'cedula'              => '1300000001',
            'name'                => 'Administrador ULEAM',
            'email'               => 'admin@uleam.edu.ec',
            'password'            => bcrypt('123456789'),
            'password_changed_at' => now(),
            'activo'              => true,
        ]);
        $admin->forceFill(['email_verified_at' => now()])->save();
        $admin->assignRole('admin');

        // Operador
        $operador = User::create([
            'cedula'              => '1300000002',
            'name'                => 'Operador Puerto',
            'email'               => 'operador@uleam.edu.ec',
            'password'            => bcrypt('123456789'),
            'password_changed_at' => now(),
            'activo'              => true,
        ]);
        $operador->forceFill(['email_verified_at' => now()])->save();
        $operador->assignRole('operador');

        // Estudiante
        $usuario = User::create([
            'cedula'              => '1316764974',
            'name'                => 'Estudiante Test',
            'email'               => 'mosquerabdp23@gmail.com',
            'password'            => bcrypt('123456789'),
            'password_changed_at' => now(),
            'activo'              => true,
        ]);
        $usuario->forceFill(['email_verified_at' => now()])->save();
        $usuario->assignRole('usuario');

        // Embarcaciones
        $this->call(EmbarcacionSeeder::class);
    }
}