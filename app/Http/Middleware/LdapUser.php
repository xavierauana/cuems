<?php

namespace App\Http\Middleware;

use Closure;

class LdapUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if ($request->user() and !$request->user()->is_ldap_user) {
            return $next($request);
        }

    }
}
