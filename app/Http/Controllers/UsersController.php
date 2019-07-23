<?php

namespace App\Http\Controllers;

use Adldap\AdldapInterface;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * @var \App\User
     */
    private $repo;

    private $paginateNumber = 20;

    /**
     * UsersController constructor.
     * @param \App\User $repo
     */
    public function __construct(User $repo) {
        $this->repo = $repo;

        $this->middleware('ldap_user');

    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $query = $this->repo->withTrashed();

        $users = ($keyword = $request->query("keywords")) ?
            $query->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->paginate($this->paginateNumber) :
            $query->paginate($this->paginateNumber);

        if ($request->ajax()) {
            return response()->json($users);
        }


        return view("admin.users.index", compact("users"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view("admin.users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validatedData = $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $newUser = new User();

        $newUser->name = $validatedData['name'];
        $newUser->email = $validatedData['email'];
        $newUser->password = bcrypt($validatedData['password']);
        $newUser->save();

        return redirect()->route('users.index')
                         ->withStatus('New user created.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return void
     */
    public function show(User $user) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return void
     */
    public function edit(User $user) {
        return view("admin.users.edit", compact("user"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        $validatedData = $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (isset($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->withStatus('User updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Exception
     */
    public function destroy(Request $request, User $user) {
        $user->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()
                ->route('users.index')
                ->withStatus('User deleted.');
    }

    public function restore(Request $request, int $userId) {

        if ($request->user()->is_ldap_user) {
            abort(403);
        }
        if ($user = User::withTrashed()->find($userId)) {
            $user->restore();
        }

        return redirect()->route("users.index")
                         ->withStatus($user->name, " is restored!");
    }

    public function search(Request $request) {
        if ($keyword = $request->query("keywords")) {
            $users = $this->repo->withTrashed()->where('name', 'like', "%{$keyword}%")
                                ->paginate($this->paginateNumber);
        } else {
            $users = $this->repo->withTrashed()->paginate($this->paginateNumber);
        }

        if ($request->ajax()) {
            return response()->json($users);
        }

        return view('admin.users.index', compact('users'));
    }

    public function ldap(AdldapInterface $adldap) {

        $search = $adldap->search();

        $users = $search->users()->get();

        return view("admin.users.ldap.index", compact('users'));
    }

    public function addLdapUser(Request $request) {

        $validatedData = $this->validate($request, [
            'name'  => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $user = new User($validatedData);
        $user->password = bcrypt(str_random(26));
        $user->is_ldap_user = true;
        $user->save();

        return redirect()->route('users.index');
    }
}
