<?php

namespace App\Http\Controllers\Auth;

use Adldap\AdldapInterface;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $email = $request->get('email') . "@medt.cuhk.edu.hk";
        if ($user = User::whereEmail($email)
                        ->whereIsLdapUser(true)
                        ->first()) {

            Log::info('try login ldap user');

            try {
                $provider = app(AdldapInterface::class);
                if ($provider->auth()->attempt(
                    $email,
                    $request->get('password'))) {
                    Auth::login($user);
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                Log::info('ldap user login exception' . $e->getMessage());

                return false;
            }
        }

        return false;
    }

    public function adminLogin(Request $request) {

        if ($this->attemptAdminLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);

    }

    private function attemptAdminLogin($request) {

        $credentials = $this->credentials($request);

        $credentials['is_ldap_user'] = false;

        return $this->guard()
                    ->attempt($credentials, $request->filled('remember')
                    );
    }

    protected function authenticated(Request $request, $user) {
        return redirect()->intended("/dashboard");
    }

}
