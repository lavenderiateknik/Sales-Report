<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use function Pest\Laravel\json;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'branch'])->orderBy('created_at', "desc")->get();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $users
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user()->load(['role', 'branch']);

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $user
        ]);
    }
    public function edit($id)
    {
        $user=User::findOrFail($id);
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $user
        ]);
    }

    public function store(Request $request)
    {
        // validasi data
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed', // pastikan ada password_confirmation
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        // simpan user
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data'    => $user
        ], 201);
    }

}
