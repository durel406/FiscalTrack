<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@fiscaltrack.test'],
            [
                'name' => 'Administrateur Demo',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'identifiant' => 'ADMIN001',
            ]
        );

        User::updateOrCreate(
            ['email' => 'comptable@fiscaltrack.test'],
            [
                'name' => 'Comptable Demo',
                'password' => Hash::make('password'),
                'role' => 'comptable',
                'status' => 'active',
                'identifiant' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'fiscal@fiscaltrack.test'],
            [
                'name' => 'Responsable Fiscal Demo',
                'password' => Hash::make('password'),
                'role' => 'responsable_fiscal',
                'status' => 'active',
                'identifiant' => null,
            ]
        );
    }
}
