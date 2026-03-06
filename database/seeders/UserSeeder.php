<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Direccion; 
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@creditosolidario.com',
            'password' => Hash::make('password123'),
        ]);

        $userCliente = User::create([
            'name' => 'María López',
            'email' => 'maria@ejemplo.com',
            'password' => Hash::make('12345678'), 
        ]);

        // Crear su dirección
        $direccion = Direccion::create([
            'calle' => 'Avenida de la Solidaridad',
            'numCasa' => '15, 2ºB',
            'municipio' => 'Sevilla',
            'provincia' => 'Sevilla',
        ]);

        Cliente::create([
            'nombre' => 'María',
            'apellidos' => 'López García',
            'dni' => '12345678Z',
            'telefono' => '600123456',
            'email' => 'maria@ejemplo.com',
            'saldo' => 5000, 
            'activo' => true,
            'user_id' => $userCliente->id,
            'direccion_id' => $direccion->id,
        ]);
    }
}
