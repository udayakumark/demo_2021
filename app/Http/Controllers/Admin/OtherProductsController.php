<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\OtherProducts;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class OtherProductsController extends Controller {
    public function Index() {
        return view('admin.otherproducts.Index');
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $model  = OtherProducts::select('*');
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
                $q->where('name','like','%'.$name.'%')->orWhere('otherproducts.name','like','%'.$name.'%');
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
                $result[$index]['snumber'] = $start+1;
                $result[$index]['name'] = $name;
                // action buttons
                $action = '<a href="'.url('/admin/update-otherproducts/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-otherproducts').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-otherproducts';
        $redirectUrl  = '/admin/otherproducts';
        $actionName   = 'Create';
        $type 		  = 1;
        $userPincodeList = [];
        $model 			= new OtherProducts;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);
            if($validator->passes()) {
				$otherproductsTypeIsExists = OtherProducts::select('name')->where('name',$request->name)->exists();
				if($otherproductsTypeIsExists) {
					return response()->json(['singleerror'=>'OtherProducts type already exists. Please choose a different name.']);
				} else {
					$model->name        			=     $request->name;
					$model->status            			= 1;
					if($model->save()) {
						return response()->json(['success'=>'New OtherProducts type is created successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to add new purchase item. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.otherproducts.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'otherproducts_type'=>$model->type]);
    }
    // Update
    public function Update($id=null,Request $request) {
        $actionUrl    = '/admin/update-otherproducts/'.$id;
        $redirectUrl  = '/admin/otherproducts';
        $actionName   = 'Update';
        $type 	      = 2;
        $model        = OtherProducts::where('status', 1)->where('id',$id)->first();
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);
            if($validator->passes()){
				$otherproductsTypeIsExists = OtherProducts::select('name')->where('name',$request->name)->where('id','!=',$id)->exists();
				if($otherproductsTypeIsExists) {
					return response()->json(['singleerror'=>'OtherProducts type already exists. Please choose a different name.']);
				} else {
					$model->name       			    = $request->name;
					$model->status            		= 1;
					if($model->save()) {
						return response()->json(['success'=>'OtherProducts type details is updated successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to update warehouse. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.otherproducts.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'otherproducts_type'=>$model->type]);
    }
    // Delete
    public function Delete(Request $request){
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = OtherProducts::where('status', 1)->where('id',$id)->first();
        if(!empty($model)) {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'OtherProducts type details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}