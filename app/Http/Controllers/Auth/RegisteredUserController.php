<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;     
use App\Models\Direccion;  
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'saldo' => 'nullable|numeric|min:0',
            'dni' => ['nullable', 'string', 'max:20'],
            'municipio' => ['nullable', 'string', 'max:255'],
            'calle' => ['nullable', 'string', 'max:255'],
            'numCasa' => ['nullable', 'string', 'max:255'],
            'provincia' => ['nullable', 'string', 'max:255'],
        ]);

        // Creamos el Usuario 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        // Creamos la Dirección con los datos enviados 
        $direccion = Direccion::create([
            'municipio' => $request->municipio ?? 'Sin especificar',
            'calle' => $request->calle ?? 'Sin especificar',
            'numCasa' => $request->numCasa ?? '0',
            'provincia' => $request->provincia ?? 'Sin especificar',
        ]);

        // Creamos el Cliente vinculando el User ID y el Direccion ID
        Cliente::create([
            'user_id' => $user->id,
            'direccion_id' => $direccion->id,
            'nombre' => $request->name,
            'apellidos' => $request->apellidos ?? '',
            'telefono' => $request->telefono ?? '',
            'email' => $request->email,
            'dni' => $request->dni ?? '00000000X',
            'saldo' => $request->saldo ?? 0,
            'activo' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $token = $user->createToken('api')->plainTextToken;
        
        return response([
            'token' => $token,
            'user'  => $user->load('cliente.direccion'), 
        ], 201);
    }
}
