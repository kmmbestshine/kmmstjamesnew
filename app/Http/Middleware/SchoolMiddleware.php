<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class SchoolMiddleware
{
    protected $auth;
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

    public function handle($request, Closure $next)
    {
        // dd($this->auth->user()->type);
        if($this->auth->user()->type!='school')
            {
               if ($request->ajax()) {
                return response('Unauthorized.', 401);
                } else {
                return \Redirect::back();
                }
            }
        return $next($request);
    }
}
