<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nombre',
        'email',
        'password',
        'rol',
        'telefono',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function citas()
    {
        return $this->hasMany(Cita::class, 'usuario_id');
    }

    // Scopes
    public function scopeClientes($query)
    {
        return $query->where('rol', 'cliente');
    }

    public function scopeAdmins($query)
    {
        return $query->where('rol', 'admin');
    }

    // Helpers
    public function isAdmin()
    {
        return $this->rol === 'admin';
    }

    public function isCliente()
    {
        return $this->rol === 'cliente';
    }
}
