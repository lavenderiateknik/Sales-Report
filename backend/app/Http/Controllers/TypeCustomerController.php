<?php

namespace App\Http\Controllers;

use App\Models\TypeCustomer;
use Illuminate\Http\Request;
use SebastianBergmann\Comparator\TypeComparator;

class TypeCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeCustomers = TypeCustomer::all();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $allTypeCustomers
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
    public function show(TypeCustomer $typeCustomer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeCustomer $typeCustomer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeCustomer $typeCustomer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeCustomer $typeCustomer)
    {
        //
    }
}
