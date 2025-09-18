<?php

namespace App\Http\Controllers;

use App\Models\TypeProject;
use Illuminate\Http\Request;

class TypeProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allTypeProjects = TypeProject::all();
        return response()->json([
            "success" => true,
            "message" => "Data Found",
            "data" => $allTypeProjects
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
    public function show(TypeProject $typeProject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeProject $typeProject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeProject $typeProject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeProject $typeProject)
    {
        //
    }
}
