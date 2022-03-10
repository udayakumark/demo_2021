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
use App\Models\Paddy;
use App\Models\Cashbook;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class CashbookController extends Controller
{
    public function Index()
    {
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();

        $result = array();

        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }

        return view('admin.cashbook.Index',['vendorlist'=>$result]);
    }

    // Ajax data load into table
    public function List(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        $model  = Cashbook::select('*');
        $model->where('status','!=',0);

        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $name  	= "";

                if(isset($user->user_id)){

                    $user_details =  UserDetails::where('user_id', $user->user_id)->first();
                    $name = $user_details->first_name." ".$user_details->last_name;
                }

                $result[$index]['snumber'] = $start+1;
                $result[$index]['name'] = $name;
                $result[$index]['mobile_number'] = $user->type." - ".$user->amount;
                $result[$index]['email_id'] = $user->amount;
                $result[$index]['address'] = $user->date_time;

                // action buttons
                $action = '<a href="'.url('/admin/update-cashbook/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-cashbook').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-cashbook';
        $redirectUrl  = '/admin/cashbook';
        $actionName   = 'Create';
        $type 		  = 1;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status', 1)->orderBy('id')->get();
        $userPincodeList = [];

        $model 			= new Cashbook;

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|string',
                'type' => 'required|string',
                'date_time' => 'required|string',
                'amount' => 'required|string',
            ]);


            if($validator->passes())
            {


                $model->user_id        			=     $request->user_id;
                $model->type        			=     $request->type;
                $model->date_time        			=     $request->date_time;
                $model->amount        			=     $request->amount;
                $model->status            			= 1;

                if($model->save())
                {

                    return response()->json(['success'=>'New Cashbook Created Successfully.']);

                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new Cashbook. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }

        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();

        $result = array();

        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }

        return view('admin.cashbook.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type,'vendorlist'=>$result]);
    }

    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-cashbook/'.$id;
        $redirectUrl  = '/admin/cashbook';
        $actionName   = 'Update';
        $type 	      = 2;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status','!=',0)->get();

        $model        = Cashbook::where('status', 1)->where('id',$id)->first();

        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();

        $result = array();

        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|string',
                'type' => 'required|string',
                'date_time' => 'required|string',
                'amount' => 'required|string',
            ]);


            if($validator->passes())
            {


                $model->user_id        			=     $request->user_id;
                $model->type        			=     $request->type;
                $model->date_time        			=     $request->date_time;
                $model->amount        			=     $request->amount;
                $model->status            			= 1;
                if($model->save())
                {

                    return response()->json(['success'=>'Cashbook Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update Cashbook. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.cashbook.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'pincodeList'=>$pincodeList,'type'=>$type,'vendorlist'=>$result]);
    }

    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->id) ? $request->id : 0;
        $model        = Cashbook::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Cashbook Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }

}
