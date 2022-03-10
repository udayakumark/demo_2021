<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\Bag;
use App\Models\Rice;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class RiceController extends Controller {
    public function Index() {
        return view('admin.rice.Index');
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $model  = Rice::select('*');
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
				$bag_type_search = "";
				$bagDetailsSearch 	= Bag::where('name',$name)->first();
				if(isset($bagDetailsSearch)) {
					$bag_type_search = $bagDetailsSearch->id;
				}
                $q->where('original_name','like','%'.$name.'%')->orWhere('rice_types.original_name','like','%'.$name.'%');
				$q->where('bag_type','like','%'.$bag_type_search.'%')->orWhere('rice_types.bag_type','like','%'.$bag_type_search.'%');
				$q->where('duplicate_name','like','%'.$name.'%')->orWhere('rice_types.duplicate_name','like','%'.$name.'%');
				
            });
        }
        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                if(isset($user->original_name)){
                    $original_name = $user->original_name;
                }
				if(isset($user->bag_type)){
                    $bagDetails 	= Bag::where('id',$user->bag_type)->first();
                    $bag_type = $bagDetails->name;
                }
				if(isset($user->dealers_price)){
                    $dealers_price = $user->dealers_price;
                }
				if(isset($user->customers_price)){
                    $customers_price = $user->customers_price;
                }
				if(isset($user->onlinesales_price)){
                    $onlinesales_price = $user->onlinesales_price;
                }
                $result[$index]['snumber'] = $start+1;
                $result[$index]['original_name'] = $original_name;
                $result[$index]['bag_type'] = $bag_type;
                $result[$index]['dealers_price'] = $dealers_price;
                $result[$index]['customers_price'] = $customers_price;
                $result[$index]['onlinesales_price'] = $onlinesales_price;
                // action buttons
                $action = '<a href="'.url('/admin/view-rice/'.$user->id).'" title="View" class="btn btn-icon btn-sm btn-info view-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/update-rice/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-rice').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-rice';
        $redirectUrl  = '/admin/rice';
        $actionName   = 'Create';
        $type 		  = 1;
        $BagTypesList  = Bag::where('status', 1)->orderBy('id')->get();
        $model 			= new Rice;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'original_name' => 'required|string',
                'bag_type' => 'required|numeric',
                'duplicate_name' => 'required|string',
                'dealers_price' => 'required|numeric|not_in:0',
                'customers_price' => 'required|numeric|not_in:0',
                'onlinesales_price' => 'required|numeric|not_in:0'
            ]);
            if($validator->passes()) {
				$riceTypeIsExists = Rice::select('original_name')->where('original_name',$request->original_name)->where('bag_type',$request->bag_type)->exists();
				if($riceTypeIsExists) {
					return response()->json(['singleerror'=>'Rice type already exists/disabled. Please choose a different name.']);
				} else {
					$model->original_name        			=     $request->original_name;
					$model->bag_type        				=     $request->bag_type;
					$model->duplicate_name        			=     $request->duplicate_name;
					$model->dealers_price        			=     $request->dealers_price;
					$model->customers_price        			=     $request->customers_price;
					$model->onlinesales_price        		=     $request->onlinesales_price;
					$model->status            			= 1;
					if($model->save()) {
						return response()->json(['success'=>'New Rice type is created successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to add new purchase item. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.rice.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'bag_type'=>$model->bag_type,'BagTypesList'=>$BagTypesList]);
    }
	// View
    public function View($id=null,Request $request) {
        $actionUrl    = '/admin/update-rice/'.$id;
        $redirectUrl  = '/admin/rice';
        $actionName   = 'View';
        $type 	      = 3;
        $model        = Rice::where('status', 1)->where('id',$id)->first();
		if(isset($model->bag_type)){
            $bagDetails 	= Bag::where('id',$model->bag_type)->first();
            $bag_type = $bagDetails->name;
        }
        return view('admin.rice.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'bag_type'=>$bag_type]);
    }
    // Update
    public function Update($id=null,Request $request) {
        $actionUrl    = '/admin/update-rice/'.$id;
        $redirectUrl  = '/admin/rice';
        $actionName   = 'Update';
        $type 	      = 2;
		$BagTypesList  = Bag::where('status', 1)->orderBy('id')->get();
        $model        = Rice::where('status', 1)->where('id',$id)->first();
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'original_name' => 'required|string',
                'bag_type' => 'required|numeric',
                'duplicate_name' => 'required|string',
                'dealers_price' => 'required|numeric|not_in:0',
                'customers_price' => 'required|numeric|not_in:0',
                'onlinesales_price' => 'required|numeric|not_in:0'
            ]);
            if($validator->passes()) {
				$riceTypeIsExists = Rice::select('original_name')->where('original_name',$request->original_name)->where('bag_type',$request->bag_type)->where('id','!=',$id)->exists();
				if($riceTypeIsExists) {
					return response()->json(['singleerror'=>'Rice type already exists/disabled. Please choose a different name.']);
				} else {
					$model->original_name        			=     $request->original_name;
					$model->bag_type        				=     $request->bag_type;
					$model->duplicate_name        			=     $request->duplicate_name;
					$model->dealers_price        			=     $request->dealers_price;
					$model->customers_price        			=     $request->customers_price;
					$model->onlinesales_price        		=     $request->onlinesales_price;
					$model->status            				= 1;
					if($model->save()) {
						return response()->json(['success'=>'Rice type details is updated successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to update warehouse. try again after sometime.']);
					}
				}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.rice.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type,'bag_type'=>$model->bag_type,'BagTypesList'=>$BagTypesList]);
    }
    // Delete
    public function Delete(Request $request){
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = Rice::where('status', 1)->where('id',$id)->first();
        if(!empty($model)) {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Rice type details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}