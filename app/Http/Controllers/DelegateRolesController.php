<?php

namespace App\Http\Controllers;

use App\DelegateRole;
use Illuminate\Http\Request;

class DelegateRolesController extends Controller
{
    /**
     * @var \App\DelegateRole
     */
    private $repo;

    /**
     * DelegateRolesController constructor.
     * @param \App\DelegateRole $repo
     */
    public function __construct(DelegateRole $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $roles = $this->repo->paginate(100);

        return view("admin.delegate_roles.index", compact("roles"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view("admin.delegate_roles.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validatedData = $this->validate($request,
            $this->repo->getStoreRules());

        $newRole = $this->repo->create($validatedData);

        return redirect()->route("roles.index")
                         ->withStatus("New Role: {$newRole->label} is created!");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DelegateRole $delegateRole
     * @return \Illuminate\Http\Response
     */
    public function show(DelegateRole $delegateRole) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DelegateRole $delegateRole
     * @return \Illuminate\Http\Response
     */
    public function edit(DelegateRole $role) {
        return view("admin.delegate_roles.edit", ['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\DelegateRole         $role
     * @return void
     */
    public function update(Request $request, DelegateRole $role) {
        $validatedData = $this->validate($request,
            $role->getUpdateRules());

        $role->update($validatedData);

        return redirect()->route('roles.index')
                         ->withStatus("Delegate Role: {$role->label} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DelegateRole $delegateRole
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $delegateRoleId) {

        DelegateRole::whereId($delegateRoleId)->delete();

        return response()->json(['status' => 'completed']);
    }
}
