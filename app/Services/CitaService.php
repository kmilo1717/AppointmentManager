<?php

namespace App\Services;

use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Servicio responsable de encapsular la lógica de negocio relacionada con las citas.
 *
 * Proporciona métodos reutilizables para crear, actualizar, filtrar y obtener estadísticas de citas.
 *
 * @package App\Services
 */
class CitaService
{
    /**
     * Crea una nueva cita validando la entrada y asignando el usuario correspondiente.
     *
     * @param StoreCitaRequest $request
     * @return Cita
     */
    public function crearCita(StoreCitaRequest $request): Cita
    {
        $data = $request->validated();

        // Si no es admin, asigna automáticamente el usuario autenticado
        $data['usuario_id'] = auth()->user()->isAdmin()
            ? $data['usuario_id']
            : auth()->id();

        $data['estado'] = 'pendiente';

        return Cita::create($data);
    }

    /**
     * Actualiza una cita existente y valida que no haya conflictos con la fecha/hora.
     *
     * @param UpdateCitaRequest $request
     * @param Cita $cita
     * @return void
     */
    public function actualizarCita(UpdateCitaRequest $request, Cita $cita): void
    {
        $data = $request->validated();

        // Verifica disponibilidad si se cambia fecha u hora
        if (isset($data['fecha']) || isset($data['hora'])) {
            $fecha = $data['fecha'] ?? $cita->fecha;
            $hora = $data['hora'] ?? $cita->hora;

            $yaExiste = Cita::where('fecha', $fecha)
                ->where('hora', $hora)
                ->where('id', '!=', $cita->id)
                ->exists();

            if ($yaExiste) {
                abort(422, 'Ya existe una cita agendada para esa fecha y hora');
            }
        }

        $cita->update($data);
    }

    /**
     * Retorna las horas disponibles para una fecha, excluyendo la cita actual si aplica.
     *
     * @param string $fecha
     * @param string|null $citaId
     * @return array
     */
    public function obtenerHorasDisponibles(string $fecha, ?string $citaId = null): array
    {
        $disponibles = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
            '17:00', '17:30', '18:00'
        ];

        $ocupados = Cita::where('fecha', $fecha)
            ->when($citaId, fn($q) => $q->where('id', '!=', $citaId))
            ->pluck('hora')
            ->map(fn($hora) => Carbon::parse($hora)->format('H:i'))
            ->toArray();

        return array_values(array_diff($disponibles, $ocupados));
    }

    /**
     * Filtra y pagina las citas según el rol del usuario y los filtros enviados.
     *
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listarCitasFiltradas(User $user, Request $request)
    {
        $query = $user->isAdmin() ? Cita::with('usuario') : $user->citas();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        if ($request->filled('usuario_id') && $user->isAdmin()) {
            $query->where('usuario_id', $request->usuario_id);
        }

        return $query->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->paginate(15);
    }

    /**
     * Retorna estadísticas de las citas según el tipo de usuario (admin o no).
     *
     * @param User $user
     * @return array
     */
    public function obtenerEstadisticas(User $user): array
    {
        return [
            'total' => $user->isAdmin() ? Cita::count() : $user->citas()->count(),
            'pendientes' => $user->isAdmin() ? Cita::pendientes()->count() : $user->citas()->pendientes()->count(),
            'confirmadas' => $user->isAdmin() ? Cita::confirmadas()->count() : $user->citas()->confirmadas()->count(),
            'canceladas' => $user->isAdmin() ? Cita::canceladas()->count() : $user->citas()->canceladas()->count(),
        ];
    }
}
