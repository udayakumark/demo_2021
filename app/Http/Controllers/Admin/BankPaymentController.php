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
use App\Models\VendorType;
use App\Models\BankPayment;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class BankPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.bankpayment.Index');
    }

     // Ajax data load into table
     public function List(Request $request)
     {
         $start     = $request->start;
         $length    = $request->length;
         $model     = BankPayment::select('*');
         $model->where('status','!=',0);
 
         // Filters Parameters
         parse_str($_POST['formData'], $filterArray);
        //  $name = "";
        //  $mobile_no = "";
        //  $email_id  = "";
        //  $address   = "";
 
        //  if(isset($filterArray['full_name'])){
        //      $name = trim($filterArray['name']);
        //  }
 
        //  // Filter Conditions
        //  if($name!=""){
        //      $model->where(function($q) use ($name){
        //          $q->where('name','like','%'.$name.'%')->orWhere('user_details.last_name','like','%'.$name.'%');
        //      });
        //  }
 
         // Get data with pagination
         $totalRecords 	= count($model->get());
         $data    		= $model->offset($start)->limit($length)->get();
 
         $result = [];
         if(!empty($data)){
             foreach ($data as $index=>$payment) {
                 $payment_mode  	= "";
                 if(isset($payment->payment_mode)){
                     $payment_mode = $payment->payment_mode;
                 }
                 $result[$index]['snumber'] = $start+1;
                 $result[$index]['payment_mode'] = $payment_mode;
 
                 // action buttons
                 $action = '<a href="'.url('/admin/update-bank-payment/'.$payment->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                 $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$payment->id.'"   data-url="'.url('/admin/delete-bank-payment').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Create(Request $request)
    {
        $actionUrl    = '/admin/create-bank-payment';
        $redirectUrl  = '/admin/bank-payment';
        $actionName   = 'Create';
        $type 		  = 1;
        
        $model 			= new BankPayment;

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'payment_mode' => 'required|string'
            ]);


            if($validator->passes())
            {
                $model->payment_mode      =     $request->payment_mode;
                $model->status            = 1;

                if($model->save())
                {

                    return response()->json(['success'=>'New Bank Payment Created Successfully.']);

                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new Bank Payment. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.bankpayment.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-bank-payment/'.$id;
        $redirectUrl  = '/admin/bank-payment';
        $actionName   = 'Update';
        $type 	      = 2;
        
        $model        = BankPayment::where('status', 1)->where('id',$id)->first();

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'payment_mode' => 'required|string'
            ]);

            if($validator->passes())
            {
                $model->payment_mode    = $request->payment_mode;
             
                if($model->save())
                {
                    return response()->json(['success'=>'Bank Payment Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update bank payment. try again after sometime.']);
                }
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.bankpayment.Form',['model'=>$model,'action'=>$actionUrl,
        'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->id) ? $request->id : 0;
        $model      = BankPayment::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Bank Payment Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}
