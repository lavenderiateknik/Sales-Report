<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceSummaryController extends Controller
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
            'working_days' => 'required|integer|min:0|max:31',
            'present_days' => 'required|integer|min:0|max:31',
            'absent_days' => 'nullable|integer|min:0|max:31',
        ]);

        // auto hitung kalau tidak dikirim
        if (!isset($validated['absent_days'])) {
            $validated['absent_days'] =
                max(0, $validated['working_days'] - $validated['present_days']);
        }

        // validasi logis
        if ($validated['present_days'] > $validated['working_days']) {
            return response()->json([
                'message' => 'Hari hadir tidak boleh melebihi hari kerja'
            ], 422);
        }

        $validated['created_by'] = Auth::id();

        $attendance = AttendanceSummary::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            $validated
        );

        return response()->json([
            'message' => 'Absensi KPI tersimpan',
            'data' => $attendance
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceSummary $attendanceSummary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceSummary $attendanceSummary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceSummary $attendanceSummary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceSummary $attendanceSummary)
    {
        //
    }
}
