<?php

namespace App\Http\Controllers;

use App\Exports\InstitutionExport;
use App\Imports\InstitutionImport;
use App\Institution;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InstitutionsController extends Controller
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
    public function __construct(Institution $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $institutions = ($keyword = $request->query("keywords")) ?
            $this->repo
                ->where('name', 'like', "%{$keyword}%")
                ->paginate($this->paginateNumber) :
            $this->repo
                ->paginate($this->paginateNumber);

        if ($request->ajax()) {
            return response()->json($institutions);
        }


        return view("admin.institutions.index", compact("institutions"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view("admin.institutions.create");
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

        return redirect()->route('institutions.index')
                         ->withStatus('New institution created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Institution $institution
     * @return void
     */
    public function show(Institution $institution) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Institution $institution
     * @return void
     */
    public function edit(Institution $institution) {
        return view("admin.institutions.edit", compact("institution"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Institution         $institution
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Institution $institution) {
        $validatedData = $this->validate($request, [
            'name' => 'required'
        ]);

        $institution->update($validatedData);

        return redirect()
            ->route('institutions.index')
            ->withStatus('Institution updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Institution        $institution
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Exception
     */
    public function destroy(Request $request, Institution $institution) {
        $institution->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()
                ->route('institutions.index')
                ->withStatus('Institution deleted.');
    }

    public function search(Request $request) {
        if ($keyword = $request->query("keywords")) {
            $institutions = $this->repo->where('name', 'like', "%{$keyword}%")
                                       ->paginate($this->paginateNumber);
        } else {
            $institutions = $this->repo->paginate($this->paginateNumber);
        }

        if ($request->ajax()) {
            return response()->json($institutions);
        }

        return view('admin.institutions.index', compact('institutions'));
    }

    public function export() {
        return (new InstitutionExport())->download('institutions.xlsx');
    }

    public function import() {
        return view('admin.institutions.import');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postImport(Request $request) {

        $this->validate($request, [
            'file' => 'required|file|min:0.1'
        ]);

        Excel::import(new InstitutionImport(), request()->file('file'));

        return redirect()->route('institutions.index')
                         ->withStatus('Institutions imported!');
    }
}
