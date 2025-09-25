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
    Route::get('/allusers', [UserController::class, 'index']);
    Route::get('/alltypecustomers', [TypeCustomerController::class, 'index']);
    Route::get('/alltypeprojects', [TypeProjectController::class, 'index']);
    Route::get('/alltypereports', [TypeReportController::class, 'index']);
    //salesreport
    Route::get('/allsalesreports', [SalesReportController::class, 'index']);
    Route::get('/salesreports/{id}', [SalesReportController::class, 'salesreports']);
    Route::get('/branchsalesreports/{branch}', [SalesReportController::class, 'branchsalesreports']);

    Route::get('/typecustomers/{id}', [SalesReportController::class, 'typecustomerbysales']);
    Route::get('/typecustomersbybranch/{branchId}', [SalesReportController::class, 'typecustomerbybranch']);
    Route::get('/alltypecustomers', [SalesReportController::class, 'alltypecustomers']);

    Route::get('/recap-reports', [SalesReportController::class, 'recapByMonth']);
    Route::get('/recap-reports-branch/{branch}', [SalesReportController::class, 'recapByMonthBranch']);
    Route::get('/recap-reports/{id}', [SalesReportController::class, 'recapByMonthUser']);
    Route::get('/recap-reports-customer', [SalesReportController::class, 'recapByCustomer']);
    Route::get('/recap-nominal-monthly', [SalesReportController::class, 'recapNominalMontly']);
    Route::get('/recap-reports-type', [SalesReportController::class, 'recapByType']);


    Route::get('/allroles', [RoleController::class, 'index']);
    Route::get('/allbranches', [BranchController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
