<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAdmin
{
    protected $request;

    /**
     * Create a new bindings substitutor.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route("auth.login");
        }
        if (!Auth::user()->privilegio >= 1) {
            return redirect()->route("home.index");
        }
        return $next($request);
    }
}
