<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Category; // Asegúrate de importar el modelo Category
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
            'category_name' => 'required|string|max:255',
        ]);

        // Buscar la categoría por nombre
        $category = Category::where('name', $request->category_name)->first();

        // Si no existe la categoría, devolver un error
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Asignar el propietario autenticado al nuevo negocio
        $business = new Business($request->all());
        $business->owner_id = Auth::id();
        $business->category_id = $category->id; // Asignar el ID de la categoría
        $business->save(); // Guardar el negocio

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
            'category_name' => 'required|string|max:255', // Validar que category_name esté presente
        ]);

        // Actualizar los datos del negocio (excepto categoría)
        $business->update($request->except('category_name')); // Excluir category_name de la actualización

        // Si se proporciona un nuevo nombre de categoría, buscar el category_id correspondiente
        if ($request->has('category_name')) {
            $category = Category::where('name', $request->category_name)->first();

            // Verificar si la categoría existe
            if ($category) {
                $business->category_id = $category->id; // Actualizar el category_id
                $business->save();
            } else {
                return response()->json(['error' => 'Categoría no encontrada.'], 404);
            }
        }

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
