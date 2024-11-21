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
        Log::info('Accediendo a la ruta GET /businesses', ['user_id' => Auth::id()]);

        // Verificar el rol del usuario
        if (Auth::user()->role === 'owner') {
            // Si es un owner, filtrar los negocios que pertenecen a este propietario
            $businesses = Business::where('owner_id', Auth::id())->get();
        } else {
            // Si es un user, devolver todos los negocios
            $businesses = Business::all();
        }

        // Devolver los negocios en formato JSON
        return response()->json($businesses);
    }


    public function show($id)
    {
        Log::info('Accediendo a la ruta GET /businesses/' . $id, ['user_id' => Auth::id()]);

        // Obtener un negocio específico
        $business = Business::findOrFail($id);

        // Verificar si el usuario tiene permiso para ver el negocio
        $this->authorize('view', $business);

        return response()->json($business);
    }

    public function store(Request $request)
    {
        Log::info('Accediendo a la ruta POST /businesses', ['user_id' => Auth::id(), 'token' => $request->bearerToken()]);

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
        Log::info('Accediendo a la ruta PUT /businesses/' . $id, ['user_id' => Auth::id()]);

        // Obtener el negocio a actualizar
        $business = Business::findOrFail($id);

        // Verificar si el usuario tiene permiso para actualizar el negocio
        $this->authorize('update', $business);

        // Validar los datos de la solicitud
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:businesses,email,' . $id,
        ]);

        // Actualizar los datos del negocio
        $business->update($request->all());

        return response()->json($business);
    }

    public function destroy($id)
    {
        Log::info('Accediendo a la ruta DELETE /businesses/' . $id, ['user_id' => Auth::id()]);

        // Obtener el negocio a eliminar
        $business = Business::findOrFail($id);

        // Verificar si el usuario tiene permiso para eliminar el negocio
        $this->authorize('delete', $business);

        // Eliminar el negocio
        $business->delete();

        return response()->json(['message' => 'Business deleted successfully.']);
    }
}
