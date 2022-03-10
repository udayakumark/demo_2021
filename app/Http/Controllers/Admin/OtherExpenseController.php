<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\Bag;
use App\Models\TblBill;
use App\Models\BillPayment;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;
use Illuminate\Support\Facades\Auth;

class OtherExpenseController extends Controller {
    public function Index() {
        return view('admin.otherExpense.Index');
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $bill_type_id =20;
        $model  = TblBill::select('user_details.first_name','tbl_billing.bill_id', 'tbl_billing.inv_date', 'tbl_billing.comments','tbl_billing.total');
		$model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.user_id');
        $model->where('tbl_billing.bill_type_id',$bill_type_id);
        $model->where('tbl_billing.delete_flag',0)->where('tbl_billing.status',1);
        $model->orderBy('tbl_billing.created_at', 'desc');
		

        // $model  = TblBill::select('*');
        // $model->where('status','!=',0);
        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        // $name = "";
        // if(isset($filterArray['name'])){
        //     $name = trim($filterArray['name']);
        // }
        // Filter Conditions
        // if($name!=""){
        //     $model->where(function($q) use ($name){
        //         $q->where('name','like','%'.$name.'%')->orWhere('bag_types.name','like','%'.$name.'%');
        //     });
        // }
        // Get data with pagination
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        $total_expenses ='';
        if(!empty($data)){
            foreach ($data as $index=>$datas) {
                if(isset($datas->comments)){
                    $comments = $datas->comments;
                }
                // $total_expenses += $datas->total;
                $result[$index]['snumber'] = $start+1;
                $result[$index]['first_name'] = $datas->first_name;
                $result[$index]['inv_date'] = date('d-m-Y',strtotime($datas->inv_date)); 
                $result[$index]['comments'] = $comments;
                $result[$index]['total'] = $datas->total;
                // $result[$index]['total'] = '₹ '.number_format($datas->total,2);
                // action buttons
                $action = '<a href="'.url('/admin/update-other-expense/'.$datas->bill_id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$datas->bill_id.'"   data-url="'.url('/admin/delete-other-expense').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
                $result[$index]['action'] = $action;
                // $result[$index]['total_expenses'] = $total_expenses;
                $start++;
            }
        }
        $sql    = $model->toSql();
        // print_r($sql);exit('Hello');

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
        $actionUrl    = '/admin/create-other-expense';
        $redirectUrl  = '/admin/other-expense';
        $actionName   = 'Create';
        $bill_type_id = '20'; //Other Expense
        $payment_type = 3; // Cash payment
        $loginId  = Auth::guard('admin')->user()->id;

        $model 			= new TblBill;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'comments' => 'required|string',
                'cash_amount' => 'required'
            ]);
            if($validator->passes()) {				
					$model->bill_type_id          		= $bill_type_id;
					$model->user_id         		    = $loginId;
                    $model->inv_date        			= $request->billdate;
					$model->comments        			= $request->comments;
					$model->total        			    = $request->cash_amount;
					$model->payment_type     			= $payment_type;
					$model->status            		    = 1;

					if($model->save()) {
                        $bill_Payment    			 	= new BillPayment;
						$bill_Payment->bill_id			=	$model->bill_id;
                        $bill_Payment->cash_amount		=	$request->cash_amount;
						$bill_Payment->save();
                        
                        if($model && $bill_Payment) {
                            return response()->json(['success'=>'New Other Expense is created successfully.']);
                        } else {
                            return response()->json(['singleerror'=>'Failed to add other expense. try again after sometime.']);
                        }						
					}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.otherExpense.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName]);
    }
    // Update
    public function Update($id=null,Request $request) {
        $bill_id 	= $id;
        $actionUrl    = '/admin/update-other-expense/'.$id;
        $redirectUrl  = '/admin/other-expense';
        $actionName   = 'Update';
        $bill_type_id = '20'; //Other Expense
        $payment_type = 3; // Cash payment
        $loginId  = Auth::guard('admin')->user()->id;

        $model        = TblBill::where('status', 1)->where('bill_id',$id)->first();

        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'comments' => 'required|string',
                'cash_amount' => 'required'
            ]);
            if($validator->passes()){
					$model->bill_type_id          		= $bill_type_id;
					$model->user_id         		    = $loginId;
					$model->inv_date        			= $request->billdate;
					$model->comments        			= $request->comments;
					$model->total        			    = $request->cash_amount;
					$model->payment_type     			= $payment_type;
					$model->status            		    = 1;

					if($model->save()) {
                        BillPayment::where('bill_id', $bill_id)->delete();
                        $bill_Payment    			 	= new BillPayment;
						$bill_Payment->bill_id			=	$model->bill_id;
                        $bill_Payment->cash_amount		=	$request->cash_amount;
						$bill_Payment->save();
                        
                        if($model && $bill_Payment) {
                            return response()->json(['success'=>'Other Expense details is updated successfully.']);
                        } else {
                            return response()->json(['singleerror'=>'Failed to update Other Expense. try again after sometime.']);
                        }
				    }
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.otherExpense.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName]);
    }
    // Delete
    public function Delete(Request $request){
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = TblBill::where('status', 1)->where('bill_id',$id)->first();
        if(!empty($model)) {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Other expense details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}