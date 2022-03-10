<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\VendorType;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class VendorController extends Controller
{
    public function Index()
    {
		$VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        return view('admin.vendors.index',['VendorTypeList'=>$VendorTypeList]);
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
        $vendorType   = "";
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
		if(isset($filterArray['vendorType'])){
            $vendorType = trim($filterArray['vendorType']);
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
		if($vendorType!=""){
            $model->where('user_details.account',$vendorType);
        }
        // Get data with pagination
        $model->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $full_name  	= "";
                $vendor_type    = "";
                $payment_type 	= "";
                $current_balance = "";
                if(isset($user->userDetails->first_name)){
                    $full_name = $user->userDetails->first_name.' '.$user->userDetails->last_name;
                }
				if($user->userDetails->account && $user->userDetails->account!="")
				{
					$VendorTypeList  = VendorType::where('id', $user->userDetails->account)->where('status', 1)->orderBy('id')->first();
					$vendor_type  = $VendorTypeList->name;
				}
				if($user->userDetails->type && $user->userDetails->type!="")
				{
					$payment_type  = ($user->userDetails->type=='C') ? 'Sundry creditor' : 'Sundry debitor';
				}
				if($user->userDetails->current_balance && $user->userDetails->current_balance!="")
				{
					$current_balance  = $user->userDetails->current_balance;
				}

                $result[$index]['snumber'] = $start+1;
                $result[$index]['dealer_name'] = $full_name;
                $result[$index]['mobile_number'] = $user->mobile_number;
                $result[$index]['vendor_type'] = $vendor_type;
                $result[$index]['payment_type'] = $payment_type;
                $result[$index]['current_balance'] = $current_balance;
                // action buttons
                $action = '<a href="'.url('/admin/view-vendor/'.$user->id).'" title="View" class="btn btn-icon btn-sm btn-info view-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/update-vendor/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-vendor').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $areaList     = array();
        $VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $model 			= new User;
        $usermodel 		= new UserDetails;
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'type' => 'required|string',
                'account' => 'required|numeric',
            ]);
			
            if($validator->passes())
            {
				$userDetailsIsExists = UserDetails::select('first_name')->where('first_name',$request->first_name)->exists();
				if($userDetailsIsExists) {
					return response()->json(['singleerror'=>'User Name already exists/disabled. Please choose a different name.']);
				} else {
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
					$model->user_name        			= $request->first_name;
					$model->email_id       			    = $request->email_id;
					$model->mobile_number       		= $request->mobile_number;
					$model->password                  	= Hash::make($request->mobile_number);
					$model->user_type            	    = 4;
					$model->mobile_verified         	= 1;
					$model->email_verified            	= 1;
					$model->status            			= 1;
					$model->date_time           		= date('Y-m-d H:i:s');
					if($model->save())
					{
						$usermodel->user_id 			= $model->id;
						$usermodel->first_name          = $request->first_name;
						$usermodel->state            	= $request->state;
						$usermodel->city             	= $request->city;
						$usermodel->area             	= $request->area;
						$usermodel->pincode             = $request->pincode;
						$usermodel->address             = $request->address;
						$usermodel->profile_image       = $ProfileImagePath;
						$usermodel->aadhar_image        = $aadharImagePath;
						$usermodel->aadhar              = $request->aadhar;
						$usermodel->type              	= $request->type;
						$usermodel->account             = $request->account;
						$usermodel->bank_name           = $request->bank_name;
						$usermodel->bank_branch         = $request->bank_branch;
						$usermodel->account_number      = $request->account_number;
						$usermodel->current_balance     = $request->current_balance;
						$usermodel->ifsc_code           = $request->ifsc_code;
						$usermodel->gst              	= $request->gst;
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
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.vendors.Form',['VendorTypeList'=>$VendorTypeList,'model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'areaList'=>$areaList,'type'=>$type]);
    }
	// View
    public function View($id=null,Request $request)
    {
        $actionUrl    = '/admin/view-vendor/'.$id;
        $redirectUrl  = '/admin/vendors';
        $actionName   = 'View';
        $type 	      = 3;
        $areaList     = array();
        $VendorTypeList  = '';
        $model        = User::where('status', 1)->where('user_type',4)->where('id',$id)->first();
        if(empty($model)){
            return response()->view('admin.ExceptionHandler.404');
        }else{
            $usermodel    = UserDetails::where('user_id', $id)->first();
        }
		if($usermodel->account && $usermodel->account!="")
		{
			$VendorTypeList  = VendorType::where('id', $usermodel->account)->where('status', 1)->orderBy('id')->first();
			$VendorTypeList  = $VendorTypeList->name;
		}
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
        return view('admin.vendors.Form',['VendorTypeList'=>$VendorTypeList,'model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'areaList'=>$areaList,'type'=>$type]);
    }
    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-vendor/'.$id;
        $redirectUrl  = '/admin/vendors';
        $actionName   = 'Update';
        $type 	      = 2;
        $VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $model        = User::where('status', 1)->where('user_type',4)->where('id',$id)->first();
        if(empty($model)){
            return response()->view('admin.ExceptionHandler.404');
        }else{
            $usermodel    = UserDetails::where('user_id', $id)->first();
        }
		$areaList = $this->getPincodedetailsSelf($usermodel->pincode);
		$areaList =  ($areaList['status']) ? $areaList['data']['area'] : array();
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                 'first_name' => 'required|string',
                 'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number,'.$id,
                 'type' => 'required|string',
                 'account' => 'required|numeric',
            ]);

            if($validator->passes())
            {
				$userDetailsIsExists = UserDetails::select('first_name')->where('first_name',$request->first_name)->where('user_id','!=',$id)->exists();
				if($userDetailsIsExists) {
					return response()->json(['singleerror'=>'User Name already exists/disabled. Please choose a different name.']);
				} else {
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
						$usermodel->first_name          = $request->first_name;
						$usermodel->account             = $request->account;
						$usermodel->type              	= $request->type;
						$usermodel->state            	= $request->state;
						$usermodel->city             	= $request->city;
						$usermodel->area             	= $request->area;
						$usermodel->pincode             = $request->pincode;
						$usermodel->address             = $request->address;
						$usermodel->aadhar              = $request->aadhar;
						$usermodel->bank_name           = $request->bank_name;
						$usermodel->bank_branch         = $request->bank_branch;
						$usermodel->account_number      = $request->account_number;
						$usermodel->current_balance     = $request->current_balance;
						$usermodel->ifsc_code           = $request->ifsc_code;
						$usermodel->gst              	= $request->gst;
						$usermodel->save();
						return response()->json(['success'=>'Vendor Details Updated Successfully.']);
					}
					else
					{
						return response()->json(['singleerror'=>'Failed to update Vendor. try again after sometime.']);
					}
				}
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.vendors.Form',['VendorTypeList'=>$VendorTypeList,'model'=>$model,'usermodel'=>$usermodel,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'areaList'=>$areaList,'type'=>$type]);
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
	public function getPincodedetailsSelf($pin_code) {
		$returnData = array();
		$status=false;
		$message='';
        $url = 'https://api.postalpincode.in/pincode/'.$pin_code;
		$crl = curl_init();
		curl_setopt($crl, CURLOPT_URL, $url);
		curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);  
		curl_close($crl);
		if(isset($response) && !empty($response)) {
			$responseDecodeJson = json_decode($response, true);
			//print_r($responseDecodeJson);
			foreach ($responseDecodeJson as $resp) {
				if(isset($resp['PostOffice']) && !empty($resp['PostOffice'])){
				   $areaArr = array();
                   foreach($resp['PostOffice'] as $post) {
					   $areaArr[] = $post['Name'];
					   $returnData['city'] = $post['District'];
					   $returnData['state'] = $post['State'];
				   }
				   $returnData['area'] = $areaArr;
				   $status=true;
				} else {
					$message = 'No Area were found. Please try again later.';
				}
			}
		} else {
			$message = 'No Area were found. Please try again later.';
		}
		$finalReturnData = array('status'=>$status,'message'=>$message,'data'=>$returnData);
		return $finalReturnData;
    }
	public function getPincodedetails(Request $request) {
        $pin_code 	= $request->pin_code;
		$returnData = array();
		$status=false;
		$message='';
        $url = 'https://api.postalpincode.in/pincode/'.$pin_code;
		$crl = curl_init();
		curl_setopt($crl, CURLOPT_URL, $url);
		curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($crl);  
		curl_close($crl);
		if(isset($response) && !empty($response)) {
			$responseDecodeJson = json_decode($response, true);
			//print_r($responseDecodeJson);
			foreach ($responseDecodeJson as $resp) {
				if(isset($resp['PostOffice']) && !empty($resp['PostOffice'])){
				   $areaArr = array();
                   foreach($resp['PostOffice'] as $post) {
					   $areaArr[] = $post['Name'];
					   $returnData['city'] = $post['District'];
					   $returnData['state'] = $post['State'];
				   }
				   $returnData['area'] = $areaArr;
				   $status=true;
				} else {
					$message = 'No Area were found. Please try again later.';
				}
			}
		} else {
			$message = 'No Area were found. Please try again later.';
		}
		$finalReturnData = array('status'=>$status,'message'=>$message,'data'=>$returnData);
		return response()->json($finalReturnData);
    }
}
