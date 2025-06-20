<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\CitaCreated;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'fecha',
        'hora',
        'descripcion',
        'estado',
        'tipo_servicio',
        'vehiculo_marca',
        'vehiculo_modelo',
        'vehiculo_placa',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i',
    ];

    protected $dispatchesEvents = [
        'created' => CitaCreated::class,
    ];

    protected $table = 'appointments';

    // Relationships
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeByFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    public function scopeByUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // Helpers
    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function isConfirmada()
    {
        return $this->estado === 'confirmada';
    }

    public function isCancelada()
    {
        return $this->estado === 'cancelada';
    }

    public function getFechaFormateadaAttribute()
    {
        return $this->fecha->format('d/m/Y');
    }

    public function getHoraFormateadaAttribute()
    {
        return $this->hora->format('H:i');
    }
}
