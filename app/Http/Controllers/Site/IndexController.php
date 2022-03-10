<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\ContactusRequests;
use App\Models\RiceBenefits;
use App\Models\Gallery;
use App\Models\Products;
use App\Models\ProductPrices;
use App\Models\CartProducts;
use App\Models\HomeBanner;
use App\Models\Testimonials;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class IndexController extends Controller
{
    public function Index()
    {
        $Banners      = HomeBanner::where('status',1)->get();
        $Testimonials = Testimonials::where('status',1)->get();
        $model   = Products::where('status','!=',0);
        $model->orderBy('date_time', 'desc');
        $model->limit(10);
        $data   = $model->get();
        $latest_products = [];

        if(count($data)>0)
        {
            foreach ($data as $index=>$product)
            {
                $cart_products  = [];
                $product_prices = ProductPrices::where(['status'=>1,'product_id'=>$product->id])->orderBy('id','asc')->limit(1)->get();
                if(!empty($product_prices))
                {
                    $price_id = $product_prices[0]->id;
                    if(isset(Auth::guard('web')->user()->id)){
                        $user_id        = Auth::guard('web')->user()->id;
                        $cart_products  = CartProducts::where(['status'=>1,'user_id'=>$user_id,'price_id'=>$price_id])->first();
                    }
                }
                array_push($latest_products,array("product"=>$product,"product_prices"=>$product_prices,"cart_products"=>$cart_products));
            }
        }
        return view('site.index',['latest_products'=>$latest_products,'Banners'=>$Banners,'Testimonials'=>$Testimonials]);
    }

    public function Login(Request $request)
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
                }else if($check_user->user_type!=2){
                    $validator->errors()->add('user_name', 'Invalid username or password.');
                }else if($check_user->status==2){
                    $validator->errors()->add('user_name', 'Account is currently blocked. Please contact your admin.');
                }else if($check_user->status==3){
                    $verifyUrl    = url('otp-verification/'.$check_user->reset_key);
                    $verifyUrl    = '<a href="'.$verifyUrl.'">Click Here</a>';
                    $validator->errors()->add('user_name', 'Otp verification not completed. Click the link to vetify the otp.'.$verifyUrl);
                }
            }
            });


            if ($validator->passes())
            {
                $credentials = $request->only('user_name', 'password');
                if (Auth::guard('web')->attempt($credentials))
                {
                    $request->session()->regenerate();
                    return response()->json(['success'=>"Login Successfully.",'redirectUrl'=>url('myaccount')]);
                }
            }
            else
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
        }
        return view('site.login');
    }

    public function Register(Request $request)
    {
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email_id' => 'required|email|unique:users',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'user_name' => 'required|unique:users',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
                'pincode' => 'required|numeric|digits:6|string',
                'address' => 'required|string',
            ]);

            if($validator->passes())
            {
            	$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    			$string       = str_shuffle($characters);
    			$resetKey     = substr($string, 0,12);
    			$encryptKey   = sha1($resetKey);
    			$otpCode      = str_shuffle($string);
    			$otpCode      = substr($otpCode, 0,6);
    			$verifyUrl    = url('otp-verification/'.$encryptKey);

            	$model = new User();
            	$model->user_name = $request->user_name;
            	$model->email_id  = $request->email_id;
            	$model->mobile_number = $request->mobile_number;
            	$model->password = Hash::make($request->password);
            	$model->user_type = 2;
            	$model->otp_code = $otpCode;
            	$model->reset_key = $encryptKey;
                $model->otp_created = date('Y-m-d H:i:s',strtotime('+20 minute'));
            	$model->date_time = date('Y-m-d H:i:s');
            	if($model->save())
            	{
            		$user_details = new UserDetails;
            		$user_details->user_id = $model->id;
            		$user_details->first_name = $request->first_name;
            		$user_details->last_name = $request->last_name;
            		$user_details->dob = null;
            		$user_details->pincode = $request->pincode;
            		$user_details->address = $request->address;
            		$user_details->profile_image = null;
            		$user_details->date_time = date('Y-m-d H:i:s');
            		$user_details->save();

                	return response()->json(['success'=>'Registration process completed successfully','redirectUrl'=>$verifyUrl]);
            	}
            	else
            	{
            		return response()->json(['error'=>'Server problem. try again after sometime.']);
            	}
            }
            else
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
        }
        return view('site.register');
    }



    public function OtpVerification($key=null,Request $request)
    {
    	$checkKey = User::where(['reset_key'=>$key,'user_type'=>2,'status'=>3])->first();
    	if(!empty($checkKey))
    	{
            $currentDate   = date('Y-m-d H:i:s');
            $otpCreated    = $checkKey->otp_created;
            $userId        = $checkKey->id;
            $expiredtime   = strtotime($currentDate)-strtotime($otpCreated);
            if($expiredtime>600){ // more then 10 mins
                $mobile_number = $checkKey->mobile_number;
                $characters    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $string        = str_shuffle($characters);
                $otpCode       = str_shuffle($string);
                $otpCode       = substr($otpCode, 0,6);
                $message       = "Dear Customer, Your SAKTHI RICE new account verification OTP code is ".$otpCode;
                User::where(['id'=>$userId])->update(['otp_code'=>$otpCode,'otp_created'=>$currentDate]);
                sendSms(urlencode($message),$mobile_number);
            }

    		if(isset($_POST['_token']))
        	{
            	$validator = Validator::make($request->all(), [
                    'request_type'=>'required',
                    'key'=>'required'
            	]);


                $validator->after(function ($validator) {
                if($_POST['request_type']!="" && $_POST['request_type']!="")
                {
                    $type       = trim($_POST['request_type']);
                    $key        = trim($_POST['key']);
                    $otp_code   = (isset($_POST['otp_code']) && $_POST['otp_code']!="") ? trim($_POST['otp_code']) : "";
                    $check = User::where(['reset_key'=>$key,'user_type'=>2,'status'=>3])->first();
                    if(empty($check)){
                        $validator->errors()->add('otp_code', 'Encryption key is invalid. try again.');
                    }else{
                        if($type==1){ // Verification
                            if($otp_code==""){
                                $validator->errors()->add('otp_code', 'OTP Code is required.');
                            }
                        }else{ // Resend
                            $currentDate   = date('Y-m-d H:i:s');
                            $otpCreated    = $check->otp_created;
                            $expiredtime   = strtotime($currentDate)-strtotime($otpCreated);
                            if($expiredtime<=300){ // 5 mins before request
                                $validator->errors()->add('otp_code', 'Previous otp is not expired.');
                            }
                        }
                    }
                }
                });


            	if($validator->passes())
            	{
                    if($request->request_type==1){ // Verification
                        if($request->otp_code==$checkKey->otp_code)
                        {
                            $checkKey->status = 1;
                            $checkKey->reset_key = null;
                            $checkKey->otp_code = null;
                            $checkKey->mobile_verified = 1;
                            $checkKey->save();
                            $redirectUrl = url('login');
                            Session::flash('success', 'Otp code verified successfully');
                            return response()->json(['success'=>'Otp code verified successfully','redirectUrl'=>$redirectUrl]);
                        }
                        else
                        {
                            return response()->json(['error'=>'Invalid Otp. try again.']);
                        }
                    }else{ // Resend otp
                        $mobile_number = $checkKey->mobile_number;
                        $characters    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        $string        = str_shuffle($characters);
                        $otpCode       = str_shuffle($string);
                        $otpCode       = substr($otpCode, 0,6);
                        $message       = "Dear Customer, Your SAKTHI RICE new account verification OTP code is ".$otpCode;
                        User::where(['id'=>$userId])->update(['otp_code'=>$otpCode,'otp_created'=>$currentDate]);
                        sendSms(urlencode($message),$mobile_number);
                        return response()->json(['success'=>'New otp code send to your registered mobile number.']);
                    }
            	}
            	else
            	{
                	return response()->json(['errors'=>$validator->errors()->all()]);
            	}
            }
    		return view('site.otp-verification',['data'=>$checkKey,'key'=>$key]);
    	}
    	else
    	{
    		return redirect('/login');
    	}
    }

    public function ContactUs()
    {
        return view('site.contact-us');
    }

    public function ContactusSave(Request $request)
    {
        if($request->ajax() && $request->_token)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required:string',
                'email' => 'required|email',
                'subject' => 'required:min:6',
                'message' => 'required|min:6',
            ]);

            if($validator->passes())
            {
                $model              = new ContactusRequests;
                $model->name        = $request->name;
                $model->email_id    = $request->email;
                $model->subject     = $request->subject;
                $model->message     = $request->message;
                $model->date_time   = date('Y-m-d H:i:s');
                if($model->save())
                {
                    return response()->json(['success'=>'Your request is received successfully. We will contact you soon.']);
                }
                else
                {
                    return response()->json(['error'=>'Server busy. try again after some time.']);
                }
            }
            else
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
        }
        else
        {
            return redirect('contact-us');
        }
    }

    public function RiceBenefits()
    {
        $RiceBenefits = RiceBenefits::where('status',1)->get();
        return view('site.rice-benefits',['RiceBenefits'=>$RiceBenefits]);
    }

    public function RiceDetails()
    {
        return view('site.rice-details');
    }

    public function Gallery()
    {
        $Gallery = Gallery::where('status',1)->get();
        return view('site.gallery',['Gallery'=>$Gallery]);
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
                $checkMobile   = User::where(['mobile_number'=>$request->mobile_number,'user_type'=>2])->first();
                if(empty($checkMobile)){
                    return response()->json(['error'=>'Invalid Mobile number. try again.']);
                }else if($checkMobile->status!==1){
                    return response()->json(['error'=>'Sorry your account is not active now. Please contact our admin.']);
                }else{
                    $redirectUrl  = url('login');
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
        return view('site.forgot-password');
    }


    public function __construct()
    {
        $this->middleware('user_auth')->except(['Index','ContactUs','ContactusSave','RiceBenefits','RiceDetails','Gallery']);
    }
}
