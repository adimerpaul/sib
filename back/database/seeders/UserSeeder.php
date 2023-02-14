<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123Admin'),
        ]);
        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'socio']);
        Role::create(['name' => 'contador']);
        Role::create(['name' => 'rrhh']);
        Role::create(['name' => 'planilla']);
        Role::create(['name' => 'cajero']);
        $user->assignRole('administrador');
        $user->assignRole('socio');
        $user->assignRole('contador');
        $user->assignRole('rrhh');
        $user->assignRole('planilla');
        $user->assignRole('cajero');
    }
}
