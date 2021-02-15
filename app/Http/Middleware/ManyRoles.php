<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class ManyRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next, $roles)
    {    
        $roles = explode("|", $roles);
        foreach($roles as $role)
        {
            if($this->auth->user()->type==$role)
            {
                return $next($request);
            }
        }
        return redirect()->back();
    }
}
