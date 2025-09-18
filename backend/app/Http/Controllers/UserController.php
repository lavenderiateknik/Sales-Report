<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

use function Pest\Laravel\json;

class UserController extends Controller
{
    public function all()
    {
        $user = User::all();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $user
        ]);
    }
}
