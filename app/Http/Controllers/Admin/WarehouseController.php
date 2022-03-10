<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Cities;
use App\Models\Pincodes;
use App\Models\Warehouses;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class WarehouseController extends Controller
{
    public function Index()
    {
        return view('admin.warehouses.index');
    }

    // Ajax data load into table
    public function List(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        $model  = Warehouses::select('*');
        $model->where('status','!=',0);

        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        $name = "";
        $mobile_no = "";
        $email_id  = "";
        $address   = "";
        
        if(isset($filterArray['full_name'])){
            $name = trim($filterArray['name']);
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
        if($name!=""){
            $model->where(function($q) use ($full_name){
                $q->where('name','like','%'.$name.'%')->orWhere('user_details.last_name','like','%'.$full_name.'%');
            });
        }
        if($mobile_no!=""){
            $model->where('mobile_number','like','%'.$mobile_no.'%');
        }
        if($email_id!=""){
            $model->where('email_id','like','%'.$email_id.'%');
        }
        if($address!=""){
            $model->where('address','like','%'.$address.'%');
        }

        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $full_name  	= "";
                $address    	= "";
                $date_time 		= "";
                $profile_image 	= "";
                if(isset($user->name)){
                    $name = $user->name;
                }
                if(isset($user->address)){
                    $address = $user->address;
                }
               
              
                $result[$index]['snumber'] = $start+1;
                $result[$index]['name'] = $name;
                $result[$index]['mobile_number'] = $user->mobile_number;
                $result[$index]['email_id'] = $user->email_id;
                $result[$index]['address'] = $user->address;

                // action buttons
                $action = '<a href="'.url('/admin/update-warehouse/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-warehouse').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-warehouse';
        $redirectUrl  = '/admin/warehouses';
        $actionName   = 'Create';
        $type 		  = 1;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status', 1)->orderBy('id')->get();
        $userPincodeList = [];

        $model 			= new Warehouses;
        
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'email_id' => 'required|email|unique:users',
                'mobile_number' => 'required|numeric|digits:10|unique:users',
                'address'=>'required|string'
            ]);
            

            if($validator->passes())
            {
            
               
                $model->name        			=     $request->first_name;
                $model->email_id       			    = $request->email_id;
                $model->mobile_number       		= $request->mobile_number;
                $model->address                  	= $request->address;
                $model->status            			= 1;
                
                if($model->save())
                {
                
                    return response()->json(['success'=>'New Warehouse Created Successfully.']);

                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new Warehouse. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.warehouses.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-warehouse/'.$id;
        $redirectUrl  = '/admin/warehouses';
        $actionName   = 'Update';
        $type 	      = 2;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status','!=',0)->get();

        $model        = Warehouses::where('status', 1)->where('id',$id)->first();
       
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number,'.$id,
                'address'=>'required|string'
            ]);

            if($validator->passes())
            {               
                $model->mobile_number   = $request->mobile_number;
                $model->name       		= $request->name;
                $model->address       	= $request->address;
                if($model->save())
                {
                
                    return response()->json(['success'=>'warehouse Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update warehouse. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.warehouses.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->id) ? $request->id : 0;
        $model        = Warehouses::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'warehouse Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }

}
