<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VendorType;
use App\Models\Cities;
use App\Models\Pincodes;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class VendorController extends Controller
{
    public function Index()
    {
        return view('admin.vendors.index');
    }

    // Ajax data load into table
    public function List(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        $model  = User::select('users.*');
        $model->join('user_details', 'user_details.user_id', '=', 'users.id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);

        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        $full_name = "";
        $mobile_no = "";
        $email_id  = "";
        $address   = "";
        if(isset($filterArray['full_name'])){
            $full_name = trim($filterArray['full_name']);
        }
        if(isset($filterArray['mobile_no'])){
            $mobile_no = trim($filterArray['mobile_no']);
        }
        if(isset($filterArray['email_id'])){
            $email_id = trim($filterArray['email_id']);
        }
        if(isset($filterArray['address'])){
            $address = trim($filterArray['address']);
        }

        // Filter Conditions
        if($full_name!=""){
            $model->where(function($q) use ($full_name){
                $q->where('user_details.first_name','like','%'.$full_name.'%')->orWhere('user_details.last_name','like','%'.$full_name.'%');
            });
        }
        if($mobile_no!=""){
            $model->where('users.mobile_number','like','%'.$mobile_no.'%');
        }
        if($email_id!=""){
            $model->where('users.email_id','like','%'.$email_id.'%');
        }
        if($address!=""){
            $model->where('user_details.address','like','%'.$address.'%');
        }

        // Get data with pagination
        $model->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $full_name  	= "";
                $address    	= "";
                $date_time 		= "";
                $profile_image 	= "";
                if(isset($user->userDetails->first_name)){
                    $full_name = $user->userDetails->first_name.' '.$user->userDetails->last_name;
                }
                if(isset($user->userDetails->address)){
                    $address = $user->userDetails->address;
                }
                if(isset($user->date_time) && $user->date_time!="")
                {
                    $date_time = date('d-m-Y h:i a',strtotime($user->date_time));
                }
                if(isset($user->userDetails->profile_image) && $user->userDetails->profile_image!="")
                {
                    $imagePath = public_path($user->userDetails->profile_image);
                    $imageLink = url('public/'.$user->userDetails->profile_image);
                    if(file_exists($imagePath))
                    {
                        $profile_image = '<img src="'.$imageLink.'" class="grid-image">';
                    }
                }
                $result[$index]['snumber'] = $start+1;
                $result[$index]['dealer_name'] = $full_name;
                $result[$index]['mobile_number'] = $user->mobile_number;
                $result[$index]['address'] = $address;
                $result[$index]['profile_image'] = $profile_image;
                $result[$index]['date_time'] = $date_time;

                // action buttons
                $action = '<a href="'.url('/admin/update-vendor/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/vendor-dealer').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
                $result[$index]['action'] = $action;
                $start++;
            }
        }

        $response = array(
            "draw" => intval($request->draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $result
        );
        return response()->json($response);
    }

    // Create
    public function Create(Request $request)
    {
        $actionUrl    = '/admin/create-vendor';
        $redirectUrl  = '/admin/vendors';
        $actionName   = 'Create';
        $type 		  = 1;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status', 1)->orderBy('id')->get();
        $VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $userPincodeList = [];

        $model 			= new User;
        $usermodel 		= new UserDetails;
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email_id' => 'required|email|unique:users',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'user_name' => 'required|string|unique:users',
                'password' => 'required|string',
                'type' => 'required|numeric',
                'city' => 'required|numeric',
                'pincode' => 'required|numeric|digits:6',
                'address'=>'required|string'
            ]);


            if($validator->passes())
            {
                $ProfileImagePath = "";
                $aadharImagePath = "";
                if(isset($request->profile_image) && !empty($request->profile_image))
                {
                    $ProfileImage        = "Vendor".time().'.'.$request->profile_image->extension();
                    $ProfileImagePath    = '/uploads/profile_images/'.$ProfileImage;
                    $request->profile_image->move(public_path('uploads/profile_images'), $ProfileImage);
                }

                if(isset($request->aadhar_image) && !empty($request->aadhar_image))
                {
                    $aadhar_image        = "aadhar".time().'.'.$request->aadhar_image->extension();
                    $aadharImagePath    = '/uploads/aadhar_images/'.$aadhar_image;
                    $request->aadhar_image->move(public_path('uploads/aadhar_images'), $aadhar_image);
                }

                $model->user_name        			= $request->user_name;
                $model->email_id       			    = $request->email_id;
                $model->mobile_number       		= $request->mobile_number;
                $model->password                  	= Hash::make($request->password);
                $model->user_type            	    = 4;
                $model->mobile_verified         	= 1;
                $model->email_verified            	= 1;
                $model->status            			= 1;
                $model->date_time           		= date('Y-m-d H:i:s');
                if($model->save())
                {
                    $countryId  = 0;
                    $stateId    = 0;
                    $cityId     = $request->city;
                    $getCity 	= Cities::where('id',$cityId)->first();
                    if(!empty($getCity)){
                        $countryId  = $getCity->country_id;
                        $stateId    = $getCity->state_id;
                    }


                    $usermodel->user_id 			= $model->id;
                    $usermodel->first_name          = $request->first_name;
                    $usermodel->last_name           = $request->last_name;
                    $usermodel->dob          		= $request->dob;
                    $usermodel->country_id          = $countryId;
                    $usermodel->state_id            = $stateId;
                    $usermodel->city_id             = $cityId;
                    $usermodel->pincode             = $request->pincode;
                    $usermodel->address             = $request->address;
                    $usermodel->profile_image       = $ProfileImagePath;
                    $usermodel->aadhar_image        = $aadharImagePath;
                    $usermodel->aadhar              = $request->aadhar;
                    $usermodel->type              = $request->type;
                    $usermodel->status              = 1;
                    $usermodel->date_time           = date('Y-m-d H:i:s');
                    $usermodel->save();
                    return response()->json(['success'=>'New Vendor Created Successfully.']);

                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new Vendor. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.vendors.Form',['VendorTypeList'=>$VendorTypeList,'model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-vendor/'.$id;
        $redirectUrl  = '/admin/vendors';
        $actionName   = 'Update';
        $type 	      = 2;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status','!=',0)->get();

        $userPincodes = SupplierPincodes::where('user_id',$id)->where('status','!=',0)->get();
        $userPincodeList = [];
        if(!empty($userPincodes)){
            foreach ($userPincodes as $pincode) {
                array_push($userPincodeList, $pincode->pincode_id);
            }
        }

        $model        = User::where('status', 1)->where('user_type',4)->where('id',$id)->first();
        if(empty($model)){
            return response()->view('admin.ExceptionHandler.404');
        }else{
            $usermodel    = UserDetails::where('user_id', $id)->first();
        }

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email_id' => 'required|email|unique:users,email_id,'.$id,
                'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number,'.$id,
                //'dob' => 'required|date',
                'city' => 'required|numeric',
                'pincode' => 'required|numeric|digits:6',
                'address'=>'required|string',
                'profile_image'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if($validator->passes())
            {
                if($request->profile_image && $request->profile_image!="")
                {
                    $ProfileImage        = "Vendor".time().'.'.$request->profile_image->extension();
                    $ProfileImagePath    = '/uploads/profile_images/'.$ProfileImage;
                    $request->profile_image->move(public_path('uploads/profile_images'), $ProfileImage);
                    $usermodel->profile_image       	= $ProfileImagePath;
                }

                if(isset($request->aadhar_image) && !empty($request->aadhar_image))
                {
                    $aadhar_image        = "Vendor".time().'.'.$request->aadhar_image->extension();
                    $aadharImagePath    = '/uploads/aadhar_images/'.$aadhar_image;
                    $request->aadhar_image->move(public_path('uploads/aadhar_images'), $aadhar_image);
                    $usermodel->aadhar_image       	= $aadharImagePath;
                }

                $model->email_id       			    = $request->email_id;
                $model->mobile_number       		= $request->mobile_number;
                if($model->save())
                {
                    $cityId     = $request->city;
                    $getCity 	= Cities::where('id',$cityId)->first();
                    if(!empty($getCity)){
                        $usermodel->country_id      = $getCity->country_id;
                        $usermodel->state_id        = $getCity->state_id;
                    }



                    $usermodel->first_name          = $request->first_name;
                    $usermodel->last_name           = $request->last_name;
                    $usermodel->dob          		= $request->dob;
                    $usermodel->city_id             = $cityId;
                    $usermodel->pincode             = $request->pincode;
                    $usermodel->address             = $request->address;
                    $usermodel->aadhar              = $request->aadhar;
                    $usermodel->save();
                    return response()->json(['success'=>'Vendor Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update Vendor. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.vendors.Form',['model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = User::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Vendor Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }

}
