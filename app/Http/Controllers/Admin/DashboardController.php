<?php

namespace App\Http\Controllers\Admin;

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

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        return view('admin.dashboard');
    } 

    public function registrations()
    {
        return view('admin.user-registrations');
    }

    public function UserDataMailSend($users=null,Request $request)
    {
        if(isset($_POST['Submit']))
        {
            $model->name       = $request->name;
            $model->type       = 1;
            if($model->save())
                return redirect('/admin/manage-qualifications')->with(['status'=>'success','message'=>'Qualification data updated successfully!']);
            else
                return redirect('/admin/manage-qualifications')->with(['status'=>'error','message'=>'Failed to update Qualification data. Try again after sometime!']);
        }
        return view('admin.email-form',['action'=>'/admin/send-user-detail-mail/'.$users]);
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
                $user_id            = Auth::guard('admin')->user()->id;
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
        return view('admin.changepassword');
    }

    public function __construct()
    {
        $this->middleware('admin');
    }
}
