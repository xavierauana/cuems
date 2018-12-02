<?php

namespace App\Http\Controllers\Auth;

use Adldap\AdldapInterface;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function attemptLogin(Request $request) {

        if ($user = User::whereEmail($request->get('email'))
                        ->whereIsLadpUser(true)
                        ->first()) {

            try {
                $provider = app(AdldapInterface::class);
                if ($provider->auth()->attempt(
                    $request->get('email'),
                    $request->get('password'))) {

                    Auth::login($user);

                } else {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }
}
