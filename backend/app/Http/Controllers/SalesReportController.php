<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;


use function in_array; 

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeReport = SalesReport::with(['typeCustomer', 'typeReport','user.branch'])
            ->select([
                'id','date','user_id','type_customer_id','customer_name',
                'is_new_customer','type_project','project_name',
                'pic_name','pic_phone','pic_position',
                'type_report_id','report_notes','equipment_needs',
                'items_purchase_order','nominal_purchase_order',
                'check_in','check_out','coordinate_check_in','coordinate_check_out',
                'picture','created_at'
            ])
            ->orderBy('date','desc')
            ->paginate(request('per_page', 10));

        $allTypeReport->getCollection()->transform(function ($item) {
            if ($item->picture) {
                $item->picture = base64_encode($item->picture);
            }
            return $item;
        });

        return response()->json($allTypeReport);
    }

    public function optionvisit()
    {
        $reports = SalesReport::with(['typeCustomer', 'typeReport','user.branch'])
            ->select([
                'id','date','user_id','type_customer_id','customer_name',
                'is_new_customer','type_project','project_name',
                'pic_name','pic_phone','pic_position',
                'type_report_id','report_notes','equipment_needs',
                'items_purchase_order','nominal_purchase_order',
                'check_in','check_out','coordinate_check_in','coordinate_check_out',
                'picture','created_at'
            ])
            ->where('type_report_id', 1)
            ->orderBy('created_at','desc')
            ->paginate(request('per_page', 10));

        $reports->getCollection()->transform(function ($item) {
            if ($item->picture) {
                $item->picture = base64_encode($item->picture);
            }
            return $item;
        });

        return response()->json($reports);
    }


    public function salesreports($id)
    {
        $reports = SalesReport::with(['typeCustomer', 'typeReport', 'user'])
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 10));

        // convert BLOB → base64
        $reports->getCollection()->transform(function ($item) {
            if ($item->picture) {
                $item->picture = base64_encode($item->picture);
            }
            return $item;
        });

        return response()->json($reports);
    }

    public function optionvisitsalesreports($id)
    {
        $reports = SalesReport::with(['typeCustomer', 'typeReport', 'user'])
            ->where('user_id', $id)
            ->where('type_report_id', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 10));

        $reports->getCollection()->transform(function ($item) {
            if ($item->picture) {
                $item->picture = base64_encode($item->picture);
            }
            return $item;
        });

        return response()->json($reports);
    }


    public function salesreport($reportsid)
    {

        $reports = SalesReport::with(['typeCustomer', 'typeReport', 'user'])
        ->where('id', $reportsid)
        ->where('type_report_id', '!=', 6)
        ->first();
        $reports->makeHidden(['picture']);
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ], 200);
    }
    
    public function branchsalesreports($id)
    {
        $reports = SalesReport::with(['typeCustomer', 'typeReport', 'user'])
            ->join('users', 'users.id', '=', 'sales_reports.user_id')
            ->where('users.branch_id', $id)
            ->select('sales_reports.*')
            ->orderBy('sales_reports.created_at', 'desc')
            ->paginate(request('per_page', 10));

        $reports->getCollection()->transform(function ($item) {
            if ($item->picture) {
                $item->picture = base64_encode($item->picture);
            }
            return $item;
        });

        return response()->json($reports);
    }

    public function optionvisitbranchsalesreports($id)
    {
        $branchreports = SalesReport::with(['typeCustomer', 'typeReport', 'user'])
            ->join('users', 'users.id', '=', 'sales_reports.user_id')
            ->where('users.branch_id', $id)
            ->where('type_report_id', 1)
            ->select('sales_reports.*')
            ->orderBy('sales_reports.created_at', 'desc')
            ->paginate(request('per_page', 10));

        $branchreports->getCollection()->transform(function ($item) {
            if ($item->picture) {
                $item->picture = base64_encode($item->picture);
            }
            return $item;
        });

        return response()->json($branchreports);
    }


    public function typecustomerbysales($id)
    {
        $query = SalesReport::selectRaw('type_customer_id, COUNT(*) as total')
            ->where('user_id', $id);

        if (request('start_date')) {
            $query->whereDate('date', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->whereDate('date', '<=', request('end_date'));
        }

        $reports = $query
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
        $query = SalesReport::selectRaw('type_customer_id, COUNT(*) as total')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->where('users.branch_id', $branchId);

        if (request('start_date')) {
            $query->whereDate('sales_reports.date', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->whereDate('sales_reports.date', '<=', request('end_date'));
        }

        $reports = $query
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
        $query = SalesReport::selectRaw('type_customer_id, COUNT(*) as total');

        if (request('start_date')) {
            $query->whereDate('date', '>=', request('start_date'));
        }

        if (request('end_date')) {
            $query->whereDate('date', '<=', request('end_date'));
        }

        $reports = $query
            ->groupBy('type_customer_id')
            ->with('typeCustomer')
            ->get();

        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $reports
        ]);
    }

    public function recapByMonth(Request $request)
    {
        $query = SalesReport::selectRaw("
            DATE_FORMAT(date, '%b') as month,
            SUM(CASE WHEN type_report_id = 3 THEN 1 ELSE 0 END) as offering,
            SUM(CASE WHEN type_report_id = 5 THEN 1 ELSE 0 END) as purchase
        ");

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $reports = $query
            ->groupBy(DB::raw("DATE_FORMAT(date, '%b')"))
            ->orderByRaw("MIN(date)")
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

    public function recapByCustomer(Request $request)
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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sales_reports.date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($user->role_id == 7) {
            $query->where('sales_reports.user_id', $user->id);
        } elseif (in_array($user->role_id, [6,5,4])) {
            $query->where('users.branch_id', $user->branch_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Found',
            'data' => $query->get()
        ]);
    }

    public function recapByCustomerSpv()
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

        $query->where('sales_reports.user_id', $user->id);
        $data = $query->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Found',
            'data' => $data
        ]);
    }

    public function recapByDate(Request $request)
    {
        $user = Auth::user();

        $query = SalesReport::select(
            'date',
            DB::raw("SUM(CASE WHEN type_report_id = 1 THEN 1 ELSE 0 END) as visit"),
            DB::raw("SUM(CASE WHEN type_report_id = 2 THEN 1 ELSE 0 END) as follow_up"),
            DB::raw("SUM(CASE WHEN type_report_id = 3 THEN 1 ELSE 0 END) as offering"),
            DB::raw("SUM(CASE WHEN type_report_id = 4 THEN 1 ELSE 0 END) as negotiation"),
            DB::raw("SUM(CASE WHEN type_report_id = 5 THEN 1 ELSE 0 END) as po"),
            DB::raw("COUNT(*) as total")
        )
        ->join('users', 'sales_reports.user_id', '=', 'users.id')
        ->groupBy('date');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sales_reports.date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($user->role_id == 7) {
            $query->where('sales_reports.user_id', $user->id);
        } elseif (in_array($user->role_id, [6,5,4])) {
            $query->where('users.branch_id', $user->branch_id);
        }

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

        // Filter default berdasarkan role
        if ($user->role_id == 8) {
            $baseQuery->where('sales_reports.user_id', $user->id);
        } elseif (in_array($user->role_id, [7, 6, 5])) {
            $baseQuery->where('users.branch_id', $user->branch_id);
        }

        // Jika memilih 1 user di dropdown → override filter
        if (request()->has('user_id') && request('user_id') != '') {
            $baseQuery->where('sales_reports.user_id', request('user_id'));
        }

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

    public function recapNominalMontlyDetail(Request $request)
    {
        $user = auth()->user();

        $baseQuery = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id');

        // 🔹 PRIORITAS FILTER (INI YANG MENYAMAKAN PRINSIP)
        if ($request->filled('user_id')) {
            $baseQuery->where('sales_reports.user_id', $request->user_id);

        } elseif ($request->filled('branch_id')) {
            $baseQuery->where('users.branch_id', $request->branch_id);

        } else {
            // 🔹 Default role
            if ($user->role_id == 8) {
                $baseQuery->where('sales_reports.user_id', $user->id);
            } elseif (in_array($user->role_id, [7, 6, 5])) {
                $baseQuery->where('users.branch_id', $user->branch_id);
            }
        }

        // Tambahkan setelah filter user/branch
        if ($request->filled('start_date')) {
            $baseQuery->whereDate('sales_reports.date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $baseQuery->whereDate('sales_reports.date', '<=', $request->end_date);
        }


        // 🔹 Ambil sales sesuai filter
        $salesList = (clone $baseQuery)
            ->select('users.id', 'users.name')
            ->distinct()
            ->get();

        // 🔹 Ambil bulan
        $months = DB::table('sales_reports')
            ->selectRaw("
                MONTH(date) as month_number,
                MONTHNAME(date) as month_name
            ")
            ->groupByRaw("MONTH(date), MONTHNAME(date)")
            ->orderByRaw("MONTH(date)")
            ->pluck('month_name', 'month_number');

        $reports = [];

        foreach ($months as $monthNumber => $monthName) {
            $monthData = ['month' => $monthName, 'sales' => []];

            foreach ($salesList as $sales) {
                $total = (clone $baseQuery)
                    ->whereMonth('sales_reports.date', $monthNumber)
                    ->where('sales_reports.user_id', $sales->id)
                    ->sum('sales_reports.nominal_purchase_order');

                $monthData['sales'][] = [
                    'name' => $sales->name,
                    'total' => $total
                ];
            }

            $reports[] = $monthData;
        }

        return response()->json([
            'data' => $reports,
            'grand_total' => (clone $baseQuery)->sum('sales_reports.nominal_purchase_order'),
        ]);
    }


   public function recapNominalMonthlyBranches(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $data = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->select(
                DB::raw('branches.id as branch_id'),
                DB::raw('branches.name as branch'),
                DB::raw('MONTH(sales_reports.date) as month_num'),
                DB::raw('MONTHNAME(sales_reports.date) as month'),
                DB::raw('SUM(sales_reports.nominal_purchase_order) as total')
            )
            ->whereYear('sales_reports.date', $year)
            ->groupBy(
                'branches.id',
                'branches.name',
                DB::raw('MONTH(sales_reports.date)'),
                DB::raw('MONTHNAME(sales_reports.date)')
            )
            ->orderBy('branches.id')
            ->orderBy(DB::raw('MONTH(sales_reports.date)'))
            ->get();

        return response()->json($data);
    }


    public function recapNominalMonthlyBranch($branchId)
    {
        $year = request('year', date('Y'));

        $reports = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->selectRaw("
                MONTH(sales_reports.date) as month_num,
                MONTHNAME(sales_reports.date) as month,
                SUM(sales_reports.nominal_purchase_order) as total
            ")
            ->where('users.branch_id', $branchId)
            ->whereYear('sales_reports.date', $year)
            ->groupBy(
                DB::raw('MONTH(sales_reports.date)'),
                DB::raw('MONTHNAME(sales_reports.date)')
            )
            ->orderBy(DB::raw("MONTH(sales_reports.date)"))
            ->get();

        return response()->json($reports);
    }

    public function availableYears()
    {
        $years = DB::table('sales_reports')
            ->select(DB::raw("DISTINCT(YEAR(date)) as year"))
            ->orderBy('year', 'ASC')
            ->pluck('year');

        return response()->json([
            "years" => $years
        ]);
    }

    public function recapNominalMontlySpv()
    {
        $user = auth()->user();

        $baseQuery = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->where('sales_reports.user_id', $user->id);        
        
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
        
    public function recapByTypeSpv()
    {
        $user = auth()->user();

        $query = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->join('type_reports', 'sales_reports.type_report_id', '=', 'type_reports.id')
            ->selectRaw("type_reports.name as report_type, COUNT(sales_reports.id) as total");
        
        $query->where('sales_reports.user_id', $user->id);

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

    public function recapByType(Request $request)
    {
        $user = auth()->user();

        $query = DB::table('sales_reports')
            ->join('users', 'sales_reports.user_id', '=', 'users.id')
            ->join('type_reports', 'sales_reports.type_report_id', '=', 'type_reports.id')
            ->selectRaw("
                type_reports.name as report_type,
                COUNT(sales_reports.id) as total
            ");

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sales_reports.date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->filled('user_id')) {
            $query->where('sales_reports.user_id', $request->user_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('users.branch_id', $request->branch_id);
        } else {
            if ($user->role_id == 7) {
                $query->where('sales_reports.user_id', $user->id);
            } elseif (in_array($user->role_id, [6,5,4])) {
                $query->where('users.branch_id', $user->branch_id);
            }
        }

        $reports = $query
            ->groupBy('type_reports.name')
            ->orderBy('type_reports.name')
            ->get();

        return response()->json([
            'data' => $reports,
            'grand_total' => $reports->sum('total'),
        ]);
    }

    public function recapByCustomerName()
    {
        $reports = DB::table('sales_reports as sr')
        ->join('type_reports as tr', 'sr.type_report_id', '=', 'tr.id')
        ->select(
            'sr.customer_name as customer',
            'sr.project_name as project_name',
            DB::raw("SUM(CASE WHEN LOWER(tr.name) = 'visit' THEN 1 ELSE 0 END) as visit"),
            DB::raw("SUM(CASE WHEN LOWER(tr.name) IN ('follow up','follow_up','follow') THEN 1 ELSE 0 END) as follow_up"),
            DB::raw("SUM(CASE WHEN LOWER(tr.name) IN ('negosiasi','negotiation') THEN 1 ELSE 0 END) as negotiation"),
            DB::raw("SUM(CASE WHEN LOWER(tr.name) IN ('penawaran','offering') THEN 1 ELSE 0 END) as penawaran"),
            DB::raw("SUM(CASE WHEN LOWER(tr.name) IN ('po','purchase order','preorder') THEN 1 ELSE 0 END) as po"),
            DB::raw("COUNT(sr.id) as grand_total")
        )
        ->groupBy('sr.customer_name', 'sr.project_name')
        ->orderBy('sr.customer_name')
        ->orderBy('sr.project_name')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $reports
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
        try {
            $validated = $request->validate([
                'type_report_id' => 'required',
                'date' => 'required|date',
                'type_customer_id' => 'required|exists:type_customers,id',
                'customer_name' => 'required|string|max:255',
                'type_project' => 'nullable|string',
                'check_in' => 'required|date_format:H:i',
                'coordinate_check_in' => 'nullable|string',
                'project_name' => 'required|string|max:255',
                'pic_name' => 'string|max:255',
                'pic_phone' => 'string|max:20',
                'pic_position' => 'string|max:255',
                'report_notes' => 'nullable|string',
                'equipment_needs' => 'nullable|string',
                'check_out' => 'nullable|date_format:H:i',
                'coordinate_check_out' => 'nullable|string',
                'nominal_purchase_order' => 'nullable|numeric',
                'items_purchase_order' => 'nullable|string',
                'is_new_customer' => 'nullable',
                'picture' => 'nullable|image|max:5120',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }

        // 🔐 paksa boolean supaya konsisten
        $validated['is_new_customer'] = $request->boolean('is_new_customer');

        $report = new SalesReport($validated);
        $report->user_id = Auth::id();

        if ($request->hasFile('picture')) {
            $report->picture = file_get_contents(
                $request->file('picture')->getRealPath()
            );
        }

        $report->save();

        return response()->json([
            'message' => 'Berhasil Tersimpan',
            'data' => [
                'id' => $report->id,
                'date' => $report->date,
            ]
        ], 201);
    }

    public function storeFolowUp(Request $request)
    {
        try {
            $validated = $request->validate([
                'type_report_id' => 'required',
                'date' => 'required|date',
                'type_customer_id' => 'required|exists:type_customers,id',
                'customer_name' => 'required|string|max:255',
                'type_project' => 'nullable|string',
                'project_name' => 'required|string|max:255',
                'pic_name' => 'string|max:255',
                'pic_phone' => 'string|max:20',
                'pic_position' => 'string|max:255',
                'report_notes' => 'nullable|string',
                'equipment_needs' => 'nullable|string',
                'check_out' => 'nullable|date_format:H:i',
                'coordinate_check_out' => 'nullable|string',
                'nominal_purchase_order' => 'nullable|numeric',
                'items_purchase_order' => 'nullable|string',
                'is_new_customer' => 'nullable',
                'picture' => 'nullable|image|max:5120',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }

        // 🔐 paksa boolean supaya konsisten
        $validated['is_new_customer'] = $request->boolean('is_new_customer');

        $report = new SalesReport($validated);
        $report->user_id = Auth::id();

        if ($request->hasFile('picture')) {
            $report->picture = file_get_contents(
                $request->file('picture')->getRealPath()
            );
        }

        $report->save();

        return response()->json([
            'message' => 'Berhasil Tersimpan',
            'data' => [
                'id' => $report->id,
                'date' => $report->date,
            ]
        ], 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(SalesReport $salesReport)
    {
        //
    }

    /**
     * Display visited customers.
     */
    public function visited($id)
    {
        // 1. Ambil data dari database
        $allvisited = SalesReport::where('user_id', $id)
        ->select([
            'id','date','customer_name','project_name',
            'type_report_id','is_new_customer',"type_project","type_customer_id" 
        ])
        ->get();

        // 2. Bersihkan data dari karakter non-UTF8 yang rusak
        $cleanData = $allvisited->map(function ($item) {
            return collect($item->toArray())->map(function ($value) {
                if (is_string($value)) {
                    // Mengonversi string ke UTF-8 dan mengabaikan karakter yang tidak valid
                    return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
                return $value;
            });
        });

        // 3. Kembalikan response
        return response()->json([
            'message' => 'Data Found',
            'data' => $cleanData
        ], 200);
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
    public function update(Request $request, $id)
    {
    //    if ($user->role == 7) {
    //     // Sales hanya boleh update miliknya sendiri
    //     $report = SalesReport::where('id', $id)
    //         ->where('user_id', $user->id)
    //         ->firstOrFail();
    //     } else {
    //         // Atasan boleh akses report bawahan
    //         $report = SalesReport::where('id', $id)->firstOrFail();
    //     }
        $report = SalesReport::findOrFail($id);
        $validated = $request->validate([
            'report_notes' => 'nullable|string',
            'pic_name' => 'nullable|string',
            'pic_phone' => 'nullable|string',
            'pic_position' => 'nullable|string',
            'equipment_needs' => 'nullable|string',
            'items_purchase_order' => 'nullable|string',
            'nominal_purchase_order' => 'nullable|numeric',
            'check_out' => 'required|date_format:H:i',
            'coordinate_check_out' => 'required|string',
            'picture' => 'nullable|image|max:5120',
            'is_new_customer' => 'nullable|boolean'
        ]);

        $report->fill($validated);

        if ($request->hasFile('picture')) {
            $report->picture = file_get_contents(
                $request->file('picture')->getRealPath()
            );
        }

        $report->save();

        return response()->json([
            'message' => 'Sales report berhasil dikirim'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesReport $salesReport)
    {
        //
    }
}
