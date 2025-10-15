<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerDatabase;
use App\Imports\CustomerDatabaseImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CustomerDatabaseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Pastikan gunakan kolom integer, bukan relasi
        $role = is_object($user->role) ? $user->role->id : $user->role; 

        if (in_array($role, [5, 6, 7, 8])) {
            $data = CustomerDatabase::where('id_branch', $user->branch_id)->get();
        } else {
            $data = CustomerDatabase::all();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'id_branch' => 'required|exists:branches,id', // wajib ada
        ]);

        Excel::import(new CustomerDatabaseImport($request->id_branch), $request->file('file'));

        return response()->json(['message' => 'Data berhasil diimpor ke database!']);
    }
}
