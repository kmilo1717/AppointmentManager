<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cita;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'nombre' => 'Administrador del Sistema',
            'email' => 'admin@taller.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'telefono' => '123456789',
            'email_verified_at' => now(),
        ]);

        $clientes = [
            [
                'name' => 'Juan Pérez',
                'nombre' => 'Juan Carlos Pérez González',
                'email' => 'juan@email.com',
                'password' => Hash::make('password'),
                'rol' => 'cliente',
                'telefono' => '987654321',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'María García',
                'nombre' => 'María Elena García López',
                'email' => 'maria@email.com',
                'password' => Hash::make('password'),
                'rol' => 'cliente',
                'telefono' => '456789123',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos López',
                'nombre' => 'Carlos Alberto López Martínez',
                'email' => 'carlos@email.com',
                'password' => Hash::make('password'),
                'rol' => 'cliente',
                'telefono' => '789123456',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($clientes as $cliente) {
            User::create($cliente);
        }

        // Crear citas de ejemplo
        $citas = [
            [
                'usuario_id' => 2,
                'fecha' => now()->addDays(1)->format('Y-m-d'),
                'hora' => '09:00:00',
                'descripcion' => 'Cambio de aceite y filtros',
                'estado' => 'pendiente',
                'tipo_servicio' => 'Mantenimiento',
                'vehiculo_marca' => 'Toyota',
                'vehiculo_modelo' => 'Corolla',
                'vehiculo_placa' => 'ABC-123'
            ],
            [
                'usuario_id' => 3,
                'fecha' => now()->addDays(2)->format('Y-m-d'),
                'hora' => '10:30:00',
                'descripcion' => 'Revisión de frenos',
                'estado' => 'confirmada',
                'tipo_servicio' => 'Reparación',
                'vehiculo_marca' => 'Honda',
                'vehiculo_modelo' => 'Civic',
                'vehiculo_placa' => 'XYZ-789'
            ],
            [
                'usuario_id' => 4,
                'fecha' => now()->addDays(3)->format('Y-m-d'),
                'hora' => '14:00:00',
                'descripcion' => 'Diagnóstico general del motor',
                'estado' => 'pendiente',
                'tipo_servicio' => 'Diagnóstico',
                'vehiculo_marca' => 'Nissan',
                'vehiculo_modelo' => 'Sentra',
                'vehiculo_placa' => 'DEF-456'
            ]
        ];

        foreach ($citas as $cita) {
            Cita::create($cita);
        }
    }
}
