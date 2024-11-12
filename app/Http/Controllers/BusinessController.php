<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::all();
        return response()->json($businesses);
    }

    public function show($id)
    {
        $business = Business::findOrFail($id);
        return response()->json($business);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:businesses,email',
            'owner_id' => 'required|exists:owners,id',
        ]);

        $business = new Business($request->all());
        $business->owner_id = $request->owner_id;
        $business->save();

        return response()->json($business, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|unique:businesses,email,' . $id,
        ]);

        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $business->update($request->all());
        return response()->json($business);
    }

    public function destroy($id)
    {
        $business = Business::findOrFail($id);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $business->delete();
        return response()->json(['message' => 'Business deleted successfully.']);
    }
}
