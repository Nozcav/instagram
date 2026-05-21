<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validación corregida
        // Se cambia 'unique:profiles,username' por 'unique:users,username' 
        // porque tu tabla 'users' ya tiene esa columna.
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'username' => 'required|string|unique:users,username' 
        ]);

        // 2. Creación del Usuario
        // Se incluye 'username' aquí para evitar el error 1364.
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        // Verificación de seguridad
        if (!$user) {
            Log::error('No se pudo crear el usuario', ['data' => $data]);
            return response()->json(['message' => 'No se pudo crear el usuario'], 500);
        }

        // 3. Creación del Perfil asociado
        // Esto asume que tienes un modelo Profile y la tabla vinculada.
        try {
            Profile::create([
                'user_id'  => $user->id,
                'username' => $data['username']
            ]);
        } catch (\Exception $e) {
            Log::warning('Usuario creado pero no se pudo crear el perfil', ['error' => $e->getMessage()]);
        }

        // 4. Generación de Token (Sanctum)
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado con éxito',
            'user'    => $user->load('profile'),
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        // Verificación de credenciales
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user'  => $user->load('profile'),
            'token' => $token
        ]);
    }

    public function me(Request $request)
    {
        // Retorna el usuario autenticado con su perfil
        return response()->json($request->user()->load('profile'));
    }
}