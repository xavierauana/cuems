<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorContactRequest;
use App\Http\Requests\UpdateVendorContactRequest;
use App\Vendor;
use App\VendorContact;
use Illuminate\Http\Request;

class VendorContactsController extends Controller
{
    /**
     * @var \App\VendorContact
     */
    private $model;

    private $paginateNumber = null;

    /**
     * VendorContactsControllers constructor.
     * @param \App\VendorContact $vendorContact
     */
    public function __construct(VendorContact $vendorContact) {
        $this->model = $vendorContact;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Vendor             $vendor
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Vendor $vendor) {

        $query = $this->model;

        if ($keyword = $request->query("keyword")) {
            $query = $query->where('name', 'like', "%{$keyword}%");
        }

        $vendorContacts = $query->paginate($this->paginateNumber);

        return view("admin.vendors.vendorContacts.index",
            compact('vendor', 'vendorContacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Vendor $vendor
     * @return \Illuminate\Http\Response
     */
    public function create(Vendor $vendor) {

        return view("admin.vendors.vendorContacts.create", compact("vendor"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVendorContactRequest $request
     * @param \App\Vendor                                   $vendor
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVendorContactRequest $request, Vendor $vendor) {

        $validatedData = $request->validated();

        $vendor->contacts()->create($validatedData);

        return redirect()
            ->route('vendors.vendorContacts.index', compact("vendor"))
            ->withStatus('New vendor contact created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vendor        $vendor
     * @param  \App\VendorContact $vendorContact
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor, VendorContact $vendorContact) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Vendor         $vendor
     * @param  \App\VendorContact $vendorContact
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor, VendorContact $vendorContact) {

        return view("admin.vendors.vendorContacts.edit",
            compact('vendor', 'vendorContact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVendorContactRequest $request $request
     * @param  \App\VendorContact                            $vendorContact
     * @param  \App\Vendor                                   $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdateVendorContactRequest $request, Vendor $vendor,
        VendorContact $vendorContact
    ) {
        $validatedData = $request->validated();

        $vendorContact->update($validatedData);

        return redirect()
            ->route('vendors.vendorContacts.index', compact('vendor'))
            ->withStatus('VendorContact updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\VendorContact      $vendorContact
     * @param  \App\Vendor             $vendor
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(
        Request $request, Vendor $vendor, VendorContact $vendorContact
    ) {

        $vendorContact->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()->back()->withStatus('Contact deleted!');
    }
}
