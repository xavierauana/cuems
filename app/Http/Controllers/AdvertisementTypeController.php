<?php

namespace App\Http\Controllers;

use App\AdvertisementType;
use Illuminate\Http\Request;

class AdvertisementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $advertisementTypes = AdvertisementType::all();

        return view('admin.advertisement_types.index',
            compact('advertisementTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin.advertisement_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validatedData = $this->validate($request,
            AdvertisementType::StoreRules());

        AdvertisementType::create($validatedData);

        return redirect()->route('advertisement_types.index')
                         ->withStatus('New advertisement type created!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\AdvertisementType $advertisementType
     * @return \Illuminate\Http\Response
     */
    public function show(AdvertisementType $advertisementType) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\AdvertisementType $advertisementType
     * @return \Illuminate\Http\Response
     */
    public function edit(AdvertisementType $advertisementType) {
        return view('admin.advertisement_types.edit',
            compact('advertisementType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AdvertisementType   $advertisementType
     * @return \Illuminate\Http\Response
     */
    public function update(
        Request $request, AdvertisementType $advertisementType
    ) {
        $validatedData = $this->validate($request,
            AdvertisementType::StoreRules());

        $advertisementType->update($validatedData);

        return redirect()->route('advertisement_types.index')
                         ->withStatus('Advertisement type updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\AdvertisementType $advertisementType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdvertisementType $advertisementType) {
        $advertisementType->delete();

        return redirect()->route('advertisement_types.index')
                         ->withStatus('Advertisement types deleted!');

    }
}
