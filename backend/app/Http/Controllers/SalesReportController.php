<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeReport = SalesReport::with(['typeCustomer', 'typeProject', 'typeReport'])->get();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $allTypeReport
        ], 200);
    }

    public function salesreports($id)
    {
        $reports = SalesReport::where('user_id', $id)->with(['typeCustomer', 'typeProject', 'typeReport'])->get();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ], 200);
    }
    public function branchsalesreports($id)
    {
        $branchreports = SalesReport::with(['typeCustomer', 'typeProject', 'typeReport'])
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
