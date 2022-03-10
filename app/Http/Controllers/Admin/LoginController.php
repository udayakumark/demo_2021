<?php

namespace App\Http\Controllers\Admin;

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

class LoginController extends Controller
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
                        $validator->errors()->add('user_name', 'Invalid username or password1.');
                    }else if(!Hash::check($_POST['password'], $check_user->password)){
                        $validator->errors()->add('user_name', 'Invalid username or password2.');
                    // }else if($check_user->user_type!=1 ){
                    //     $validator->errors()->add('user_name', 'Invalid username or password3.');
                    }else if($check_user->status==2){
                        $validator->errors()->add('user_name', 'Account is currently blocked. Please contact your admin.');
                    }
                }
            });


            if ($validator->passes())
            {
                $credentials = $request->only('user_name', 'password');

                if (Auth::guard('admin')->attempt($credentials))
                {
                    $request->session()->regenerate();
                    return redirect()->intended('admin/dashboard');
                }
            }
            else
            {
                return redirect('admin')->withErrors($validator)->withInput();
            }
        }

        return view('admin.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        return redirect('/admin');
    }

    public function Forgotpassword(Request $request)
    {
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required|digits:10',
            ]);
           

            if($validator->passes())
            {
                // $checkMobile   = User::where(['mobile_number'=>$request->mobile_number,'user_type'=>2])->first();
                $checkMobile   = User::where(['mobile_number'=>$request->mobile_number])->first();
                if(empty($checkMobile)){
                    return response()->json(['error'=>'Invalid Mobile number. try again.']);
                }else if($checkMobile->status!==1){
                    return response()->json(['error'=>'Sorry your account is not active now. Please contact our admin.']);
                }else{
                    $redirectUrl  = url('admin');
                    $characters   = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $string       = str_shuffle($characters);
                    $Password     = "12345";//substr($string, 0,8);
                    $newPassword  = Hash::make($Password);
                    $checkMobile->password = $newPassword;
                    if($checkMobile->save())
                    {
                        $message  = "Dear Customer, Your SAKTHI RICE new password is ".$Password;
                        $mobile_number = $checkMobile->mobile_number;
                        sendSms(urlencode($message),$mobile_number);
                        return response()->json(['success'=>'New password is sent to your registered Mobile number.','redirectUrl'=>$redirectUrl]);
                    }
                    else
                    {
                        return response()->json(['error'=>'Server Error. try again after some time.']);
                    }
                }
            }
            else
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
        }
        return view('admin.forgotpwd.forgot-password');
    }

    public function __construct()
    {
        $this->middleware('admin_auth')->except('logout');
    }
}
