<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerDatabase;
use App\Imports\CustomerDatabaseImport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerDatabaseController extends Controller
{
    public function index()
    {
        $data = CustomerDatabase::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
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
