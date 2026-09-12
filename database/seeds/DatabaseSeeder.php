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
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'identifiant' => 'ADMIN001',
            ]
        );

        User::updateOrCreate(
            ['email' => 'comptable@fiscaltrack.test'],
            [
                'name' => 'Comptable',
                'password' => Hash::make('password'),
                'role' => 'comptable',
                'identifiant' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'fiscal@fiscaltrack.test'],
            [
                'name' => 'Responsable fiscal',
                'password' => Hash::make('password'),
                'role' => 'responsable_fiscal',
                'identifiant' => null,
            ]
        );
    }
}
