<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\TypeReportController;
use App\Http\Controllers\TypeProjectController;
use App\Http\Controllers\TypeCustomerController;
use App\Http\Controllers\CustomerDatabaseController;
use App\Http\Controllers\AttendanceSummaryController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\SalesTargetController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::get('/allusers', [UserController::class, 'all'])->name("allUsers");

// Rute yang memerlukan otentikasi
// Rute untuk otentikasi, tidak perlu middleware auth

Route::post('/login', [AuthController::class, 'login']);

// Rute yang memerlukan otentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
    Route::get('/user', [UserController::class, 'user']);
    Route::post('/createuser',[UserController::class, 'store']);
    Route::get('/allusers', [UserController::class, 'index']);
    Route::get('/usersbranch/{id}', [UserController::class, 'userPerBranch']);
    Route::get('/user/{id}', [UserController::class, 'edit']);
    Route::put('/updateuser/{id}',[UserController::class,'update']);
    Route::get('/sales-by-branch',[UserController::class,'getSalesByBranch']);
    Route::delete('/deleteuser/{id}', [UserController::class, 'destroy']);
    
    Route::get('/alltypecustomers', [TypeCustomerController::class, 'index']);
    Route::get('/alltypeprojects', [TypeProjectController::class, 'index']);
    Route::get('/alltypereports', [TypeReportController::class, 'index']);
    //salesreport
    Route::get('/allsalesreports', [SalesReportController::class, 'index']);
    Route::get('/allvisitedcustomers/{id}', [SalesReportController::class, 'visited']);
    Route::get('/salesreports/{id}', [SalesReportController::class, 'salesreports']);
    Route::get('/salesreport/{reportsid}', [SalesReportController::class, 'salesreport']);
    Route::get('/sales-reports/{id}/picture', [SalesReportController::class, 'showPicture']);
    Route::post('/sales-reports', [SalesReportController::class, 'store']);
    Route::put('/checkout/{id}', [SalesReportController::class, 'update']);
    Route::get('/branchsalesreports/{branch}', [SalesReportController::class, 'branchsalesreports']);

    Route::get('/typecustomers/{id}', [SalesReportController::class, 'typecustomerbysales']);
    Route::get('/typecustomersbybranch/{branchId}', [SalesReportController::class, 'typecustomerbybranch']);
    Route::get('/optiontypecustomers', [SalesReportController::class, 'alltypecustomers']);

    Route::get('/recap-reports', [SalesReportController::class, 'recapByMonth']);
    Route::get('/recap-reports-branch/{branch}', [SalesReportController::class, 'recapByMonthBranch']);
    Route::get('/recap-reports/{id}', [SalesReportController::class, 'recapByMonthUser']);
    Route::get('/recap-reports-customer', [SalesReportController::class, 'recapByCustomer']);
    Route::get('/recap-reports-customer-spv', [SalesReportController::class, 'recapByCustomerSpv']);
    Route::get('/recap-reports-date', [SalesReportController::class, 'recapByDate']);
    Route::get('/recap-nominal-monthly', [SalesReportController::class, 'recapNominalMontly']);
    Route::get('/recap-nominal-monthly-detail', [SalesReportController::class, 'recapNominalMontlyDetail']);
    Route::get('/recap-nominal-monthly-spv', [SalesReportController::class, 'recapNominalMontlySpv']);
    Route::get('/recap-nominal-monthly-branches', [SalesReportController::class, 'recapNominalMonthlyBranches']);
    Route::get('/recap-nominal-monthly-branch/{id}', [SalesReportController::class, 'recapNominalMonthlyBranch']);
    Route::get('/available-years', [SalesReportController::class, 'availableYears']);
    
    Route::get('/recap-reports-type', [SalesReportController::class, 'recapByType']);
    Route::get('/recap-reports-type-spv', [SalesReportController::class, 'recapByTypeSpv']);
    Route::get('/recap-report-by-customer', [SalesReportController::class, 'recapByCustomerName']);

    Route::get('/allroles', [RoleController::class, 'index']);
    Route::get('/allbranches', [BranchController::class, 'index']);
    Route::post('/addbranches',[BranchController::class, 'store']);
    Route::get('/branches/{id}',[BranchController::class, 'show']);
    Route::put('/updatebranch/{id}', [BranchController::class, 'update']);
    Route::delete('/branches/{id}', [BranchController::class, 'destroy']);



    Route::get('/allcustomerdatabase', [CustomerDatabaseController::class, 'index']);
    Route::put('/updatestatuscustomerdatabase', [CustomerDatabaseController::class, 'updatestatus']);
    Route::get('/customerdatabase', [CustomerDatabaseController::class, 'indexGrouped']);
    Route::post('/addcustomerdatabase', [CustomerDatabaseController::class, 'store']);
    Route::post('/import-customer-database', [CustomerDatabaseController::class, 'import']);
    Route::get('/customer-database/project/{project_id}', [CustomerDatabaseController::class, 'detailByProject']);
    Route::post('/customer/assign', [CustomerDatabaseController::class, 'assign']);

    // KPI
    Route::get('/kpi/user/{userId}', [KpiController::class, 'userMonthly']);
    Route::get('/kpi/{user}/{month}/{year}', [KpiController::class, 'show']);
    Route::get('/sales-targets/{userId}/{month}/{year}',[SalesTargetController::class,'showTarget']);
    Route::post('/sales-targets',[SalesTargetController::class,'store']);
    Route::get('/sales-targets/{user}/{month}/{year}', [KpiController::class, 'getTarget']);
    //attendants
    Route::post('/attendance-summaries', [AttendanceSummaryController::class, 'store']);
    Route::get('/attendance-summaries/{user}/{month}/{year}',[AttendanceSummaryController::class, 'showByUser']);
    Route::get('/attendance-summaries/month/{month}/{year}',[AttendanceSummaryController::class, 'listByMonth']);



    Route::post('/logout', [AuthController::class, 'logout']);
});
