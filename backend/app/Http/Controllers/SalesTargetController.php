<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'target_omset' => 'nullable|numeric',
            'target_visit' => 'nullable|integer',
            'target_penawaran' => 'nullable|integer',
            'target_new_customer' => 'nullable|integer',
        ]);

        $validated['created_by'] = Auth::id();

        $target = SalesTarget::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            $validated
        );

        return response()->json([
            'message' => 'Target KPI tersimpan',
            'data' => $target
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(SalesTarget $salesTarget)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesTarget $salesTarget)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesTarget $salesTarget)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesTarget $salesTarget)
    {
        //
    }
}
