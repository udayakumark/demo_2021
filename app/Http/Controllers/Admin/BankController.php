<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Cities;
use App\Models\Pincodes;
use App\Models\VendorType;
use App\Models\Bank;
use App\Models\Paddy;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class BankController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function __invoke(Request $request)
    // {
    //     //
    // }
    public function Index()
    {
        return view('admin.bank.Index');        
    }

    // Ajax data load into table
    public function List(Request $request)
    { 
        $start 	= $request->start;
        $length = $request->length;
        $sql = "";
       
        // $model  = Bank::select('bank.bank_name','bank.account_no', DB::raw('SUM(bp.cash_amount) + bank.current_balance as current_balance'));        
        $model  = Bank::select('bank.bank_name','bank.account_no', DB::raw('SUM(bp.cash_amount) as cash_balance'), 'bank.current_balance');        
        $model->leftJoin('bill_payment as bp', 'bp.bank_id', '=', 'bank.id');
        $model->where('bank.status','!=',0);
        $model->groupBy('bank.id');

        
        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        $name = "";
        // $mobile_no = "";
        // $email_id  = "";
        // $address   = "";

        if(isset($filterArray['name'])){
            $name = trim($filterArray['name']);
        }

        // Filter Conditions
        if($name!=""){
            $model->where(function($q) use ($name){
                $q->where('bank_name','like','%'.$name.'%')->orWhere('bank.account_name','like','%'.$name.'%');
            });
        }

        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $sql            = $model->toSql();
        // print_r($sql);exit('Hello');
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$bankData) {
                $bank_name  	= "";
                $account_name  	= "";
                $type 	        = "";
                $mobile_no      = "";
               
                if(isset($bankData->bank_name)){
                    $bank_name = $bankData->bank_name;
                }
                if(isset($bankData->account_name)){
                    $account_name = $bankData->account_name;
                }
                if(isset($bankData->type)){
                    $type = $bankData->type;
                }
                if(isset($bankData->mobile_no)){
                    $mobile_no = $bankData->mobile_no;
                }

                $result[$index]['snumber']      = $start+1;
                $result[$index]['bank_name']    = $bank_name;
                $result[$index]['account_name'] = $account_name;

                $result[$index]['account_no']   = $bankData->account_no;
                $result[$index]['ifsc']         = $bankData->ifsc;
                $result[$index]['mobile_no']    = $mobile_no;
                $result[$index]['branch']       = $bankData->branch;
                $result[$index]['current_balance'] = '₹ '.number_format($bankData->current_balance + $bankData->cash_balance, 2);
                $result[$index]['bank_address'] = $bankData->bank_address;
                // $result[$index]['status']    = $bankData->status;
                $result[$index]['type']         = $type;

                // action buttons
                $action = '<a href="'.url('/admin/update-bank/'.$bankData->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp; <a href="'.url('/admin/bank-view/'.$bankData->id).'" title="View" class="btn btn-icon btn-sm btn-info view-button"><i class="fas fa-eye"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$bankData->id.'"   data-url="'.url('/admin/delete-bank').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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

        $actionUrl    = '/admin/create-bank';
        $redirectUrl  = '/admin/bank';
        $actionName   = 'Create';
        $type 		  = 1;
       
        $model 			= new Bank;
        
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'bank_name'     => 'required|string',
                'account_name'  => 'required|string',
                'account_no'    => 'required|numeric',
                'ifsc'          => 'required',
                'branch'        => 'required|string',
                'type'          => 'required',
                'current_balance'=> 'required|numeric'
            ]);
            
            if($validator->passes())
            {
                $model->bank_name      			=     $request->bank_name;
                $model->account_name   			=     $request->account_name;
                $model->account_no    			=     $request->account_no;
                $model->ifsc        			=     $request->ifsc;
                $model->mobile_no    			=     $request->mobile_no;
                $model->branch        			=     $request->branch;
                $model->current_balance			=     $request->current_balance;
                $model->bank_address   			=     $request->bank_address;
                $model->type        			=     $request->type;
                $model->date_time        		=     date('Y-m-d', time());
                $model->status       			=    '1';
        
                if($model->save())
                {

                    return response()->json(['success'=>'New Bank details Created Successfully.']);

                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new bank details. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.bank.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName, 'type'=>$type]);
    }
    
    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-bank/'.$id;
        $redirectUrl  = '/admin/bank';
        $actionName   = 'Update';
        $type 	      = 2;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status','!=',0)->get();
        $model        = Bank::where('status', 1)->where('id',$id)->first();
                    
            if(isset($_POST['_token']))
            {
        

            $validator = Validator::make($request->all(), [
                'bank_name' => 'required|string|min:3|max:50',
                'account_name'  => 'required|string|min:3|max:50',
                'account_no'    => 'required|numeric|min:3',
                'ifsc'          => 'required',
                'branch'        => 'required|string',
                'type'          => 'required',
                'current_balance'=> 'required|numeric|min:1'
            ]);
            
            if($validator->passes())
            {
                $model->bank_name      			=     $request->bank_name;
                $model->account_name   			=     $request->account_name;
                $model->account_no    			=     $request->account_no;
                $model->ifsc        			=     $request->ifsc;
                $model->mobile_no        			=     $request->mobile_no;
                $model->branch        			=     $request->branch;
                $model->current_balance			=     $request->current_balance;
                $model->bank_address   			=     $request->bank_address;
                $model->type        			=     $request->type;
                $model->date_time        		=     date('Y-m-d', time());
                $model->status       			=   '1';

                if($model->save())
                {

                    return response()->json(['success'=>'Bank Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update Bank Details. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.bank.Form',['model'=>$model,'action'=>$actionUrl,
        'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,
        'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Ajax data load into table
    public function View($id=null)
    {
            $actionUrl    = '/admin/bank-view/';
            $redirectUrl  = '/admin/bank';
            $actionName   = 'View';
            $type 	      = 3;
            
            $model        = Bank::where('status', 1)->where('id',$id)->first();
            if(!empty($model))
            {
                return view('admin.bank.View',['model'=>$model,'action'=>$actionUrl,
                'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
            } else {
                return redirect('admin/bank');
            }
    }    
    
    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->id) ? $request->id : 0;
        $model        = Bank::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Bank Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }

}
