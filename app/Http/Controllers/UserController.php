<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    // Obtener todos los usuarios (Solo para administradores)
    public function index()
    {
        $users = User::all();

        return response()->json($users);
    }

    // Mostrar los datos del usuario autenticado
    public function show($id)
    {
        $user = auth()->user();

        // Verificar si el ID de la URL coincide con el ID del usuario autenticado
        if ($user->id != $id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }



    // Crear un nuevo usuario (Solo para administradores)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:user,owner',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    // Actualizar los datos del usuario autenticado
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user();  // Obtener el usuario autenticado

        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
        ]);

        return response()->json(['message' => 'User updated successfully']);
    }

    // Eliminar un usuario (Solo para administradores)
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Solo un administrador debería poder eliminar usuarios
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
