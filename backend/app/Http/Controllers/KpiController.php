<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTarget;
use App\Models\SalesReport;
use App\Models\AttendanceSummary;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KpiController extends Controller
{
    /**
     * KPI per user / bulan / tahun
     * GET /api/kpi/{user}/{month}/{year}
     */
    public function show($userId, $month, $year)
    {
        $auth = Auth::user();

        // =============================
        // 1. VALIDASI AKSES
        // =============================
        if (in_array($auth->role_id, [5,6,7])) {
            $targetUser = User::findOrFail($userId);

            if ($targetUser->branch_id !== $auth->branch_id) {
                return response()->json([
                    'message' => 'Tidak punya akses melihat KPI user ini'
                ], 403);
            }
        }

        // =============================
        // 2. USER INFO
        // =============================
        $user = User::with('branch','role')->findOrFail($userId);

        // =============================
        // 3. SALES REPORT (BASE QUERY)
        // =============================
        $reports = SalesReport::where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        // =============================
        // 4. AKTUAL
        // =============================
        $actual = [
            'visit'        => (clone $reports)->where('type_report_id', 1)->count(),
            'follow_up'    => (clone $reports)->where('type_report_id', 2)->count(),
            'offering'     => (clone $reports)->where('type_report_id', 3)->count(),
            'negotiation'  => (clone $reports)->where('type_report_id', 4)->count(),
            'purchase_order' => (clone $reports)->where('type_report_id', 5)->count(),
            'omset'        => (float) (clone $reports)->sum('nominal_purchase_order'),
            'new_customer' => (clone $reports)->where('is_new_customer', 1)->count(),
        ];

        // =============================
        // 5. TARGET
        // =============================
        $target = SalesTarget::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (!$target) {
            $target = new SalesTarget([
                'target_omset' => 0,
                'target_visit' => 0,
                'target_penawaran' => 0,
                'target_new_customer' => 0,
            ]);
        }

        // =============================
        // 6. ABSENSI
        // =============================
        $attendance = AttendanceSummary::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $attendanceRate = null;
        if ($attendance && $attendance->working_days > 0) {
            $attendanceRate = round(
                ($attendance->present_days / $attendance->working_days) * 100,
                2
            );
        }

        // =============================
        // 7. RESPONSE
        // =============================
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role->name,
                'branch' => $user->branch->name,
            ],

            'period' => [
                'month' => (int) $month,
                'year'  => (int) $year,
            ],

            'attendance' => [
                'working_days' => $attendance->working_days ?? 0,
                'present_days' => $attendance->present_days ?? 0,
                'absent_days'  => $attendance->absent_days ?? 0,
                'rate'         => $attendanceRate,
            ],

            'target' => [
                'omset'        => $target->target_omset ?? 0,
                'visit'        => $target->target_visit ?? 0,
                'penawaran'    => $target->target_penawaran ?? 0,
                'new_customer' => $target->target_new_customer ?? 0,
            ],

            'actual' => $actual,
        ]);
    }
}
