<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function in_array; 

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeReport = SalesReport::with(['typeCustomer', 'typeProject', 'typeReport','user'])->get();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $allTypeReport
        ], 200);
    }

    public function salesreports($id)
    {
        $reports = SalesReport::where('user_id', $id)->with(['typeCustomer', 'typeProject', 'typeReport', 'user'])->get();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ], 200);
    }
    public function branchsalesreports($id)
    {
        $branchreports = SalesReport::with(['typeCustomer', 'typeProject', 'typeReport', 'user'])
            ->join('users', 'users.id', '=', 'sales_reports.user_id')
            ->where('users.branch_id', $id)
            ->select('sales_reports.*')
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $branchreports
        ], 200);
    }

    public function typecustomerbysales($id)
    {
        $reports = SalesReport::selectRaw('type_customer_id, COUNT(*) as total')
            ->where('user_id', $id)
            ->groupBy('type_customer_id')
            ->with('typeCustomer')
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }

    public function typecustomerbybranch($branchId)
    {
        $reports = SalesReport::selectRaw('type_customer_id, COUNT(*) as total')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->where('users.branch_id', $branchId)
            ->groupBy('type_customer_id')
            ->with('typeCustomer')
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }
    public function alltypecustomers()
    {
        $reports = SalesReport::selectRaw('type_customer_id, COUNT(*) as total')
            ->groupBy('type_customer_id')
            ->with('typeCustomer')
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }

    public function recapByMonth()
    {
        $reports = SalesReport::selectRaw("
            DATE_FORMAT(date, '%b') as month,
            SUM(CASE WHEN type_report_id = 3 THEN 1 ELSE 0 END) as offering,
            SUM(CASE WHEN type_report_id = 5 THEN 1 ELSE 0 END) as purchase
        ")
            ->groupBy(DB::raw("DATE_FORMAT(date, '%b')"))
            ->orderByRaw("MIN(date)") // supaya urut Jan, Feb, dst
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }

    public function recapByMonthBranch($branch)
    {
        $reports = SalesReport::selectRaw("
            DATE_FORMAT(sales_reports.date, '%b') as month,
            SUM(CASE WHEN sales_reports.type_report_id = 3 THEN 1 ELSE 0 END) as offering,
            SUM(CASE WHEN sales_reports.type_report_id = 5 THEN 1 ELSE 0 END) as purchase
        ")
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->where('users.branch_id', $branch)
            ->groupBy(DB::raw("DATE_FORMAT(sales_reports.date, '%b')"))
            ->orderByRaw("MIN(sales_reports.date)") // biar urut Jan, Feb, dst
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }



    public function recapByMonthUser($id)
    {
        $reports = SalesReport::selectRaw("
            DATE_FORMAT(date, '%b') as month,
            SUM(CASE WHEN type_report_id = 3 THEN 1 ELSE 0 END) as offering,
            SUM(CASE WHEN type_report_id = 5 THEN 1 ELSE 0 END) as purchase
        ")
            ->where('user_id', $id)
            ->groupBy(DB::raw("DATE_FORMAT(date, '%b')"))
            ->orderByRaw("MIN(date)")
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }

    public function recapByCustomer()
    {
        $user = Auth::user();

        $query = SalesReport::select(
            'customer_name',
            DB::raw("SUM(CASE WHEN type_report_id = 1 THEN 1 ELSE 0 END) as visit"),
            DB::raw("SUM(CASE WHEN type_report_id = 2 THEN 1 ELSE 0 END) as follow_up"),
            DB::raw("SUM(CASE WHEN type_report_id = 3 THEN 1 ELSE 0 END) as penawaran"),
            DB::raw("SUM(CASE WHEN type_report_id = 4 THEN 1 ELSE 0 END) as negosiasi"),
            DB::raw("SUM(CASE WHEN type_report_id = 5 THEN 1 ELSE 0 END) as po"),
            DB::raw("COUNT(*) as total")
        )
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->groupBy('customer_name');

        // 🔹 Filter sesuai role
        if ($user->role_id == 8) {
            // Sales → hanya data miliknya
            $query->where('sales_reports.user_id', $user->id);
        } elseif (in_array($user->role_id, [7, 6, 5])) {
            // Supervisor, Branch Manager, Assistant Manager → berdasarkan branch
            $query->where('users.branch_id', $user->branch_id);
        }
        // Manager Sales & Top Management → tidak ada filter

        $data = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data Found',
            'data' => $data
        ]);
    }

    public function recapNominalMontly()
    {
        $user = auth()->user();

        // Query dasar
        $baseQuery = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id');

        // Filter berdasarkan role
        if ($user->role_id == 8) {
            $baseQuery->where('sales_reports.user_id', $user->id);
        } elseif (in_array($user->role_id, [7, 6, 5])) {
            $baseQuery->where('users.branch_id', $user->branch_id);
        }
        // Role 4,3,2,1 → tidak difilter

        // Rekap per bulan
        $reports = (clone $baseQuery)
            ->selectRaw("MONTHNAME(sales_reports.date) as month, SUM(sales_reports.nominal_purchase_order) as total")
            ->groupByRaw("MONTH(sales_reports.date), MONTHNAME(sales_reports.date)")
            ->orderByRaw("MONTH(sales_reports.date)")
            ->get();

        // Grand total
        $grandTotal = (clone $baseQuery)
            ->sum('sales_reports.nominal_purchase_order');

        return response()->json([
            'data' => $reports,
            'grand_total' => $grandTotal,
        ]);
    }

    public function recapByType()
    {
        $user = auth()->user();

        $query = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->join('type_reports', 'sales_reports.type_report_id', '=', 'type_reports.id')
            ->selectRaw("type_reports.name as report_type, COUNT(sales_reports.id) as total");

        // Filtering sesuai hirarki
        if ($user->role_id == 8) {
            // Sales → hanya miliknya
            $query->where('sales_reports.user_id', $user->id);
        } elseif (in_array($user->role_id, [7, 6, 5])) {
            // Supervisor, Branch Manager, Assistant Manager → berdasarkan branch
            $query->where('users.branch_id', $user->branch_id);
        } 
        // Role 4,3,2,1 → lihat semua

        $reports = $query
            ->groupBy('type_reports.name')
            ->orderBy('type_reports.name')
            ->get();

        $grandTotal = $reports->sum('total');

        return response()->json([
            'data' => $reports,
            'grand_total' => $grandTotal,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesReport $salesReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesReport $salesReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesReport $salesReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesReport $salesReport)
    {
        //
    }
}
