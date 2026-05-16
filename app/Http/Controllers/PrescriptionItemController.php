<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionItem;
use App\Http\Requests\StorePrescriptionItemRequest;
use App\Http\Requests\UpdatePrescriptionItemRequest;

class PrescriptionItemController extends Controller
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
    public function store(StorePrescriptionItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PrescriptionItem $prescriptionItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrescriptionItem $prescriptionItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrescriptionItemRequest $request, PrescriptionItem $prescriptionItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrescriptionItem $prescriptionItem)
    {
        //
    }
}
