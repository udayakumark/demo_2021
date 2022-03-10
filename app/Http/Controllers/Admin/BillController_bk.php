<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\Rice;
use App\Models\Bag;
use App\Models\TblBill;
use App\Models\InvoicenoSettings;
use App\Models\BillsItem;
use App\Models\UserDetails;
use App\Models\Warehouses;
use App\Models\Cashbook;
use App\Models\BillsPurchases;
use App\Models\OtherProducts;
use App\Models\HSNCode;
use App\helpers;
use Validator;
use Redirect;
use PDF;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
	public function B2bIndex()
    {
		$heading  = 'Manage B2b Duplicate Billing';
		$actionUrl    = url('b2b/bill/add/');
		$redirectUrl  = url('b2b/bill/list');
        return view('admin.Bill.b2b.List',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
	public function B2bList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
		$bill_type_id =9;
		$bill_source_id =1;
        $model  = TblBill::select('tbl_billing.*');
		$model->join('users', 'users.id', '=', 'tbl_billing.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblBill::where('tbl_billing.bill_type_id',$bill_type_id)->where('tbl_billing.bill_source',$bill_source_id)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_billing.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/b2b/bill-print/'.$user->bill_id).'" title="Print" class="btn btn-icon btn-sm btn-primary update-button" target="_blank"><i class="fas fa-file-pdf"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2b/bill-view/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2b/bill-edit/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->bill_id.'"   data-url="'.url('/admin/b2b-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function B2bAdd(Request $request) {
		$actionUrl    = 'b2b/bill/add/';
		$redirectUrl  = '/admin/b2b/bill';
		$bill_type ='B2b';
		$bill_type_id =9;
		$bill_source_id =1;
		$bill_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$riceList  = Rice::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(9,'B2B');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bDetails->name;
				}
				$riceB2bName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2bName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblBill;
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$BillsItemsPurchases    			 = new BillsPurchases;
							$BillsItemsPurchases->bill_id  = $model->bill_id;
							$BillsItemsPurchases->wh_id  = $model->warehouse_id;
							$BillsItemsPurchases->product_id    = $product_id[$i];
							$BillsItemsPurchases->qty    = $qty[$i];
							$BillsItemsPurchases->price    = $price[$i];
							$BillsItemsPurchases->final_price    = $final_price[$i];
							$BillsItemsPurchases->total       = $total[$i];
							$BillsItemsPurchases->save();
						}
					}
					if ($model && $SalesItem && $BillsItemsPurchases){
						return response()->json(['success'=>'B2b Bill Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create B2b Bill information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create B2b Bill information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$nextInvoiceId,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'hsn'=>$request->hsn,'discount'=>$request->discount,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"bill_id"=>'',"paddy_id"=>'',"qty"=>'',"price"=>'',"final_price"=>'',"total"=>''));
        return view('admin.Bill.b2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function B2bView(Request $request){
        $bill_id 	= $request->bill_id;
		$bill_type ='B2b';
		$bill_type_id =9;
		$bill_action ='View';
		$redirectUrl = url('/admin/b2b/bill');
     
        $inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		$hsnCodeList='';
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$result=array();
		$bag_type ='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bDetails->name;
				}
				$riceB2bName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result[$index]['name'] = $riceB2bName;
                $result[$index]['id'] = $rice->id;
            }
        }
		return view('admin.Bill.b2b.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl]); 
    }
	public function B2bEdit(Request $request) {
		$bill_id 	= $request->bill_id;
		$actionUrl    = 'admin/b2b/bill-edit/'.$bill_id;
		$redirectUrl  = '/admin/b2b/bill';
		$bill_type ='B2b';
		$bill_type_id =9;
		$bill_source_id=1;
		$bill_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(9,'B2B');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bDetails->name;
				}
				$riceB2bName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2bName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$invoice_type = $request->invoice_type;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblBill::where('bill_id',$bill_id)->first();
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = BillsItem::where('bill_id', $bill_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
	 					foreach($salesitems as $salesitem) {
							BillsItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					$billsItemsPur = BillsPurchases::where('bill_id', $bill_id)->get();					
					if(isset($billsItemsPur) && !empty($billsItemsPur)) {
	 					foreach($billsItemsPur as $billsItemsPu) {
							BillsPurchases::where('id', $billsItemsPu->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$BillsItemsPurchases    			 = new BillsPurchases;
							$BillsItemsPurchases->bill_id  = $model->bill_id;
							$BillsItemsPurchases->wh_id  = $model->warehouse_id;
							$BillsItemsPurchases->product_id    = $product_id[$i];
							$BillsItemsPurchases->qty    = $qty[$i];
							$BillsItemsPurchases->price    = $price[$i];
							$BillsItemsPurchases->final_price    = $final_price[$i];
							$BillsItemsPurchases->total       = $total[$i];
							$BillsItemsPurchases->save();
						}
					}
					if ($model && $SalesItem && $BillsItemsPurchases){
						return response()->json(['success'=>'B2b Bill Details Updated Successfully.']);
	 				} else {
	 					return response()->json(['singleerror'=>'Failed to Update B2b Bill information. Try again after sometime.']);
					}
	 			} else {
	 				return response()->json(['singleerror'=>'Failed to Updat B2b Bill information. Try again after sometime.']);
	 			}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Bill.b2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function B2bDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblBill::where('bill_id', $id)->first();
        if(!empty($model))
        { 
             $model->status       = 0;
             $model->save();
            return response()->json(['success'=>'B2b Details Deleted Successfully.']);
        } 
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function getVendordetailsb2b(Request $request) {
        $user_id 	= $request->user_id;
        $vendor_details =  UserDetails::where('user_id', $user_id)->first();
        $cashdetails = $vendor_details->cashbook->sum('amount');
        $crcash = Cashbook::where('user_id', $user_id)->where('type', 'CR')->sum('amount');
        $dbcash = Cashbook::where('user_id', $user_id)->where('type', 'DB')->sum('amount');
        $balance = $crcash - $dbcash;
        if ($balance > 0){
            $balance = "+".$balance;
        }
        $response = array(
            "vendor_details" => $vendor_details,
            "cashdetails" => $balance
        );
        return response()->json($response);
    }
	public function getRicedetailsb2b(Request $request) {
        $riceList  = Rice::where('status', 1)->where('id',$request->rice_id)->first();
		echo $riceList->dealers_price;
        //return response()->json($response);
    }
	public function getInvoiceNo($type,$sourceType) {
		$InvoicenoSettings = InvoicenoSettings::where('type', $type)->value('invoice_no');
		$billMaxId = TblBill::where('invoice_no', 'like', '%SAK_'.$sourceType.'')->orderBy('bill_id', 'desc')->value('invoice_no');
		$nextInvoiceId='';
		if(isset($billMaxId) && $billMaxId!='') {
			$invoicePrefix = "SAK_".$sourceType."_2021_";
			$billMaxIdPrefix = ltrim(str_replace($invoicePrefix, "", $billMaxId),"0");	
			$nextInvoiceIdVal = $billMaxIdPrefix+1;
			$nextInvoiceId = $invoicePrefix.STR_PAD((string)$nextInvoiceIdVal,5,"0",STR_PAD_LEFT);
		} else {
			$nextInvoiceId = $InvoicenoSettings;
		}
		return $nextInvoiceId;
	}
	public function B2bPrint(Request $request){
        $bill_id 	= $request->bill_id;
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		$hsnCodeList='';
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$result=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bDetails->name;
				}
				$riceB2bName = ($bag_type) ? 'Boiled Rice -'.$bag_type : 'Boiled Rice';
                $result[$index]['name'] = $riceB2bName;
                $result[$index]['id'] = $rice->id;
            }
        }
		$pdf = PDF::loadView('admin.Bill.b2b.invoicepdf', ['vendor_details'=>$vendor_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'warehouseList'=>$warehouseListGet,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList]);
		return $pdf->stream('document.pdf');
        //return view('admin.Bill.b2b.invoicepdf',['vendorlist'=>$dealer_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems]);
    }
	public function B2bdIndex()
    {
		$heading  = 'Manage B2b Duplicate Billing';
		$actionUrl    = url('b2bd/bill/add/');
		$redirectUrl  = url('b2bd/bill/list');
        return view('admin.Bill.b2b.List',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
	public function B2bdList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
		$bill_type_id =9;
		$bill_source_id =2;
        $model  = TblBill::select('tbl_billing.*');
		$model->join('users', 'users.id', '=', 'tbl_billing.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblBill::where('tbl_billing.bill_type_id',$bill_type_id)->where('tbl_billing.bill_source',$bill_source_id)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_billing.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/b2bd/bill-print/'.$user->bill_id).'" title="Print" class="btn btn-icon btn-sm btn-primary update-button" target="_blank"><i class="fas fa-file-pdf"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2bd/bill-view/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2bd/bill-edit/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->bill_id.'"   data-url="'.url('/admin/b2bd-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function B2bdAdd(Request $request) {
		$actionUrl    = 'b2bd/bill/add/';
		$redirectUrl  = '/admin/b2bd/bill';
		$bill_type ='B2b Duplicate';
		$bill_type_id =9;
		$bill_source_id =2;
		$bill_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$riceList  = Rice::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(99,'B2BD');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bdDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bdDetails->name;
				}
				$riceB2bdName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2bdName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblBill;
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'B2b Duplicate Bill Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create B2bd Bill information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create B2bd Bill information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$nextInvoiceId,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'hsn'=>$request->hsn,'discount'=>$request->discount,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"bill_id"=>'',"paddy_id"=>'',"qty"=>'',"price"=>'',"final_price"=>'',"total"=>''));
        return view('admin.Bill.b2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function B2bdView(Request $request){
        $bill_id 	= $request->bill_id;
		$bill_type ='B2b Duplicate';
		$bill_type_id =9;
		$bill_action ='View';
		$redirectUrl = url('/admin/b2bd/bill');
	 
        $inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		$hsnCodeList='';
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$result=array();
		$bag_type ='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bdDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bdDetails->name;
				}
				$riceB2bdName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result[$index]['name'] = $riceB2bdName;
                $result[$index]['id'] = $rice->id;
            }
        }
		return view('admin.Bill.b2b.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl]); 
    }
	public function B2bdEdit(Request $request) {
		$bill_id 	= $request->bill_id;
		$actionUrl    = 'admin/b2bd/bill-edit/'.$bill_id;
		$redirectUrl  = '/admin/b2bd/bill';
		$bill_type ='B2b Duplicate';
		$bill_type_id =9;
		$bill_source_id=2;
		$bill_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$warehouseList  = Warehouses::where('status', 1)->get();
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(99,'B2BD');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bdDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bdDetails->name;
				}
				$riceB2bdName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2bdName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$invoice_type = $request->invoice_type;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblBill::where('bill_id',$bill_id)->first();
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = BillsItem::where('bill_id', $bill_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
	 					foreach($salesitems as $salesitem) {
							BillsItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'B2b Duplicate Bill Details Updated Successfully.']);
	 				} else {
	 					return response()->json(['singleerror'=>'Failed to Update B2bd Bill information. Try again after sometime.']);
					}
	 			} else {
	 				return response()->json(['singleerror'=>'Failed to Updat B2bd Bill information. Try again after sometime.']);
	 			}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Bill.b2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function B2bdDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblBill::where('bill_id', $id)->first();
        if(!empty($model))
        { 
             $model->status       = 0;
             $model->save();
            return response()->json(['success'=>'B2b Duplicate Details Deleted Successfully.']);
        } 
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function B2bdPrint(Request $request){
        $bill_id 	= $request->bill_id;
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		$hsnCodeList='';
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$result=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bdDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bdDetails->name;
				}
				$riceB2bdName = ($bag_type) ? 'Boiled Rice -'.$bag_type : 'Boiled Rice';
                $result[$index]['name'] = $riceB2bdName;
                $result[$index]['id'] = $rice->id;
            }
        }
		$pdf = PDF::loadView('admin.Bill.b2b.invoicepdf', ['vendor_details'=>$vendor_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'warehouseList'=>$warehouseListGet,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList]);
		return $pdf->stream('document.pdf');
        //return view('admin.Bill.b2bd.invoicepdf',['vendorlist'=>$dealer_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems]);
    }
	public function B2cIndex()
    {
		$heading  = 'Manage B2c Billing';
		$actionUrl    = url('b2c/bill/add/');
		$redirectUrl  = url('b2c/bill/list');
        return view('admin.Bill.b2c.List',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
	public function B2cList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
		$bill_type_id =8;
		$bill_source_id =1;
        $model  = TblBill::select('tbl_billing.*');
		$model->join('users', 'users.id', '=', 'tbl_billing.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblBill::where('tbl_billing.bill_type_id',$bill_type_id)->where('tbl_billing.bill_source',$bill_source_id)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_billing.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/b2c/bill-print/'.$user->bill_id).'" title="Print" class="btn btn-icon btn-sm btn-primary update-button" target="_blank"><i class="fas fa-file-pdf"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2c/bill-view/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2c/bill-edit/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->bill_id.'"   data-url="'.url('/admin/b2c-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function B2cAdd(Request $request) {
		$actionUrl    = 'b2c/bill/add/';
		$redirectUrl  = '/admin/b2c/bill';
		$bill_type ='B2c';
		$bill_type_id =8;
		$bill_source_id =1;
		$bill_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$riceList  = Rice::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(8,'B2C');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2cDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2cDetails->name;
				}
				$riceB2cName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2cName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblBill;
				$model->bill_type_id = $bill_type_id;
				$model->bill_source  = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$BillsItemsPurchases    			 = new BillsPurchases;
							$BillsItemsPurchases->bill_id  = $model->bill_id;
							$BillsItemsPurchases->wh_id  = $model->warehouse_id;
							$BillsItemsPurchases->product_id    = $product_id[$i];
							$BillsItemsPurchases->qty    = $qty[$i];
							$BillsItemsPurchases->price    = $price[$i];
							$BillsItemsPurchases->final_price    = $final_price[$i];
							$BillsItemsPurchases->total       = $total[$i];
							$BillsItemsPurchases->save();
						}
					}
					if ($model && $SalesItem && $BillsItemsPurchases){
						return response()->json(['success'=>'B2c Bill Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create B2c Bill information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create B2c Bill information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$nextInvoiceId,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'hsn'=>$request->hsn,'discount'=>$request->discount,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"bill_id"=>'',"paddy_id"=>'',"qty"=>'',"price"=>'',"final_price"=>'',"total"=>''));
        return view('admin.Bill.b2c.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function B2cView(Request $request){
        $bill_id 	= $request->bill_id;
		$bill_type ='B2c';
		$bill_type_id =8;
		$bill_action ='View';
        $redirectUrl  = url('/admin/b2c/bill');
		
        $inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$result=array();
		$bag_type ='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2cDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2cDetails->name;
				}
				$riceB2cName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result[$index]['name'] = $riceB2cName;
                $result[$index]['id'] = $rice->id;
            }
        }
		return view('admin.Bill.b2c.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl]); 
    }
	public function B2cEdit(Request $request) {
		$bill_id 	= $request->bill_id;
		$actionUrl    = 'admin/b2c/bill-edit/'.$bill_id;
		$redirectUrl  = '/admin/b2c/bill';
		$bill_type ='B2c';
		$bill_type_id =8;
		$bill_source_id =1;
		$bill_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$warehouseList  = Warehouses::where('status', 1)->get();
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(8,'B2C');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2cDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2cDetails->name;
				}
				$riceB2cName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2cName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$invoice_type = $request->invoice_type;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblBill::where('bill_id',$bill_id)->first();
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = BillsItem::where('bill_id', $bill_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
	 					foreach($salesitems as $salesitem) {
							BillsItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					$billsItemsPur = BillsPurchases::where('bill_id', $bill_id)->get();					
					if(isset($billsItemsPur) && !empty($billsItemsPur)) {
	 					foreach($billsItemsPur as $billsItemsPu) {
							BillsPurchases::where('id', $billsItemsPu->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$BillsItemsPurchases    			 = new BillsPurchases;
							$BillsItemsPurchases->bill_id  = $model->bill_id;
							$BillsItemsPurchases->wh_id  = $model->warehouse_id;
							$BillsItemsPurchases->product_id    = $product_id[$i];
							$BillsItemsPurchases->qty    = $qty[$i];
							$BillsItemsPurchases->price    = $price[$i];
							$BillsItemsPurchases->final_price    = $final_price[$i];
							$BillsItemsPurchases->total       = $total[$i];
							$BillsItemsPurchases->save();
						}
					}
					if ($model && $SalesItem && $BillsItemsPurchases){
						return response()->json(['success'=>'B2c Bill Details Updated Successfully.']);
	 				} else {
	 					return response()->json(['singleerror'=>'Failed to Update B2c Bill information. Try again after sometime.']);
					}
	 			} else {
	 				return response()->json(['singleerror'=>'Failed to Updat B2c Bill information. Try again after sometime.']);
	 			}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Bill.b2c.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function B2cDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblBill::where('bill_id', $id)->first();
        if(!empty($model))
        { 
             $model->status       = 0;
             $model->save();
            return response()->json(['success'=>'B2c Details Deleted Successfully.']);
        } 
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function B2cPrint(Request $request){
        $bill_id 	= $request->bill_id;
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$result=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bDetails->name;
				}
				$riceB2bName = ($bag_type) ? 'Boiled Rice -'.$bag_type : 'Boiled Rice';
                $result[$index]['name'] = $riceB2bName;
                $result[$index]['id'] = $rice->id;
            }
        }
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$pdf = PDF::loadView('admin.Bill.b2c.invoicepdf', ['vendor_details'=>$vendor_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'warehouseList'=>$warehouseListGet,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList]);
		return $pdf->stream('document.pdf');
    }
	public function getVendordetailsb2c(Request $request) {
        $user_id 	= $request->user_id;
        $vendor_details =  UserDetails::where('user_id', $user_id)->first();
        $cashdetails = $vendor_details->cashbook->sum('amount');
        $crcash = Cashbook::where('user_id', $user_id)->where('type', 'CR')->sum('amount');
        $dbcash = Cashbook::where('user_id', $user_id)->where('type', 'DB')->sum('amount');
        $balance = $crcash - $dbcash;
        if ($balance > 0){
            $balance = "+".$balance;
        }
        $response = array(
            "vendor_details" => $vendor_details,
            "cashdetails" => $balance
        );
        return response()->json($response);
    }
	public function getRicedetailsb2c(Request $request) {
        $riceList  = Rice::where('status', 1)->where('id',$request->rice_id)->first();
		echo $riceList->customers_price;
        //return response()->json($response);
    }
	public function B2cdIndex()
    {
		$heading  = 'Manage B2c Duplicate Billing';
		$actionUrl    = url('b2cd/bill/add/');
		$redirectUrl  = url('b2cd/bill/list');
        return view('admin.Bill.b2c.List',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
	public function B2cdList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
		$bill_type_id =8;
		$bill_source_id =2;
        $model  = TblBill::select('tbl_billing.*');
		$model->join('users', 'users.id', '=', 'tbl_billing.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblBill::where('tbl_billing.bill_type_id',$bill_type_id)->where('tbl_billing.bill_source',$bill_source_id)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_billing.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/b2cd/bill-print/'.$user->bill_id).'" title="Print" class="btn btn-icon btn-sm btn-primary update-button" target="_blank"><i class="fas fa-file-pdf"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2cd/bill-view/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/b2cd/bill-edit/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->bill_id.'"   data-url="'.url('/admin/b2c-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function B2cdAdd(Request $request) {
		$actionUrl    = 'b2cd/bill/add/';
		$redirectUrl  = '/admin/b2cd/bill';
		$bill_type ='B2cd';
		$bill_type_id =8;
		$bill_source_id =2;
		$bill_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$riceList  = Rice::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(88,'B2CD');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2cDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2cDetails->name;
				}
				$riceB2cdName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2cdName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblBill;
				$model->bill_type_id = $bill_type_id;
				$model->bill_source  = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'B2c Duplicate Bill Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create B2cd Bill information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create B2cd Bill information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$nextInvoiceId,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'hsn'=>$request->hsn,'discount'=>$request->discount,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"bill_id"=>'',"paddy_id"=>'',"qty"=>'',"price"=>'',"final_price"=>'',"total"=>''));
        return view('admin.Bill.b2c.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function B2cdView(Request $request){
        $bill_id 	= $request->bill_id;
		$bill_type ='B2cd';
		$bill_type_id =8;
		$bill_action ='View';
        $redirectUrl  = url('b2c/bill/list');
		
        $inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$result=array();
		$bag_type ='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2cDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2cDetails->name;
				}
				$riceB2cdName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result[$index]['name'] = $riceB2cdName;
                $result[$index]['id'] = $rice->id;
            }
        }
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		return view('admin.Bill.b2c.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl]); 
    }
	public function B2cdEdit(Request $request) {
		$bill_id 	= $request->bill_id;
		$actionUrl    = 'admin/b2cd/bill-edit/'.$bill_id;
		$redirectUrl  = '/admin/b2cd/bill';
		$bill_type ='B2cd';
		$bill_type_id =8;
		$bill_source_id =2;
		$bill_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$warehouseList  = Warehouses::where('status', 1)->get();
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(88,'B2CD');
        $result = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		$result2=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2cDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2cDetails->name;
				}
				$riceB2cdName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceB2cdName;
                $result2[$index]['id'] = $rice->id;
            }
        }
		if(isset($_POST['_token']))
        {
			$validator = Validator::make($request->all(), [
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$invoice_type = $request->invoice_type;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$price 	= $request->price;
				$final_price 	= $request->final_price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblBill::where('bill_id',$bill_id)->first();
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = BillsItem::where('bill_id', $bill_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
	 					foreach($salesitems as $salesitem) {
							BillsItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = $final_price[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'B2c Duplicate Bill Details Updated Successfully.']);
	 				} else {
	 					return response()->json(['singleerror'=>'Failed to Update B2c Duplicate Bill information. Try again after sometime.']);
					}
	 			} else {
	 				return response()->json(['singleerror'=>'Failed to Updat B2c Duplicate Bill information. Try again after sometime.']);
	 			}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Bill.b2c.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function B2cdDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblBill::where('bill_id', $id)->first();
        if(!empty($model))
        { 
             $model->status       = 0;
             $model->save();
            return response()->json(['success'=>'B2c Duplicate Bill Details Deleted Successfully.']);
        } 
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function B2cdPrint(Request $request){
        $bill_id 	= $request->bill_id;
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		$result=array();
		$bag_type='';
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $b2bDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $b2bDetails->name;
				}
				$riceB2bName = ($bag_type) ? 'Boiled Rice -'.$bag_type : 'Boiled Rice';
                $result[$index]['name'] = $riceB2bName;
                $result[$index]['id'] = $rice->id;
            }
        }
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$pdf = PDF::loadView('admin.Bill.b2c.invoicepdf', ['vendor_details'=>$vendor_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'warehouseList'=>$warehouseListGet,'riceList'=>$result,'hsnCodeList'=>$hsnCodeList]);
		return $pdf->stream('document.pdf');
    }
	public function O2bIndex()
    {
		$heading  = 'Manage O2b Billing';
		$actionUrl    = url('o2b/bill/add/');
		$redirectUrl  = url('o2b/bill/list');
        return view('admin.Bill.o2b.List',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
	public function O2bList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
		$bill_type_id =10;
		$bill_source_id =1;
        $model  = TblBill::select('tbl_billing.*');
		$model->join('users', 'users.id', '=', 'tbl_billing.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblBill::where('tbl_billing.bill_type_id',$bill_type_id)->where('tbl_billing.bill_source',$bill_source_id)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_billing.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/o2b/bill-print/'.$user->bill_id).'" title="Print" class="btn btn-icon btn-sm btn-primary update-button" target="_blank"><i class="fas fa-file-pdf"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/o2b/bill-view/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/o2b/bill-edit/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->bill_id.'"   data-url="'.url('/admin/o2b-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function O2bAdd(Request $request) {
		$actionUrl    = 'o2b/bill/add/';
		$redirectUrl  = '/admin/o2b/bill';
		$bill_type ='O2b';
		$bill_type_id =10;
		$bill_source_id =1;
		$bill_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(10,'O2B');
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
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$wgt 	= $request->wgt;
				$qty 	= $request->qty;
				$totalwgt 	= $request->totalwgt;
				$price 	= $request->price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblBill;
				$model->bill_type_id = $bill_type_id;
				$model->bill_source  = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->wgt    = $wgt[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = $totalwgt[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = NULL;
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$BillsItemsPurchases    			 = new BillsPurchases;
							$BillsItemsPurchases->bill_id  = $model->bill_id;
							$BillsItemsPurchases->wh_id  = $model->warehouse_id;
							$BillsItemsPurchases->product_id    = $product_id[$i];
							$BillsItemsPurchases->wgt    = $wgt[$i];
							$BillsItemsPurchases->qty    = $qty[$i];
							$BillsItemsPurchases->totalwgt    = $totalwgt[$i];
							$BillsItemsPurchases->price    = $price[$i];
							$BillsItemsPurchases->final_price    = NULL;
							$BillsItemsPurchases->total       = $total[$i];
							$BillsItemsPurchases->save();
						}
					}
					if ($model && $SalesItem && $BillsItemsPurchases){
						return response()->json(['success'=>'O2b Bill Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create O2b Bill information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create O2b Bill information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$nextInvoiceId,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'hsn'=>$request->hsn,'discount'=>$request->discount,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"bill_id"=>'',"paddy_id"=>'',"wgt"=>'',"qty"=>'',"totalwgt"=>'',"price"=>'',"total"=>''));
        return view('admin.Bill.o2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'otherProductsList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function O2bView(Request $request){
        $bill_id 	= $request->bill_id;
		$bill_type ='O2b';
		$bill_type_id =10;
		$bill_action ='View';
        $redirectUrl  = url('/admin/o2b/bill');
		
        $inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		return view('admin.Bill.o2b.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'otherProductsList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl]); 
    }
	public function O2bEdit(Request $request) {
		$bill_id 	= $request->bill_id;
		$actionUrl    = 'admin/o2b/bill-edit/'.$bill_id;
		$redirectUrl  = '/admin/o2b/bill';
		$bill_type ='O2b';
		$bill_type_id =10;
		$bill_source_id =1;
		$bill_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$warehouseList  = Warehouses::where('status', 1)->get();
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(10,'O2B');
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
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$invoice_type = $request->invoice_type;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$wgt 	= $request->wgt;
				$qty 	= $request->qty;
				$totalwgt 	= $request->totalwgt;
				$price 	= $request->price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblBill::where('bill_id',$bill_id)->first();
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = BillsItem::where('bill_id', $bill_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
	 					foreach($salesitems as $salesitem) {
							BillsItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->wgt    = $wgt[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = $totalwgt[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = NULL;
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					$billsItemsPur = BillsPurchases::where('bill_id', $bill_id)->get();					
					if(isset($billsItemsPur) && !empty($billsItemsPur)) {
	 					foreach($billsItemsPur as $billsItemsPu) {
							BillsPurchases::where('id', $billsItemsPu->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$BillsItemsPurchases    			 = new BillsPurchases;
							$BillsItemsPurchases->bill_id  = $model->bill_id;
							$BillsItemsPurchases->wh_id  = $model->warehouse_id;
							$BillsItemsPurchases->product_id    = $product_id[$i];
							$BillsItemsPurchases->wgt    = $wgt[$i];
							$BillsItemsPurchases->qty    = $qty[$i];
							$BillsItemsPurchases->totalwgt    = $totalwgt[$i];
							$BillsItemsPurchases->price    = $price[$i];
							$BillsItemsPurchases->final_price    = NULL;
							$BillsItemsPurchases->total       = $total[$i];
							$BillsItemsPurchases->save();
						}
					}
					if ($model && $SalesItem && $BillsItemsPurchases){
						return response()->json(['success'=>'O2b Bill Details Updated Successfully.']);
	 				} else {
	 					return response()->json(['singleerror'=>'Failed to Update O2b Bill information. Try again after sometime.']);
					}
	 			} else {
	 				return response()->json(['singleerror'=>'Failed to Updat O2b Bill information. Try again after sometime.']);
	 			}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Bill.o2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'otherProductsList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function O2bDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblBill::where('bill_id', $id)->first();
        if(!empty($model))
        { 
             $model->status       = 0;
             $model->save();
            return response()->json(['success'=>'O2b Details Deleted Successfully.']);
        } 
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function O2bPrint(Request $request){
        $bill_id 	= $request->bill_id;
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$pdf = PDF::loadView('admin.Bill.o2b.invoicepdf', ['vendor_details'=>$vendor_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'warehouseList'=>$warehouseListGet,'riceList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList]);
		return $pdf->stream('document.pdf');
    }
	public function getVendordetailso2b(Request $request) {
        $user_id 	= $request->user_id;
        $vendor_details =  UserDetails::where('user_id', $user_id)->first();
        $cashdetails = $vendor_details->cashbook->sum('amount');
        $crcash = Cashbook::where('user_id', $user_id)->where('type', 'CR')->sum('amount');
        $dbcash = Cashbook::where('user_id', $user_id)->where('type', 'DB')->sum('amount');
        $balance = $crcash - $dbcash;
        if ($balance > 0){
            $balance = "+".$balance;
        }
        $response = array(
            "vendor_details" => $vendor_details,
            "cashdetails" => $balance
        );
        return response()->json($response);
    }
	public function getRicedetailso2b(Request $request) {
        $riceList  = Rice::where('status', 1)->where('id',$request->rice_id)->first();
		echo $riceList->customers_price;
        //return response()->json($response);
    }
	public function O2bdIndex()
    {
		$heading  = 'Manage O2b Duplicate Billing';
		$actionUrl    = url('o2bd/bill/add/');
		$redirectUrl  = url('o2bd/bill/list');
        return view('admin.Bill.o2b.List',['heading'=>$heading,'actionUrl'=>$actionUrl,'redirectUrl'=>$redirectUrl]);
    }
	public function O2bdList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
		$bill_type_id =10;
		$bill_source_id =2;
        $model  = TblBill::select('tbl_billing.*');
		$model->join('users', 'users.id', '=', 'tbl_billing.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_billing.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblBill::where('tbl_billing.bill_type_id',$bill_type_id)->where('tbl_billing.bill_source',$bill_source_id)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_billing.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/o2bd/bill-print/'.$user->bill_id).'" title="Print" class="btn btn-icon btn-sm btn-primary update-button" target="_blank"><i class="fas fa-file-pdf"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/o2bd/bill-view/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/o2bd/bill-edit/'.$user->bill_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->bill_id.'"   data-url="'.url('/admin/o2b-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function O2bdAdd(Request $request) {
		$actionUrl    = 'o2bd/bill/add/';
		$redirectUrl  = '/admin/o2bd/bill';
		$bill_type ='O2bd';
		$bill_type_id =10;
		$bill_source_id =2;
		$bill_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(1010,'O2BD');
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
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$wgt 	= $request->wgt;
				$qty 	= $request->qty;
				$totalwgt 	= $request->totalwgt;
				$price 	= $request->price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblBill;
				$model->bill_type_id = $bill_type_id;
				$model->bill_source  = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->wgt    = $wgt[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = $totalwgt[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = NULL;
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'O2b Duplicate Bill Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create O2b Duplicate Bill information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create O2b Duplicate Bill information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$nextInvoiceId,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'hsn'=>$request->hsn,'discount'=>$request->discount,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"bill_id"=>'',"paddy_id"=>'',"wgt"=>'',"qty"=>'',"totalwgt"=>'',"price"=>'',"total"=>''));
        return view('admin.Bill.o2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'otherProductsList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function O2bdView(Request $request){
        $bill_id 	= $request->bill_id;
		$bill_type ='O2bd';
		$bill_type_id =10;
		$bill_action ='View';
        $redirectUrl  = url('/admin/o2bd/bill');
		
        $inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		return view('admin.Bill.o2b.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'otherProductsList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl]); 
    }
	public function O2bdEdit(Request $request) {
		$bill_id 	= $request->bill_id;
		$actionUrl    = 'admin/o2bd/bill-edit/'.$bill_id;
		$redirectUrl  = '/admin/o2bd/bill';
		$bill_type ='O2bd';
		$bill_type_id =10;
		$bill_source_id =2;
		$bill_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$bill_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		$warehouseList  = Warehouses::where('status', 1)->get();
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
		$hsnCodeList  = HSNCode::where('status', 1)->get();
		$nextInvoiceId = $this->getInvoiceNo(1010,'O2BD');
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
            'supplname' => 'required',
            'billdate' => 'required',
            'total' => 'required',
            'invoice_no' => 'required',
            'warehouse' => 'required'
			]);

			if($validator->passes())
			{
				$invoice_type = $request->invoice_type;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$hsn   = $request->hsn;
				$product_id 	= $request->product_id;
				$wgt 	= $request->wgt;
				$qty 	= $request->qty;
				$totalwgt 	= $request->totalwgt;
				$price 	= $request->price;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$discount 	= $request->discount;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblBill::where('bill_id',$bill_id)->first();
				$model->bill_type_id = $bill_type_id;
				$model->bill_source = $bill_source_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->hsn   		  = $hsn;
				$model->discount   = $discount;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = BillsItem::where('bill_id', $bill_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
	 					foreach($salesitems as $salesitem) {
							BillsItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			 = new BillsItem;
							$SalesItem->bill_id  = $model->bill_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->wgt    = $wgt[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = $totalwgt[$i];
							$SalesItem->price    = $price[$i];
							$SalesItem->final_price    = NULL;
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'O2b Duplicate Bill Details Updated Successfully.']);
	 				} else {
	 					return response()->json(['singleerror'=>'Failed to Update O2b Duplicate Bill information. Try again after sometime.']);
					}
	 			} else {
	 				return response()->json(['singleerror'=>'Failed to Updat O2b Duplicate Bill information. Try again after sometime.']);
	 			}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Bill.o2b.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'otherProductsList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'bill_type'=>$bill_type,'bill_action'=>$bill_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function O2bdDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblBill::where('bill_id', $id)->first();
        if(!empty($model))
        { 
             $model->status       = 0;
             $model->save();
            return response()->json(['success'=>'O2bd Details Deleted Successfully.']);
        } 
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function O2bdPrint(Request $request){
        $bill_id 	= $request->bill_id;
		$inv_details  = TblBill::where('bill_id', $bill_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = BillsItem::where('bill_id', $bill_id)->get();
		$otherProductsList  = OtherProducts::where('status', 1)->get();
		$hsnCodeList='';
		$hsnCodeListGet  = HSNCode::where('status', 1)->where('id', $inv_details['hsn'])->first();
		if(isset($hsnCodeListGet['name']) && $hsnCodeListGet['name']!="") {
			$hsnCodeList  = $hsnCodeListGet['name'];
		}
		$pdf = PDF::loadView('admin.Bill.o2b.invoicepdf', ['vendor_details'=>$vendor_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'warehouseList'=>$warehouseListGet,'riceList'=>$otherProductsList,'hsnCodeList'=>$hsnCodeList]);
		return $pdf->stream('document.pdf');
    }
}
