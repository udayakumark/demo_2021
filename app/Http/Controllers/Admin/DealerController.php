<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Cities;
use App\Models\Pincodes;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class DealerController extends Controller
{
    public function Index()
    {
        return view('admin.Dealers.Index');
    } 

    // Ajax data load into table
    public function List(Request $request)
    {
    	$start 	= $request->start;
    	$length = $request->length;
    	$model  = User::select('users.*');
    	$model->join('user_details', 'user_details.user_id', '=', 'users.id');
    	$model->where('users.status','!=',0);
    	$model->where('users.user_type',3);

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
				$shop_name  	= "";
				$dealer_name  	= "";
				$full_name  	= "";
				$address    	= "";
				$date_time 		= "";
    			$profile_image 	= "";
				if(isset($user->userDetails->first_name)){
					$full_name = $user->userDetails->first_name.' '.$user->userDetails->last_name;
					$shop_name = $user->userDetails->first_name;
					$dealer_name = $user->userDetails->last_name;
				}
				if(isset($user->userDetails->address)){
					$address = $user->userDetails->address;
				}
				if(isset($user->date_time) && $user->date_time!="")
    			{    				
    				$date_time = date('d-m-Y',strtotime($user->date_time));
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
				$result[$index]['snumber']		= $start+1;
    			$result[$index]['shop_name'] 	= $shop_name;
    			$result[$index]['dealer_name'] 	= $dealer_name;
    			$result[$index]['mobile_number']= $user->mobile_number;
    			$result[$index]['address']		= $address;
    			$result[$index]['profile_image']= $profile_image;
    			$result[$index]['date_time'] 	= $date_time;

    			// action buttons
    			$action = '<a href="'.url('/admin/update-dealer/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
    			$action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-dealer').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
    	$actionUrl    = '/admin/create-dealer';
    	$redirectUrl  = '/admin/dealers';
    	$actionName   = 'Create';
    	$type 		  = 1;
    	$cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status', 1)->orderBy('id')->get();
        $userPincodeList = [];

        $model 			= new User;
        $usermodel 		= new UserDetails;
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            // 'email_id' => 'required|email|unique:users',
            'mobile_number' => 'required|numeric|digits:10|unique:users',
            'user_name' => 'required|string|unique:users',
            'password' => 'required|string',
            'dob' => 'required|date',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'address'=>'required|string',
            'profile_image'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if($validator->passes()) 
            {
            	$ProfileImage        = "Dealer".time().'.'.$request->profile_image->extension(); 
            	$ProfileImagePath    = '/uploads/profile_images/'.$ProfileImage; 
        		$request->profile_image->move(public_path('uploads/profile_images'), $ProfileImage);
                $model->user_name        			= $request->user_name;
                // $model->email_id       			    = $request->email_id;
                $model->mobile_number       		= $request->mobile_number;
                $model->password                  	= Hash::make($request->password);
                $model->user_type            	    = 3; //Dealer
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

                    if(isset($request->dealer_pincodes) && !empty($request->dealer_pincodes))
                    {
                        foreach ($request->dealer_pincodes as $pincode) 
                        {
                            $pincodeModel               = new SupplierPincodes;
                            $pincodeModel->user_id      = $model->id;
                            $pincodeModel->pincode_id   = $pincode;
                            $pincodeModel->date_time    = date('Y-m-d H:i:s');
                            $pincodeModel->save();
                        }
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
                	$usermodel->status              = 1;
                	$usermodel->date_time           = date('Y-m-d H:i:s');
                	$usermodel->save();
                	return response()->json(['success'=>'New Dealer Created Successfully.']);

                }
                else
                {
                	return response()->json(['singleerror'=>'Failed to add new Dealer. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Dealers.Form',['model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type]);
    } 

    // Update
    public function Update($id=null,Request $request)
    {
    	$actionUrl    = '/admin/update-dealer/'.$id;
    	$redirectUrl  = '/admin/dealers';
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

        $model        = User::where('status', 1)->where('user_type',3)->where('id',$id)->first();
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
            // 'email_id' => 'required|email|unique:users,email_id,'.$id,
            'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number,'.$id,
            'dob' => 'required|date',
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            'address'=>'required|string',
            'profile_image'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if($validator->passes()) 
            {
            	if($request->profile_image && $request->profile_image!="")
            	{
            		$ProfileImage        = "Dealer".time().'.'.$request->profile_image->extension(); 
            		$ProfileImagePath    = '/uploads/profile_images/'.$ProfileImage; 
        			$request->profile_image->move(public_path('uploads/profile_images'), $ProfileImage);
        			$usermodel->profile_image       	= $ProfileImagePath;
            	}

                // $model->email_id       			    = $request->email_id;
                $model->mobile_number       		= $request->mobile_number;
                if($model->save())
                {
                	$cityId     = $request->city;
                	$getCity 	= Cities::where('id',$cityId)->first();
                	if(!empty($getCity)){
                		$usermodel->country_id      = $getCity->country_id;
                		$usermodel->state_id        = $getCity->state_id;
                	}

                    if(isset($request->dealer_pincodes) && !empty($request->dealer_pincodes))
                    {
                        SupplierPincodes::where(['user_id'=>$model->id])->update(['status'=>0]);
                        foreach ($request->dealer_pincodes as $pincode) 
                        {
                            $checkPincode    = SupplierPincodes::where(['pincode_id'=>$pincode,'user_id'=>$model->id])->first();
                            if(empty($checkPincode)){
                                $pincodeModel               = new SupplierPincodes;
                                $pincodeModel->user_id      = $model->id;
                                $pincodeModel->pincode_id   = $pincode;
                                $pincodeModel->date_time    = date('Y-m-d H:i:s');
                                $pincodeModel->save();
                            }else{
                                $checkPincode->pincode_id   = $pincode;
                                $checkPincode->status       = 1;
                                $checkPincode->save();
                            }
                        }
                    }

                	$usermodel->first_name          = $request->first_name;
                	$usermodel->last_name           = $request->last_name;
                	$usermodel->dob          		= $request->dob;
                	$usermodel->city_id             = $cityId;
                	$usermodel->pincode             = $request->pincode;
                	$usermodel->address             = $request->address;
                	$usermodel->save();
                	return response()->json(['success'=>'Dealer Details Updated Successfully.']);
                }
                else
                {
                	return response()->json(['singleerror'=>'Failed to update Dealer. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Dealers.Form',['model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type]);
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
    		return response()->json(['success'=>'Dealer Details Deleted Successfully.']);
    	}
    	else
    	{
    		return response()->json(['error'=>'Invalid Request. Try Again.']);
    	}
    } 
}
