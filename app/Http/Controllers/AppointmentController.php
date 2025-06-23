<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Resources\UserResource;
use App\Models\Cita;
use App\Models\User;
use App\Services\CitaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Controlador responsable de gestionar las operaciones relacionadas con las citas.
 *
 * Este controlador maneja la creación, actualización, visualización y eliminación de citas.
 *
 * @package App\Http\Controllers
 */
class AppointmentController extends Controller
{
    use AuthorizesRequests;

    protected CitaService $service;

    /**
     * Inyecta la dependencia del servicio de citas.
     *
     * @param CitaService $service
     */
    public function __construct(CitaService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el panel principal con citas filtradas, estadísticas y usuarios si es admin.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'citas' => $this->service->listarCitasFiltradas($user, $request),
            'stats' => $this->service->obtenerEstadisticas($user),
            'filters' => $request->only(['estado', 'fecha', 'usuario_id']),
            'usuarios' => $user->isAdmin() ? UserResource::collection(User::clientes()->get()) : null,
        ]);
    }

    /**
     * Almacena una nueva cita en la base de datos.
     *
     * @param StoreCitaRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCitaRequest $request)
    {
        $this->service->crearCita($request);
        return redirect()->route('dashboard')->with('success', 'Cita creada exitosamente');
    }

    /**
     * Muestra los detalles de una cita específica.
     *
     * @param Cita $cita
     * @return Response
     */
    public function show(Cita $cita): Response
    {
        $this->authorize('view', $cita);
        $cita->load('usuario');

        return Inertia::render('Citas/Show', [
            'cita' => $cita
        ]);
    }

    /**
     * Muestra el formulario de edición para una cita.
     *
     * @param Cita $cita
     * @return Response
     */
    public function edit(Cita $cita): Response
    {
        $this->authorize('update', $cita);
        $usuarios = auth()->user()->isAdmin()
            ? UserResource::collection(User::clientes()->get())
            : null;

        return Inertia::render('Citas/Edit', [
            'cita' => $cita,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Actualiza los datos de una cita existente.
     *
     * @param UpdateCitaRequest $request
     * @param Cita $cita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        $this->authorize('update', $cita);
        $this->service->actualizarCita($request, $cita);

        return redirect()->route('dashboard')->with('success', 'Cita actualizada exitosamente');
    }

    /**
     * Elimina una cita de la base de datos.
     *
     * @param Cita $cita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cita $cita)
    {
        $this->authorize('delete', $cita);
        $cita->delete();

        return back()->with('success', 'Cita eliminada exitosamente');
    }

    /**
     * Marca una cita como cancelada.
     *
     * @param Cita $cita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Cita $cita)
    {
        $this->authorize('cancel', $cita);
        $cita->update(['estado' => 'cancelada']);

        return back()->with('success', 'Cita cancelada exitosamente');
    }

    /**
     * Devuelve las horas disponibles para una fecha específica.
     *
     * @param Request $request
     * @param CitaService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableHours(Request $request, CitaService $service)
    {
        if (!$request->filled('fecha')) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha requerida'
            ], 422);
        }

        $horas = $service->obtenerHorasDisponibles($request->fecha, $request->citaId);

        return response()->json([
            'success' => true,
            'data' => $horas
        ]);
    }
}
