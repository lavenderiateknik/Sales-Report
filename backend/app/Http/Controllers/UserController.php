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

    public function userPerBranch($branch)
    {
        $users = User::where('branch_id', $branch)->get();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $users
        ],200);
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

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'role_id' => ['required','exists:roles,id'],
            'branch_id' => ['required','exists:branches,id'],
            'password' => ['nullable','confirmed','min:6'],
        ]);

        // update field biasa
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
        ]);

        // jika password dikirim
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // app/Http/Controllers/Api/UserController.php

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function getSalesByBranch(Request $request)
    {
        $user = $request->user();
        
        // Ambil user yang rolenya 8 DAN branch_id nya sama dengan user yang login
        $sales = User::where('role_id', 8)
                    ->where('branch_id', $user->branch_id)
                    ->select('id', 'name')
                    ->get();

        return response()->json($sales);
    }



}
