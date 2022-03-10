<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\OrderedProducts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\helpers;

class DealerorderController extends Controller
{
    public function Index()
    {
    	$usersList     = User::where('status','!=', 0)->where('user_type',2)->orderBy('id')->get();
    	$supplierList  = User::where('status','!=', 0)->where('user_type',3)->orderBy('id')->get();
        return view('dealer.Orders.Index',['usersList'=>$usersList,'supplierList'=>$supplierList]);
    } 

    // Ajax data load into table
    public function List(Request $request)
    {
    	$start 	  = $request->start;
    	$length   = $request->length;
    	$loginId  = Auth::guard('dealer')->user()->id;
    	$model    = Orders::where('status',1);
    	$model->where('supplier_id',$loginId);

    	// Filters Parameters
    	parse_str($_POST['formData'], $filterArray);
    	$user_id     		= "";
    	$supplier_id 		= "";
    	$order_id    		= "";
    	$payment_type  		= "";
    	$payment_status     = "";
    	$payment_reference  = "";
    	$order_status       = "";
    	$from_date 			= "";
    	$to_date 			= "";
    	if(isset($filterArray['user_id'])){
    		$user_id = trim($filterArray['user_id']);
    	}
    	if(isset($filterArray['order_id'])){
    		$order_id = trim($filterArray['order_id']);
    	}
    	if(isset($filterArray['payment_type'])){
    		$payment_type = trim($filterArray['payment_type']);
    	}
    	if(isset($filterArray['payment_status'])){
    		$payment_status = trim($filterArray['payment_status']);
    	}
    	if(isset($filterArray['payment_reference'])){
    		$payment_reference = trim($filterArray['payment_reference']);
    	}
    	if(isset($filterArray['order_status'])){
    		$order_status = trim($filterArray['order_status']);
    	}
    	if(isset($filterArray['from_date'])){
    		$from_date = trim($filterArray['from_date']);
    	}
    	if(isset($filterArray['to_date'])){
    		$to_date = trim($filterArray['to_date']);
    	}

    	// Filter Conditions
    	if($user_id!=""){
    		echo $user_id;
    		$model->where('user_id',$user_id);
    	}
    	if($order_id!=""){
    		$model->where('order_id',$order_id);
    	}	
    	if($payment_type!=""){
    		$model->where('payment_type',$payment_type);
    	}	
    	if($payment_status!=""){
    		$model->where('payment_status',$payment_status);
    	}	
    	if($payment_reference!=""){
    		$model->where('payment_referenceno','like','%'.$payment_reference.'%');
    	}	
    	if($order_status!=""){
    		$model->where('order_status',$order_status);
    	}
    	if($from_date!="" && $to_date!=""){
    		$model->where('DATE_FORMAT(date_time,"%Y-%m-%d")>='.$from_date.' AND DATE_FORMAT(date_time,"%Y-%m-%d")<='.$to_date);
    	}else if($from_date!=""){
    		$model->where('DATE_FORMAT(date_time,"%Y-%m-%d")='.$from_date);
    	}else if($from_date!=""){
    		$model->where('DATE_FORMAT(date_time,"%Y-%m-%d")='.$from_date);
    	}

    	// Get data with pagination
    	$model->orderBy('date_time', 'desc');
    	$totalRecords 	= count($model->get());
    	$data    		= $model->offset($start)->limit($length)->get();

    	$result = [];
    	if(!empty($data)){
			foreach ($data as $index=>$order) {
				$result[$index]['snumber'] = $start+1;
    			$result[$index]['order_id'] = $order->order_id;
    			$userFullname = "";
    			$supplierFullname = "";
    			if($order->user_id!="" && isset($order->user->userDetails->first_name)){
    				$userFullname = $order->user->userDetails->first_name.' '.$order->user->userDetails->last_name;
    			}
    			if($order->supplier_id!="" && isset($order->supplier->userDetails->first_name)){
    				$supplierFullname = $order->supplier->userDetails->first_name.' '.$order->supplier->userDetails->last_name;
    			}
    			$result[$index]['user'] = $userFullname;
    			$result[$index]['supplier'] = $supplierFullname;
    			$result[$index]['payment_type'] = PaymentType($order->payment_type);
    			$result[$index]['total_amount'] = $order->total_amount;
    			$result[$index]['date_time'] = $order->date_time!="" ? date('Y-m-d H:i:s',strtotime($order->date_time)) : "N/A";
    			if($order->order_status==1){
    				$result[$index]['status'] = '<select class="form-control orderstatus_change" name="status" data-id="'.$order->id.'" data-url="'.url('/dealer/changestatus-order').'"><option value="">Order Placed</option><option value="2">Order InProgress</option><option value="3">Order Shipped</option><option value="4">Order Delivered</option></select>';
    			}else if($order->order_status==2){
    				$result[$index]['status'] = '<select class="form-control orderstatus_change" name="status" data-id="'.$order->id.'" data-url="'.url('/dealer/changestatus-order').'"><option value="">Order InProgress</option><option value="3">Order Shipped</option><option value="4">Order Delivered</option></select>';
    			}else if($order->order_status==3){
    				$result[$index]['status'] = '<select class="form-control orderstatus_change" name="status" data-id="'.$order->id.'" data-url="'.url('/dealer/changestatus-order').'"><option value="">Order Shipped</option><option value="4">Order Delivered</option></select>';
    			}else{
    				$result[$index]['status'] = OrderStatus($order->order_status);
    			}

    			if($order->payment_status==0){
					$result[$index]['payment_status'] = '<select class="form-control paymentstatus_change" name="status" data-id="'.$order->id.'" data-url="'.url('/dealer/changepaymentstatus-order').'"><option value="0">Pending</option><option value="1">Success</option><option value="2">Failed</option></select>';
    			}else{
    				$result[$index]['payment_status'] = PaymentStatus($order->payment_status);
    			}

    			// action buttons
    			$action = '<a href="'.url('/dealer/view-order?id='.$order->id).'" title="View" class="btn btn-icon btn-sm btn-warning view-button"><i class="fas fa-eye"></i></a>';
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


    // Change Status
    public function ChangeStatus(Request $request)
    {
    	$id 		= isset($request->orderId) ? $request->orderId : 0;
    	$status   	= isset($request->status) ? $request->status : 0;
    	$model      = Orders::where('status', 1)->where('id',$id)->first();
    	if(!empty($model))
    	{
    		$model->order_status   = $status;
    		$model->save();
    		return response()->json(['success'=>'Order Status Changed Successfully.']);
    	}
    	else
    	{
    		return response()->json(['error'=>'Invalid Request. Try Again.']);
    	}
    } 

    // Change paymentStatus
    public function ChangepaymentStatus(Request $request)
    {
    	$id 		= isset($request->orderId) ? $request->orderId : 0;
    	$status   	= isset($request->status) ? $request->status : 0;
    	$model      = Orders::where('status', 1)->where('id',$id)->first();
    	if(!empty($model))
    	{
    		$model->payment_status   = $status;
    		$model->save();
    		return response()->json(['success'=>'Payment Status Changed Successfully.']);
    	}
    	else
    	{
    		return response()->json(['error'=>'Invalid Request. Try Again.']);
    	}
    } 


    // View Order
    public function ViewOrder(Request $request)
    {
        $id         = isset($request->id) ? $request->id : 0;
        $model      = Orders::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $products_model      = OrderedProducts::where('status', 1)->where('order_id',$id)->get();
            return view('dealer.Orders.View',['model'=>$model,'products_model'=>$products_model]);
        }
        else
        {
            return redirect('dealer/orders');
        }
    } 

}
