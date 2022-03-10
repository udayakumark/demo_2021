<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\VendorType;
use App\Models\HSNCode;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class HSNCodeController extends Controller {
    public function Index() {
        $heading  = 'Manage HSN Codes';
		$actionUrl    = url('admin/create-hsncode');
		$redirectUrl  = url('admin/hsncode-list');
        return view('admin.hsncode.Index',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $model  = HSNCode::select('*');
        $model->where('status','!=',0);
        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        $name = "";
        if(isset($filterArray['name'])){
            $name = trim($filterArray['name']);
        }
        // Filter Conditions
        if($name!=""){
            $model->where(function($q) use ($name){
                $q->where('name','like','%'.$name.'%')->orWhere('hsncode.name','like','%'.$name.'%');
            });
        }
        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                if(isset($user->name)){
                    $name = $user->name;
                }
				if(isset($user->type)){
                    $VendorTypeGet 	= VendorType::where('id',$user->type)->first();
                    $VendorType = $VendorTypeGet->name;
                }
                $result[$index]['snumber'] = $start+1;
                $result[$index]['name'] = $name;
                $result[$index]['type'] = $VendorType;
                // action buttons
                $action = '<a href="'.url('/admin/update-hsncode/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-hsncode').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
    public function Create(Request $request) {
		$heading  = 'HSN Code';
        $actionUrl    = '/admin/create-hsncode';
        $redirectUrl  = '/admin/hsncode';
        $actionName   = 'Create';
        $userPincodeList = [];
		$VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $model 			= new HSNCode;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'type' => 'required|string'
            ]);
            if($validator->passes()) {
				$hsncodeTypeIsExists = HSNCode::select('name')->where('name',$request->name)->exists();
				if($hsncodeTypeIsExists) {
					return response()->json(['singleerror'=>'HSNCode type already exists. Please choose a different name.']);
				} else {
					$model->type        			=     $request->type;
					$model->name        			=     $request->name;
					$model->status            			= 1;
					if($model->save()) {
						return response()->json(['success'=>'New HSN Code is created successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to add new HSN Code. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.hsncode.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'hsncode_type'=>$model->type,'heading'=>$heading,'VendorTypeList'=>$VendorTypeList]);
    }
    // Update
    public function Update($id=null,Request $request) {
		$heading  = 'HSN Code';
        $actionUrl    = '/admin/update-hsncode/'.$id;
        $redirectUrl  = '/admin/hsncode';
        $actionName   = 'Update';
		$VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $model        = HSNCode::where('status', 1)->where('id',$id)->first();
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
				 'type' => 'required|string'
            ]);
            if($validator->passes()){
				$hsncodeTypeIsExists = HSNCode::select('name')->where('name',$request->name)->where('id','!=',$id)->exists();
				if($hsncodeTypeIsExists) {
					return response()->json(['singleerror'=>'HSNCode already exists. Please choose a different name.']);
				} else {
					$model->type        			= $request->type;
					$model->name       			    = $request->name;
					$model->status            		= 1;
					if($model->save()) {
						return response()->json(['success'=>'HSN Code details is updated successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to update HSN Code details. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.hsncode.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'hsncode_type'=>$model->type,'heading'=>$heading,'VendorTypeList'=>$VendorTypeList]);
    }
    // Delete
    public function Delete(Request $request){
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = HSNCode::where('status', 1)->where('id',$id)->first();
        if(!empty($model)) {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'HSN Code details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}