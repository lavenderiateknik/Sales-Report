<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

use function Pest\Laravel\json;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'branch'])->get();
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
}
