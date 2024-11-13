<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;

class OwnerController extends Controller
{
    public function store(Request $request)
    {
        $owner = Owner::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => 'owner',
        ]);

        return response()->json($owner, 201);
    }

    public function index()
    {
        $owners = Owner::all();
        return response()->json($owners);
    }

    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        return response()->json($owner);
    }

    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->delete();
        return response()->json(['message' => 'Owner deleted successfully']);
    }



}
