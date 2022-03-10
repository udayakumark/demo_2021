<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\VendorType;
use App\Models\Paddy;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class PaddyController extends Controller {
    public function Index() {
        return view('admin.paddy.Index');
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $model  = Paddy::select('*');
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
                $q->where('name','like','%'.$name.'%')->orWhere('paddy_products.name','like','%'.$name.'%');
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
                    $UserDetails 	= VendorType::where('id',$user->type)->first();
                    $vendorname = $UserDetails->name;
                }
                $result[$index]['snumber'] = $start+1;
                $result[$index]['name'] = $name;
                $result[$index]['type'] = $vendorname;
                // action buttons
                $action = '<a href="'.url('/admin/update-paddy/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-paddy').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-paddy';
        $redirectUrl  = '/admin/paddy';
        $actionName   = 'Create';
        $type 		  = 1;
        $VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $userPincodeList = [];
        $model 			= new Paddy;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'type' => 'required'
            ]);
            if($validator->passes()) {
				$paddyTypeIsExists = Paddy::select('name')->where('name',$request->name)->exists();
				if($paddyTypeIsExists) {
					return response()->json(['singleerror'=>'Paddy type already exists. Please choose a different name.']);
				} else {
					$model->name        			=     $request->name;
					$model->type        			=     $request->type;
					$model->status            			= 1;
					if($model->save()) {
						return response()->json(['success'=>'New Paddy type is created successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to add new purchase item. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.paddy.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'VendorTypeList'=>$VendorTypeList,'type'=>$type,'paddy_type'=>$model->type]);
    }
    // Update
    public function Update($id=null,Request $request) {
        $actionUrl    = '/admin/update-paddy/'.$id;
        $redirectUrl  = '/admin/paddy';
        $actionName   = 'Update';
        $type 	      = 2;
		$VendorTypeList  = VendorType::where('status', 1)->orderBy('id')->get();
        $model        = Paddy::where('status', 1)->where('id',$id)->first();
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'type' => 'required'
            ]);
            if($validator->passes()){
				$paddyTypeIsExists = Paddy::select('name')->where('name',$request->name)->where('id','!=',$id)->exists();
				if($paddyTypeIsExists) {
					return response()->json(['singleerror'=>'Paddy type already exists. Please choose a different name.']);
				} else {
					$model->name       			    = $request->name;
					$model->type       			    = $request->type;
					$model->status            		= 1;
					if($model->save()) {
						return response()->json(['success'=>'Paddy type details is updated successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to update warehouse. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.paddy.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'VendorTypeList'=>$VendorTypeList,'type'=>$type,'paddy_type'=>$model->type]);
    }
    // Delete
    public function Delete(Request $request){
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = Paddy::where('status', 1)->where('id',$id)->first();
        if(!empty($model)) {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Paddy type details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}