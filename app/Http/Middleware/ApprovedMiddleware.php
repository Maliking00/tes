<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){
            if(Auth::user()->status == 'approved'){
                return $next($request);
            }else{
                return redirect(route('show.for.approval'))->with('warning', 'Your account is currently pending approval. Please contact the administrator for further information.');
            }
        }else{
            return redirect(route('welcome'))->with('warning', 'Login first.');
        }
    }
}
