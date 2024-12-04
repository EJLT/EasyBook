<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        $categories = Category::all(); // Obtener todas las categorías
        return response()->json($categories);
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // Validar que el nombre sea obligatorio y tenga un tamaño máximo de 255 caracteres
        ]);

        $category = Category::create($request->all()); // Crear una nueva categoría
        return response()->json($category, 201); // Retornar la categoría creada con un código de estado 201
    }

    // Mostrar una categoría específica
    public function show($id)
    {
        $category = Category::findOrFail($id); // Buscar la categoría por su ID o devolver error 404 si no existe
        return response()->json($category); // Retornar la categoría encontrada
    }

    // Actualizar una categoría existente
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id); // Buscar la categoría por su ID o devolver error 404 si no existe

        $request->validate([
            'name' => 'required|string|max:255', // Validar que el nombre sea obligatorio y tenga un tamaño máximo de 255 caracteres
        ]);

        $category->update($request->all()); // Actualizar los datos de la categoría
        return response()->json($category); // Retornar la categoría actualizada
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        $category = Category::findOrFail($id); // Buscar la categoría por su ID o devolver error 404 si no existe

        $category->delete(); // Eliminar la categoría
        return response()->json(['message' => 'Category deleted successfully.']); // Retornar un mensaje de éxito
    }



}
