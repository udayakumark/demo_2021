<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Mail;

class DealerloginController extends Controller
{
    public function index(Request $request)
    {

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            	'user_name' => 'required',
            	'password' => 'required',
        	]);

        	$validator->after(function ($validator) {
        	if($_POST['user_name']!="" && $_POST['password']!="")
        	{
        		$check_user = User::where('user_name',trim($_POST['user_name']))->where('status','!=',0)->first();
        		if(empty($check_user)){
					$validator->errors()->add('user_name', 'Invalid username or password.');
        		}else if(!Hash::check($_POST['password'], $check_user->password)){
        			$validator->errors()->add('user_name', 'Invalid username or password.');
        		}else if($check_user->user_type!=3){
        			$validator->errors()->add('user_name', 'Invalid username or password.');
        		}else if($check_user->status==2){
        			$validator->errors()->add('user_name', 'Account is currently blocked. Please contact your admin.');
        		}
        	}
			});


        	if ($validator->passes()) 
        	{
        		$credentials = $request->only('user_name', 'password');
            	if (Auth::guard('dealer')->attempt($credentials)) 
            	{
                	$request->session()->regenerate();
                	return redirect()->intended('dealer/dashboard');
            	}
        	}
        	else
        	{
        		return redirect('dealer')->withErrors($validator)->withInput();
        	}
        }

        return view('dealer.index');
    } 

    public function logout(Request $request)
    {
        Auth::guard('dealer')->logout();
        $request->session()->invalidate();
        return redirect('/dealer');
    }

    public function __construct()
    {
        $this->middleware('dealer_auth')->except('logout');
    }
}
