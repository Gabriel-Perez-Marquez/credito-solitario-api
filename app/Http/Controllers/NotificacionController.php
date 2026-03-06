<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $notificaciones = Notificacion::where('user_id', $request->user()->id)
                            ->orderBy('created_at', 'desc') 
                            ->get();

        $data = $notificaciones->map(function ($noti) {
            return [
                'id' => $noti->id,
                'titulo' => $noti->titulo,
                'mensaje' => $noti->mensaje,
                'leida' => (bool) $noti->leida,
                'fecha' => $noti->created_at->locale('es')->diffForHumans(), 
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', 
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        $notificacion = Notificacion::create([
            'user_id' => $request->user_id,
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje,
            'leida' => false, 
        ]);

        return response()->json([
            'message' => 'Notificación enviada correctamente',
            'data' => $notificacion
        ], 201);
    }


    public function marcarComoLeidas(Request $request)
    {
        Notificacion::where('user_id', $request->user()->id)
                    ->where('leida', false)
                    ->update(['leida' => true]);

        return response()->json(['message' => 'Notificaciones marcadas como leídas']);
    }
}
