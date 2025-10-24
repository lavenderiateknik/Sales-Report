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
    public function indexGrouped(Request $request)
    {
        $user = $request->user();

        // Pastikan gunakan kolom integer, bukan relasi
        $role = is_object($user->role) ? $user->role->id : $user->role; 

        // Filter data sesuai peran
        if (in_array($role, [5, 6, 7, 8])) {
            $data = CustomerDatabase::where('id_branch', $user->branch_id)
                    ->orderBy('project_id', 'asc')
                    ->get();
        } else {
            $data = CustomerDatabase::orderBy('project_id', 'asc')->get();
        }

        // Kelompokkan berdasarkan project_id
        $grouped = $data->groupBy('project_id')->map(function ($items) {
            $first = $items->first();
            return [
                'project_id' => $first->project_id,
                'project_name' => $first->project_name,
                'project_stage' => $first->project_stage,
                'project_town' => $first->project_town, 
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    public function detailByProject($project_id, Request $request)
    {
        $user = $request->user();
        $role = is_object($user->role) ? $user->role->id : $user->role;

        $query = CustomerDatabase::where('project_id', $project_id);

        if (in_array($role, [5, 6, 7, 8])) {
            $query->where('id_branch', $user->branch_id);
        }

        $data = $query->orderBy('company_name')->get([
            'company_name',
            'company_street_name',
            'company_website',
            'company_roles',
            'role_on_project',
            'company_town',
            'company_state',
            'company_email',
            'company_phone',
            'contact_first_name',
            'contact_surname',
            'contact_position',
            'contact_email',
            'role_status',
        ]);

        $projectInfo = $query->first([
            'project_id',
            'project_type',
            'project_name',
            'project_address',
            'project_stage',
            'project_province',
            'project_region',
            'project_town',
            'project_detail',
            'construction_start_text',
            'construction_end_text',
            'local_value',
            'site_area',
            'floor_area',
            'storeys'
        ]);

        return response()->json([
            'success' => true,
            'project' => $projectInfo,
            'companies' => $data,
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
