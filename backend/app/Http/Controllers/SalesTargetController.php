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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2020',
            'target_omset' => 'required|numeric|min:0',
            'target_visit' => 'required|integer|min:0',
            'target_penawaran' => 'required|integer|min:0',
            'target_new_customer' => 'required|integer|min:0',
        ]);

        $target = SalesTarget::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'month'   => $request->month,
                'year'    => $request->year,
            ],
            [
                'target_omset'        => $request->target_omset,
                'target_visit'       => $request->target_visit,
                'target_penawaran'   => $request->target_penawaran,
                'target_new_customer'=> $request->target_new_customer,
                'created_by'         => Auth::id(),
            ]
        );

        return response()->json([
            'message' => 'Target KPI berhasil disimpan',
            'data' => $target
        ]);
    }

    public function getTarget($userId, $month, $year)
    {
        $target = SalesTarget::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        return response()->json($target);
    }



    /**
     * Display the specified resource.
     */
    public function show(SalesTarget $salesTarget)
    {
        //
    }

    public function showTarget($userId,$month,$year)
    {
        $target = SalesTarget::where('user_id',$userId)
            ->where('month',$month)
            ->where('year',$year)
            ->first();

        return response()->json([
            'data' => $target
        ]);
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
