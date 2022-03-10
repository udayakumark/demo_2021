<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\Bag;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class BagController extends Controller {
    public function Index() {
        return view('admin.bag.Index');
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $model  = Bag::select('*');
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
                $q->where('name','like','%'.$name.'%')->orWhere('bag_types.name','like','%'.$name.'%');
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
                $action = '<a href="'.url('/admin/update-bag/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-bag').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-bag';
        $redirectUrl  = '/admin/bag';
        $actionName   = 'Create';
        $type 		  = 1;
        $userPincodeList = [];
        $model 			= new Bag;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);
            if($validator->passes()) {
				$bagIsExists = Bag::select('name')->where('name',$request->name)->exists();
				if($bagIsExists) {
					return response()->json(['singleerror'=>'Bag type already exists. Please choose a different name.']);
				} else {
					$model->name        			= $request->name;
					$model->status            		= 1;
					if($model->save()) {
						return response()->json(['success'=>'New Bag type is created successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to add new purchase item. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.bag.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'bag_type'=>$model->type]);
    }
    // Update
    public function Update($id=null,Request $request) {
        $actionUrl    = '/admin/update-bag/'.$id;
        $redirectUrl  = '/admin/bag';
        $actionName   = 'Update';
        $type 	      = 2;
        $model        = Bag::where('status', 1)->where('id',$id)->first();
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);
            if($validator->passes()){
				$bagIsExists = Bag::select('name')->where('name',$request->name)->where('id','!=',$id)->exists();
				if($bagIsExists) {
					return response()->json(['singleerror'=>'Bag type already exists. Please choose a different name.']);
				} else {
					$model->name       			    = $request->name;
					$model->status            		= 1;
					if($model->save()) {
						return response()->json(['success'=>'Bag type details is updated successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to update warehouse. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.bag.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'bag_type'=>$model->type]);
    }
    // Delete
    public function Delete(Request $request){
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = Bag::where('status', 1)->where('id',$id)->first();
        if(!empty($model)) {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Bag type details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}