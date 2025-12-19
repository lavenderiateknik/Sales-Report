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
    Route::get('/user/{id}', [UserController::class, 'edit']);
    Route::put('/updateuser/{id}',[UserController::class,'update']);
    Route::delete('/deleteuser/{id}', [UserController::class, 'destroy']);
    
    Route::get('/alltypecustomers', [TypeCustomerController::class, 'index']);
    Route::get('/alltypeprojects', [TypeProjectController::class, 'index']);
    Route::get('/alltypereports', [TypeReportController::class, 'index']);
    //salesreport
    Route::get('/allsalesreports', [SalesReportController::class, 'index']);
    Route::get('/allvisitedcustomers/{id}', [SalesReportController::class, 'visited']);
    Route::get('/salesreports/{id}', [SalesReportController::class, 'salesreports']);
    Route::get('/salesreport/{id}', [SalesReportController::class, 'salesreport']);
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
    Route::get('/recap-reports-date', [SalesReportController::class, 'recapByDate']);
    Route::get('/recap-nominal-monthly', [SalesReportController::class, 'recapNominalMontly']);
    Route::get('/recap-reports-type', [SalesReportController::class, 'recapByType']);
    Route::get('/recap-report-by-customer', [SalesReportController::class, 'recapByCustomerName']);

    Route::get('/allroles', [RoleController::class, 'index']);
    Route::get('/allbranches', [BranchController::class, 'index']);
    Route::post('/addbranches',[BranchController::class, 'store']);
    Route::get('/branches/{id}',[BranchController::class, 'show']);
    Route::put('/updatebranch/{id}', [BranchController::class, 'update']);
    Route::delete('/branches/{id}', [BranchController::class, 'destroy']);



    Route::get('/allcustomerdatabase', [CustomerDatabaseController::class, 'index']);
    Route::get('/customerdatabase', [CustomerDatabaseController::class, 'indexGrouped']);
    Route::post('/addcustomerdatabase', [CustomerDatabaseController::class, 'store']);
    Route::post('/import-customer-database', [CustomerDatabaseController::class, 'import']);
    Route::get('/customer-database/project/{project_id}', [CustomerDatabaseController::class, 'detailByProject']);


    Route::post('/logout', [AuthController::class, 'logout']);
});
