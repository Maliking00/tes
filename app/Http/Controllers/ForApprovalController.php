<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        if(Auth::user()->status == 'approved'){
            return redirect('/dashboard');
        }
        return view('forApproval.for-approval');
    }
}
