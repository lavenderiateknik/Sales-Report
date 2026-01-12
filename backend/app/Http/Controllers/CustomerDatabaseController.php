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
        $roleId = (int) $user->role_id; 
        $query = CustomerDatabase::with(['assignedUser', 'branch']);
        if ($roleId === 7) {
            $data = $query->where('assigned_to_user', $user->id)->get();     
        } elseif (in_array($roleId, [4,5, 6])) {
            $data = $query->where('id_branch', $user->branch_id)->get();
        } elseif (in_array($roleId, [1, 2, 3])) { // Misal Role 1 adalah Super Admin
            // Super Admin: Melihat semua data tanpa filter
            $data = $query->get();
        } else {
            // Default: Jika role tidak dikenali, kembalikan array kosong agar aman
            $data = collect([]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'role_debug' => $roleId, 
            'count' => $data->count(),
            'data' => $data,
        ]);
    }

    
    public function indexGrouped(Request $request)
    {
        $user = $request->user();

        // Ambil role id dengan aman
        $role = is_object($user->role) ? $user->role->id : $user->role;

        // Filter berdasarkan role dan cabang
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
                'project_id'    => $first->project_id,
                'project_name'  => $first->project_name,
                'project_stage' => $first->project_stage,
                'project_town'  => $first->project_town,
                'project_start' => $first->construction_start_text,
                'project_end'   => $first->construction_end_text,

                // Tambahkan semua company yang terlibat
                'item' => $items->map(function ($item) {
                    return [
                        'company_name'      => $item->company_name,
                        'id_branch'         => $item->id_branch,
                        'local_value'       => $item->local_value,
                        'development_type'  => $item->development_type,
                        'project_stage'     => $item->project_stage,
                        'contact_person'    => $item->contact_person,
                        'phone'             => $item->phone,
                        'email'             => $item->email,
                        // tambahkan kolom lain sesuai kebutuhan
                    ];
                })->values()
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


    public function assign(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'customer_id' => 'required|exists:customer_database,id',
            'user_id'     => 'required|exists:users,id',
        ]);

        try {
            // 2. Cari data customer
            $customer = CustomerDatabase::findOrFail($request->customer_id);

            // 3. Update kolom assigned_to_user
            $customer->update([
                'assigned_to_user' => $request->user_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil ditugaskan.',
                'data'    => $customer->load('assignedUser') // Load relasi untuk dikembalikan ke frontend
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan penugasan: ' . $e->getMessage()
            ], 500);
        }
    }
}
