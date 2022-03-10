<?php

namespace App\Http\Controllers\Dealer;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class DealerdashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('dealer')->user();
        return view('dealer.dashboard');
    } 

    // Change password
    public function changepassword(Request $request)
    {
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
            ]);

            if($validator->passes())
            {
                $user_id            = Auth::guard('dealer')->user()->id;
                $current_password   = $request->current_password;
                $new_password       = Hash::make($request->password);
                $check_password     = User::where(['id'=>$user_id])->first();
                if(Hash::check($current_password, $check_password->password))
                {
                    User::where(['id'=>$user_id])->update(['password'=>$new_password]);
                    return response()->json(['success'=>'New password changed successfully']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Current password is invalid. try again.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('dealer.changepassword');
    } 

    public function __construct()
    {
        $this->middleware('dealer');
    }
}
