<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        if(Auth::id()){
            $role=Auth()->user()->role;
            if($role=='admin'){
                return view('admin.adminhome');
            }
            // else if($role=='admin'){
            //     return view('admin.adminhome');
            // }
            // else{
            //     return redirect()->back();
            // }
        }
    }
}
