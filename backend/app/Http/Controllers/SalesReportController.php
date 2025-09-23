<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use Illuminate\Http\Request;

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
