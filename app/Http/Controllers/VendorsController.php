<?php

namespace App\Http\Controllers;

use App\Vendor;
use Illuminate\Http\Request;

class VendorsController extends Controller
{
    /**
     * @var \App\Institution
     */
    private $repo;

    private $paginateNumber = 20;

    /**
     * InstitutionsController constructor.
     * @param \App\Institution $repo
     */
    public function __construct(Vendor $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $vendors = ($keyword = $request->query("keywords")) ?
            $this->repo
                ->where('name', 'like', "%{$keyword}%")
                ->paginate($this->paginateNumber) :
            $this->repo
                ->paginate($this->paginateNumber);

        if ($request->ajax()) {
            return response()->json($vendors);
        }

        return $request->ajax() ?
            response()->json($vendors) :
            view("admin.vendors.index", compact("vendors"));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view("admin.vendors.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validatedData = $this->validate($request, [
            'name' => 'required'
        ]);

        $this->repo->create($validatedData);

        return redirect()->route('vendors.index')
                         ->withStatus("New vendor created!");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vendor $vendor
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vendor $vendor
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor) {
        return view("admin.vendors.edit", compact("vendor"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Vendor              $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor) {
        $validatedData = $this->validate($request, [
            'name' => 'required'
        ]);

        $vendor->update($validatedData);

        return redirect()->route('vendors.index')
                         ->withStatus("Vendor updated!");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Vendor             $vendor
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, Vendor $vendor) {
        $vendor->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()
                ->route('vendors.index')
                ->withStatus('Vendor category deleted.');
    }
}
