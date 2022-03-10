<?php

namespace App\Http\Middleware\dealer;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('dealer')->check()==FALSE) 
        {
            return redirect('admin');
            //return response()->view('errors.custom',['code'=>500,'message'=>'ljklkjlj']);
            //abort(403,'ljkljkl');
        }
        
        $response =  $next($request);
        return $response->header('Cache-Control','nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma','no-cache')
            ->header('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
    }
}
