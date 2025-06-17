<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AppointmentController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->isAdmin() 
            ? Cita::with('usuario') 
            : $user->citas();


        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        }
        
        if ($request->filled('usuario_id') && $user->isAdmin()) {
            $query->where('usuario_id', $request->usuario_id);
        }

        $citas = $query->orderBy('fecha', 'desc')
                      ->orderBy('hora', 'desc')
                      ->paginate(15);

        $stats = [
            'total' => $user->isAdmin() ? Cita::count() : $user->citas()->count(),
            'pendientes' => $user->isAdmin() ? Cita::pendientes()->count() : $user->citas()->pendientes()->count(),
            'confirmadas' => $user->isAdmin() ? Cita::confirmadas()->count() : $user->citas()->confirmadas()->count(),
            'canceladas' => $user->isAdmin() ? Cita::canceladas()->count() : $user->citas()->canceladas()->count(),
        ];

        return Inertia::render('Dashboard', [
            'citas' => $citas,
            'stats' => $stats,
            'filters' => $request->only(['estado', 'fecha', 'usuario_id']),
            'usuarios' => $user->isAdmin() ? User::clientes()->get(['id', 'name', 'email']) : null,
        ]);
    }

    public function create()
    {
        $usuarios = auth()->user()->isAdmin() 
            ? User::clientes()->get(['id', 'name', 'email'])
            : null;

        return Inertia::render('Citas/Create', [
            'usuarios' => $usuarios,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date|after:today',
            'hora' => 'required|date_format:H:i',
            'descripcion' => 'required|string|max:500',
            'tipo_servicio' => 'nullable|string|max:100',
            'vehiculo_marca' => 'nullable|string|max:50',
            'vehiculo_modelo' => 'nullable|string|max:50',
            'vehiculo_placa' => 'nullable|string|max:20',
            'usuario_id' => auth()->user()->isAdmin() ? 'required|exists:users,id' : 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $existeCita = Cita::where('fecha', $request->fecha)
                          ->where('hora', $request->hora)
                          ->exists();

        if ($existeCita) {
            return back()->withErrors([
                'hora' => 'Ya existe una cita agendada para esa fecha y hora'
            ])->withInput();
        }

        $cita = Cita::create([
            'usuario_id' => auth()->user()->isAdmin() ? $request->usuario_id : auth()->id(),
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'descripcion' => $request->descripcion,
            'tipo_servicio' => $request->tipo_servicio,
            'vehiculo_marca' => $request->vehiculo_marca,
            'vehiculo_modelo' => $request->vehiculo_modelo,
            'vehiculo_placa' => $request->vehiculo_placa,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('dashboard')->with('success', 'Cita creada exitosamente');
    }

    public function show(Cita $cita)
    {
        $this->authorize('view', $cita);
        
        $cita->load('usuario');

        return Inertia::render('Citas/Show', [
            'cita' => $cita,
        ]);
    }

    public function edit(Cita $cita)
    {
        $this->authorize('update', $cita);

        $usuarios = auth()->user()->isAdmin() 
            ? User::clientes()->get(['id', 'name', 'email'])
            : null;

        return Inertia::render('Citas/Edit', [
            'cita' => $cita,
            'usuarios' => $usuarios,
        ]);
    }

    public function update(Request $request, Cita $cita)
    {
        $this->authorize('update', $cita);

        $validator = Validator::make($request->all(), [
            'fecha' => 'sometimes|required|date|after:today',
            'hora' => 'sometimes|required|date_format:H:i',
            'descripcion' => 'sometimes|required|string|max:500',
            'estado' => 'sometimes|required|in:pendiente,confirmada,cancelada',
            'tipo_servicio' => 'nullable|string|max:100',
            'vehiculo_marca' => 'nullable|string|max:50',
            'vehiculo_modelo' => 'nullable|string|max:50',
            'vehiculo_placa' => 'nullable|string|max:20',
            'usuario_id' => auth()->user()->isAdmin() ? 'sometimes|required|exists:users,id' : 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        if ($request->has('fecha') || $request->has('hora')) {
            $fecha = $request->fecha ?? $cita->fecha;
            $hora = $request->hora ?? $cita->hora;
            
            $existeCita = Cita::where('fecha', $fecha)
                              ->where('hora', $hora)
                              ->where('id', '!=', $cita->id)
                              ->exists();

            if ($existeCita) {
                return back()->withErrors([
                    'hora' => 'Ya existe una cita agendada para esa fecha y hora'
                ])->withInput();
            }
        }

        $updateData = $request->only([
            'fecha', 'hora', 'descripcion', 'tipo_servicio',
            'vehiculo_marca', 'vehiculo_modelo', 'vehiculo_placa'
        ]);

        if (auth()->user()->isAdmin()) {
            $updateData = array_merge($updateData, $request->only(['estado', 'usuario_id']));
        }

        $cita->update($updateData);

        return redirect()->route('dashboard')->with('success', 'Cita actualizada exitosamente');
    }

    public function destroy(Cita $cita)
    {
        $this->authorize('delete', $cita);

        $cita->delete();

        return back()->with('success', 'Cita eliminada exitosamente');
    }

    public function cancel(Cita $cita)
    {
        $this->authorize('cancel', $cita);

        $cita->update(['estado' => 'cancelada']);

        return back()->with('success', 'Cita cancelada exitosamente');
    }

    public function availableHours(Request $request)
    {
        $fecha = $request->query('fecha');
        
        if (!$fecha) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha requerida'
            ], 422);
        }

        $horariosDisponibles = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
            '17:00', '17:30', '18:00'
        ];

        $horariosOcupados = Cita::where('fecha', $fecha)
                               ->pluck('hora')
                               ->map(function ($hora) {
                                   return Carbon::parse($hora)->format('H:i');
                               })
                               ->toArray();

        $horariosLibres = array_diff($horariosDisponibles, $horariosOcupados);

        return response()->json([
            'success' => true,
            'data' => array_values($horariosLibres)
        ]);
    }
}
