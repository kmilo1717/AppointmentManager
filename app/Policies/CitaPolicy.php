<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    public function view(User $user, Cita $cita)
    {
        return $user->isAdmin() || $user->id === $cita->usuario_id;
    }

    public function create(User $user)
    {
        return true; // Cualquier usuario autenticado puede crear citas
    }

    public function update(User $user, Cita $cita)
    {
        return $user->isAdmin() || $user->id === $cita->usuario_id;
    }

    public function delete(User $user, Cita $cita)
    {
        return $user->isAdmin();
    }

    public function cancel(User $user, Cita $cita)
    {
        return ($user->isAdmin() || $user->id === $cita->usuario_id) && 
               $cita->estado !== 'cancelada';
    }
}
