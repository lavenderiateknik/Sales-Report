<?php

namespace App\Http\Controllers;

use App\Models\TypeReport;
use Illuminate\Http\Request;

class TypeReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeReport = TypeReport::all();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $allTypeReport
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
    public function show(TypeReport $typeReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeReport $typeReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeReport $typeReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeReport $typeReport)
    {
        //
    }
}
