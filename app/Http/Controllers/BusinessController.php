<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    public function index()
    {
        Log::info('Accediendo a la ruta GET /businesses', ['user_id' => Auth::id()]); // Log para verificar la autenticación

        // Obtener todos los negocios
        $businesses = Business::all();
        return response()->json($businesses);
    }

    public function show($id)
    {
        Log::info('Accediendo a la ruta GET /businesses/' . $id, ['user_id' => Auth::id()]); // Log para verificar la autenticación

        // Obtener un negocio específico
        $business = Business::findOrFail($id);

        // Verificar si el usuario tiene permiso para ver el negocio
        $this->authorize('view', $business);

        return response()->json($business);
    }

    public function store(Request $request)
    {
        Log::info('Accediendo a la ruta POST /businesses', ['user_id' => Auth::id(), 'token' => $request->bearerToken()]); // Log para verificar el token

        // Verificar si el usuario tiene permiso para crear un negocio
        $this->authorize('create', Business::class);

        // Validar los datos de la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:businesses,email',
        ]);

        // Asignar el propietario autenticado al nuevo negocio
        $business = new Business($request->all());
        $business->owner_id = Auth::id();
        $business->save();

        return response()->json($business, 201);
    }

    public function update(Request $request, $id)
    {
        Log::info('Accediendo a la ruta PUT /businesses/' . $id, ['user_id' => Auth::id()]); // Log para verificar la autenticación

        // Validar los datos de la solicitud
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:businesses,email,' . $id,
        ]);

        // Obtener el negocio a actualizar
        $business = Business::findOrFail($id);

        // Verificar que el usuario autenticado es el propietario del negocio
        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Verificar si el usuario tiene permiso para actualizar el negocio
        $this->authorize('update', $business);

        // Actualizar los datos del negocio
        $business->update($request->all());

        return response()->json($business);
    }

    public function destroy($id)
    {
        Log::info('Accediendo a la ruta DELETE /businesses/' . $id, ['user_id' => Auth::id()]); // Log para verificar la autenticación

        // Obtener el negocio a eliminar
        $business = Business::findOrFail($id);

        // Verificar que el usuario autenticado es el propietario del negocio
        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Verificar si el usuario tiene permiso para eliminar el negocio
        $this->authorize('delete', $business);

        // Eliminar el negocio
        $business->delete();

        return response()->json(['message' => 'Business deleted successfully.']);
    }
}
