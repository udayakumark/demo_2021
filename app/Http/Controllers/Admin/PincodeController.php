<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\Pincodes;
use App\Models\Cities;
use App\helpers;
use Validator;

class PincodeController extends Controller
{
    public function Index()
    {
        return view('admin.Pincodes.Index');
    } 

    // Ajax data load into table
    public function List(Request $request)
    {
    	$start 	= $request->start;
    	$length = $request->length;
    	$model  = Pincodes::select('pincodes.*');
    	$model->join('cities', 'pincodes.city_id', '=', 'cities.id');
    	$model->where('pincodes.status','!=',0);
    	$model->where('cities.flag','!=',0);

    	// Filters Parameters
    	parse_str($_POST['formData'], $filterArray);
    	$pincode 	= "";
    	$city       = "";
    	if(isset($filterArray['pincode'])){
    		$pincode = trim($filterArray['pincode']);
    	}
    	if(isset($filterArray['city'])){
    		$city = trim($filterArray['city']);
    	}

    	// Filter Conditions
    	if($pincode!=""){
    		$model->where('pincodes.pincode','like','%'.$pincode.'%');
    	}	
    	if($city!=""){
    		$model->where('cities.name','like','%'.$city.'%');
    	}	

    	// Get data with pagination
    	$model->orderBy('pincodes.id', 'asc');
    	$totalRecords 	= count($model->get());
    	$data    		= $model->offset($start)->limit($length)->get();

    	$result = [];
    	if(!empty($data)){
			foreach ($data as $index=>$pincodes) {
				$result[$index]['snumber'] = $start+1;
    			$result[$index]['city_name'] = $pincodes->city->name;
    			$result[$index]['pincode'] = $pincodes->pincode;
    			$result[$index]['date_time'] = $pincodes->date_time!="" ? date('d-m-Y h:i a',strtotime($pincodes->date_time)) : "";

    			$status    = ($pincodes->status==1) ? 'checked' : '';
    			$result[$index]['status'] = '<label class="custom-switch mt-2"><input type="checkbox" name="custom-switch-checkbox" data-url="'.url('/admin/changestatus-pincode').'" data-id="'.$pincodes->id.'" class="custom-switch-input pincode" '.$status.'><span class="custom-switch-indicator"></span></label>';

    			// action buttons
    			$action = '<button title="Edit" data-id="'.$pincodes->id.'" data-url="'.url('/admin/update-pincode').'" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></button>';
    			$action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$pincodes->id.'"   data-url="'.url('/admin/delete-pincode').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';

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
        $model 			= new pincodes;
        $cityList     	= Cities::where('flag', 1)->orderBy('name')->get();
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'city' => 'required|numeric',
            'pincode' => 'required|string',
            ]);

            $validator->after(function ($validator) {

            $pincodes = explode(',', $_POST['pincode']);
            if($_POST['pincode']!="" && count($pincodes)>0){
            	foreach ($pincodes as $pincode) {
            		$pincode = trim($pincode);
            		if(!is_numeric($pincode)){
            			$validator->errors()->add('pincode', 'Pincodes only allow numbers.');
            		}else if(strlen((string)$pincode)!==6){
            			$validator->errors()->add('pincode', 'Pincode '.$pincode.' must be 6 digits.');
            		}else{
            			$checkPincode    = Pincodes::where('pincode',$pincode)->where('status','!=',0)->get();
            			if(count($checkPincode)>0){
            				$validator->errors()->add('pincode', 'Pincode '.$pincode.' is already exists.');
            			}
            		}
            	}
            }

			});

            if($validator->passes()) 
            {
            	$pincodes = explode(',', $request->pincode);
            	foreach ($pincodes as $pincode) {
            		$model    			  = new Pincodes;
            		$model->country_id    = 101; //india
            		$model->state_id      = 4035; //tamilnadu
					$model->city_id       = $request->city;
                	$model->pincode       = $pincode;
                	$model->date_time     = date('Y-m-d H:i:s');
                	$model->save();
            	}
                return response()->json(['success'=>'Pincode Details Added Successfully.']);
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Pincodes.Form',['model'=>$model,'action'=>'/admin/create-pincode','name'=>'Create','cityList'=>$cityList]);
    } 

    // Update
    public function Update($id=null,Request $request)
    {
        $model 		    = Pincodes::find($id);
        $cityList     	= Cities::where('flag', 1)->orderBy('name')->get();
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'city' => 'required|numeric',
            'pincode' => 'required|numeric|digits:6',
            ]);

            $validator->after(function ($validator) {

            if($_POST['pincode']!=""){
            	$pincode = trim($_POST['pincode']);
            	$checkPincode   = Pincodes::where('pincode',$pincode)->where('status','!=',0)->get();
            	if(count($checkPincode)>0){
            		$validator->errors()->add('pincode', 'Pincode '.$pincode.' is already exists.');
            	}
            }

			});

            if($validator->passes()) 
            {
                $model->city_id       = $request->city;
                $model->pincode       = $request->pincode;
                $model->save();
                return response()->json(['success'=>'Pincode Details Updated Successfully.']);
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Pincodes.Form',['model'=>$model,'action'=>'/admin/update-pincode/'.$id,'name'=>'Update','cityList'=>$cityList]);
    } 

    // Delete
    public function Delete(Request $request)
    {
    	$id 		= isset($request->pincodeId) ? $request->pincodeId : 0;
    	$model    	= Pincodes::find($id);
    	if(!empty($model)){
    		$model->status       = 0;
    		$model->save();
    		return response()->json(['success'=>'Pincode Details Deleted Successfully']);
    	}else{
    		return response()->json(['error'=>'Invalid Request. Try Again.']);
    	}
    } 

    // Change Status
    public function ChangeStatus(Request $request)
    {
        $id         = isset($request->pincodeId) ? $request->pincodeId : 0;
        $model      = Pincodes::find($id);
        if(!empty($model)){
            $model->status       = $model->status==1 ? 2 : 1;
            $model->save();
            return response()->json(['success'=>'Pincode Status Changed Successfully']);
        }else{
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    } 

}
