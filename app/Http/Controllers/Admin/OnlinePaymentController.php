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
use App\Models\Bank;
use App\Models\OnlinePayment;
use App\Models\Paddy;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class OnlinePaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function Index()
    {
        return view('admin.onlinepayment.Index');        
    }

    // Ajax data load into table
    public function List(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        
        $model  = OnlinePayment::leftJoin('bank as A', 'A.id', '=', 'bank_mode_of_payment.bank_id')
        ->select('bank_mode_of_payment.*','A.bank_name')
        ->where('bank_mode_of_payment.status','!=',0);
    
    
        // Filters Parameters
        // parse_str($_POST['formData'], $filterArray);
        // $name = "";
        // $mobile_no = "";
        // $email_id  = "";
        // $address   = "";

        // if(isset($filterArray['name'])){
        //     $name = trim($filterArray['name']);
        // }

        // Filter Conditions
        // if($name!=""){
        //     $model->where(function($q) use ($name){
        //         $q->where('bank_name','like','%'.$name.'%')->orWhere('bank.account_name','like','%'.$name.'%');
        //     });
        // }

        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$paymentType) {
                $payment_type  	= "";
                $mobile_no  	= "";
                $bank_id 	    = "";

               
                if(isset($paymentType->payment_type)){
                    $payment_type = $paymentType->payment_type;
                }
                if(isset($paymentType->mobile_no)){
                    $mobile_no = $paymentType->mobile_no;
                }
                if(isset($paymentType->bank_id)){
                    $bank_id = $paymentType->bank_id;
                }
                if(isset($paymentType->bank_name)){
                    $bank_name = $paymentType->bank_name;
                }
                

                $result[$index]['snumber']      = $start+1;
                $result[$index]['payment_type'] = $payment_type;
                $result[$index]['mobile_no']    = $mobile_no;
                $result[$index]['bank_name']    = $bank_name;
                $result[$index]['bank_id']      = $bank_id;

                // action buttons
                $action = '<a href="'.url('/admin/update-online-payment/'.$paymentType->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                // $action .= '&nbsp;&nbsp; <a href="'.url('/admin/online-payment-view/'.$paymentType->id).'" title="View" class="btn btn-icon btn-sm btn-info view-button"><i class="fas fa-eye"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$paymentType->id.'"   data-url="'.url('/admin/delete-online-payment').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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

        $actionUrl    = '/admin/create-online-payment';
        $redirectUrl  = '/admin/online-payment';
        $actionName   = 'Create';
        $type 		  = 1;
        
        $bankList   = bank::where('status', 1)->orderBy('id')->get();
        $model 		= new OnlinePayment;
        
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'payment_type'  => 'required|string',
                'mobile_no'     => 'required|numeric',
                'bank_id'       => 'required|numeric',
                
            ]);

            if($validator->passes())
            {
                $model->payment_type    =     $request->payment_type;
                $model->mobile_no   	=     $request->mobile_no;
                $model->bank_id    		=     $request->bank_id;
                $model->date_time       =     date('Y-m-d', time());
                $model->status       	= '1';
        
                if($model->save())
                {
                    return response()->json(['success'=>'New Online payment details Created Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new Online payment details. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.onlinepayment.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName, 'type'=>$type, 'bankList'=>$bankList]);
    }

    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-online-payment/'.$id;
        $redirectUrl  = '/admin/online-payment';
        $actionName   = 'Update';
        $type 	      = 2;
        
        $bankList   = Bank::where('status','!=',0)->get();
        $model      = OnlinePayment::where('status', 1)->where('id',$id)->first();

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'payment_type'  => 'required|string|min:3|max:50',
                'mobile_no'     => 'required|numeric|min:10',
                'bank_id'       => 'required',
            ]);

            if($validator->passes())
            {
                $model->payment_type    =     $request->payment_type;
                $model->mobile_no   	=     $request->mobile_no;
                $model->bank_id    		=     $request->bank_id;
                $model->date_time       =     date('Y-m-d', time());
                $model->status       	=   '1';

                if($model->save())
                {

                    return response()->json(['success'=>'Online payment Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update Online payment Details. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.onlinepayment.Form',['model'=>$model,'action'=>$actionUrl,
        'redirectUrl'=>$redirectUrl,'name'=>$actionName,'bankList'=>$bankList,'type'=>$type]);
    }


    // Ajax data load into table
    public function View($id=null)
    {
            $actionUrl    = '/admin/online-payment-view/';
            $redirectUrl  = '/admin/online-payment';
            $actionName   = 'View';
            $type 	      = 3;
            
            $model  = OnlinePayment::leftJoin('bank as A', 'A.id', '=', 'bank_mode_of_payment.bank_id')
                    ->select('bank_mode_of_payment.*','A.bank_name')
                    ->where('bank_mode_of_payment.status','!=',0);
                    
            // $model        = OnlinePayment::where('status', 1)->where('id',$id)->first();
            if(!empty($model))
            {
                return view('admin.onlinepayment.view',['model'=>$model,'action'=>$actionUrl,
                'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
            } else {
                return redirect('admin/online-payment');
            }
    }

    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->id) ? $request->id : 0;
        $model      = OnlinePayment::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Online Payment Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }

    
}
