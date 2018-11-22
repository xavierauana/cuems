<?php

namespace App\Http\Controllers;

use App\Position;
use Illuminate\Http\Request;

class PositionsController extends Controller
{
    /**
     * @var \App\Institution
     */
    private $repo;

    private $paginateNumber = 20;

    /**
     * InstitutionsController constructor.
     * @param \App\Position $repo
     */
    public function __construct(Position $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $positions = ($keyword = $request->query("keywords")) ?
            $this->repo
                ->where('name', 'like', "%{$keyword}%")
                ->paginate($this->paginateNumber) :
            $this->repo
                ->paginate($this->paginateNumber);

        if ($request->ajax()) {
            return response()->json($positions);
        }


        return view("admin.positions.index", compact("positions"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view("admin.positions.create");
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

        return redirect()->route('positions.index')
                         ->withStatus('New position created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Position $position
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Position $position
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position) {
        return view("admin.positions.edit", compact("position"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Position            $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position) {
        $validatedData = $this->validate($request, [
            'name' => 'required'
        ]);

        $position->update($validatedData);

        return redirect()
            ->route('positions.index')
            ->withStatus('Position updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Position $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Position $position) {
        $position->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()
                ->route('positions.index')
                ->withStatus('Position deleted.');
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

        return view('admin.positions.index', compact('institutions'));
    }
}
