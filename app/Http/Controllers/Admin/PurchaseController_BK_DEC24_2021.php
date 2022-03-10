<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\ProductCategories;
use App\Models\Products;
use App\Models\InvoiceSettings;
use App\Models\TblSales;
use App\Models\Paddy;
use App\Models\Rice;
use App\Models\Bag;
use App\Models\TblPurchase;
use App\Models\SalesItem;
use App\Models\PurchasesItem;
use App\Models\UserDetails;
use App\Models\Cashbook;
use App\Models\Warehouses;
use App\Models\VendorType;
use App\Models\TblProduction;
use App\Models\SourceItems;
use App\Models\DestinationItems;
use App\Models\UniqueNo;
use App\helpers;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function Index()
    {
        return view('admin.Purchase.List');
    }
	public function PaddyList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        $model  = TblPurchase::select('tbl_purchase.*');
		$model->join('users', 'users.id', '=', 'tbl_purchase.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_purchase.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',1);
        $model = TblPurchase::where('tbl_purchase.purchase_type_id',1)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        $purchase_source_search = "";
		$invoice_no = "";
        if(isset($filterArray['purchase_source_search'])){
            $purchase_source_search = trim($filterArray['purchase_source_search']);
        }
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
        if($purchase_source_search!=""){
            $model->where('tbl_purchase.purchase_source','like','%'.$purchase_source_search.'%');
        }
		if($invoice_no!=""){
            $model->where('tbl_purchase.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_purchase.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $invoiceno    	= $user->invoice_no;
				$vendorname 		= "";
				$vendor_balance  	= "";
				$purchase_source  = "";
				$broker_name  		= "";
                $total_amount 	= $user->total;
				$invoicedate 		= "";
                if(isset($user->vendor_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->vendor_id)->first();
                    $vendorname = $UserDetails->first_name.' '.$UserDetails->last_name;
                    $vendor_balance = $UserDetails->current_balance;
                }
				if(isset($user->broker_id)){
                    $UserDetails 	= UserDetails::where('user_id',$user->broker_id)->first();
                    $broker_name = $UserDetails->first_name.' '.$UserDetails->last_name;
                }
				if(isset($user->purchase_source) && $user->purchase_source!=0)
                {
                    $purchase_source = ($user->purchase_source==1) ? 'Own' : 'Third Party';
                }
				if(isset($user->inv_date) && $user->inv_date!="")
                {
                    $invoicedate = date('d-m-Y',strtotime($user->inv_date));
                }
                $result[$index]['snumber'] = $start+1;
				$result[$index]['invoiceno'] = $invoiceno;
				$result[$index]['vendorname'] = $vendorname;
                $result[$index]['vendor_balance'] = $vendor_balance;
                $result[$index]['broker_name'] = $broker_name;
                $result[$index]['purchase_source'] = $purchase_source;
                $result[$index]['amount'] = $total_amount;
				$result[$index]['invoicedate'] = $invoicedate;
                // action buttons
                $action = '<a href="'.url('/admin/purchase-view/'.$user->purchase_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/purchase-edit/'.$user->purchase_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->purchase_id.'"   data-url="'.url('/admin/purchase-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
    public function PaddyAdd(Request $request) {
		$actionUrl    = '/purchase/add/';
		$redirectUrl  = '/admin/purchase';
		$purchase_type ='Paddy';
		$purchase_type_id =1;
		$purchase_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$purchase_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
		
        $broker  = User::select('users.*');
        $broker->join('user_details', 'user_details.user_id', '=', 'users.id');
        $broker->where('users.status','!=',0);
		$broker->where('user_details.account',5);
        $broker->orderBy('users.date_time', 'desc');
        $totalRecords2 	= count($broker->get());
        $data2    		= $broker->get();

        $productList  = Paddy::where('status', 1)->where('type', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
        $categoryList = ProductCategories::where('status', 1)->orderBy('category_name')->get();
		$purchaseMaxId = TblPurchase::orderBy('purchase_id', 'desc')->value('purchase_id');
		$nextInvoiceId='';
		if(isset($purchaseMaxId) && $purchaseMaxId!='') {
			$idPostfix = (int)$purchaseMaxId+1;
			$nextInvoiceId = 'STI_PADDY_THIRDPARTY_'.STR_PAD((string)$idPostfix,5,"0",STR_PAD_LEFT);
		}
        $result = $result2 = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
        if(!empty($data2)){
            foreach ($data2 as $index=>$brokers) {
                $result2[$index]['first_name'] = $brokers->userDetails->first_name;
                $result2[$index]['last_name'] = $brokers->userDetails->last_name;
                $result2[$index]['id'] = $brokers->id;
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
				$purchase_type_id = 1;
				$purchase_source = $request->purchase_source;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$broker_id 	= $request->broker;
				$veh_no 	= $request->veh_no;
				$bro_comm   = $request->bro_comm;
				$bro_comm_total   = $request->bro_comm_total;
				$weigh1 	= $request->weigh1;
				$weigh2 	= $request->weigh2;
				$weigh3 	= $request->weigh3;
				$narration 	= $request->narration;
				$product_id 	= $request->product_id;
				$wgt  	= $request->wgt;
				$qty 	= $request->qty;
				$totalwgt 	= $request->totalwgt;
				$rate 	= $request->rate;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$bro_comm 	= $request->bro_comm;
				$bro_comm_total 	= $request->bro_comm_total;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblPurchase;
				$model->purchase_type_id = $purchase_type_id;
				$model->purchase_source = $purchase_source;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id      = $warehouse_id;
				$model->broker_id      = $broker_id;
				$model->veh_no      = $veh_no;
				$model->weigh1      = $weigh1;
				$model->weigh2      = $weigh2;
				$model->weigh3      = $weigh3;
				$model->narration   = $narration;
				$model->bro_comm   = $bro_comm;
				$model->bro_comm_total   = $bro_comm_total;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){

							$SalesItem    			  = new PurchasesItem;
							$SalesItem->purchase_id    = $model->purchase_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->wgt    = $wgt[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = $totalwgt[$i];
							$SalesItem->rate      = $rate[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'Paddy Purchase Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create Paddy Purchase information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create Paddy Purchase information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$request->invoice_no,'inv_date'=>date('Y-m-d'),'purchase_source'=>$request->purchase_source,'vendor_id'=>$request->supplname,'broker_id'=>$request->broker,'warehouse_id'=>$request->warehouse, 'veh_no'=>$request->veh_no,'narration'=>$request->narration,'weigh1'=>$request->weigh1,'weigh2'=>$request->weigh2,'weigh3'=>$request->weigh3,'bro_comm'=>$request->bro_comm,'bro_comm_total'=>$request->bro_comm_total,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"purchase_id"=>'',"paddy_id"=>'',"wgt"=>'',"qty"=>'',"totalwgt"=>'',"rate"=>'',"total"=>''));
        return view('admin.Purchase.index',['warehouseList'=>$warehouseList,'brokerlist'=>$result2,'vendorlist'=>$result,'categoryList'=>$categoryList,'productList'=>$productList,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'purchase_type'=>$purchase_type,'purchase_action'=>$purchase_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	
	public function PaddyEdit(Request $request){
		$purchase_id 	= $request->purchase_id;
		$actionUrl    = '/admin/purchase-edit/'.$purchase_id;
		$redirectUrl  = '/admin/purchase';
		$purchase_type ='Paddy';
		$purchase_type_id =1;
		$purchase_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$purchase_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();

        $broker  = User::select('users.*');
        $broker->join('user_details', 'user_details.user_id', '=', 'users.id');
        $broker->where('users.status','!=',0);
		$broker->where('user_details.account',5);
        $broker->orderBy('users.date_time', 'desc');
        $totalRecords2 	= count($broker->get());
        $data2    		= $broker->get();

        $productList  = Paddy::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
        $categoryList = ProductCategories::where('status', 1)->orderBy('category_name')->get();
        $result = $result2 = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
        if(!empty($data2)){
            foreach ($data2 as $index=>$brokers) {
                $result2[$index]['first_name'] = $brokers->userDetails->first_name;
                $result2[$index]['last_name'] = $brokers->userDetails->last_name;
                $result2[$index]['id'] = $brokers->id;
            }
        }		
        $inv_details  = TblPurchase::where('purchase_id', $purchase_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = PurchasesItem::where('purchase_id', $purchase_id)->get();
		$purchaseMaxId = TblPurchase::orderBy('purchase_id', 'desc')->value('purchase_id');
		if(isset($purchaseMaxId) && $purchaseMaxId!='') {
			$idPostfix = (int)$purchaseMaxId+1;
			$nextInvoiceId = 'STI_PADDY_THIRDPARTY_'.STR_PAD((string)$idPostfix,5,"0",STR_PAD_LEFT);
		}
		if(isset($_POST['_token'])) {
			$validator = Validator::make($request->all(), [
				'supplname' => 'required',
				'billdate' => 'required',
				'total' => 'required',
				'invoice_no' => 'required',
				'warehouse' => 'required'
			]);
			if($validator->passes()) {
				$purchase_source = $request->purchase_source;
				$inv_no1 = $request->invoice_no;
				$billdate 	= $request->billdate;
				$supplname 	= $request->supplname;
				$warehouse_id 	= $request->warehouse;
				$broker_id 	= $request->broker;
				$veh_no 	= $request->veh_no;
				$bro_comm   = $request->bro_comm;
				$bro_comm_total   = $request->bro_comm_total;
				$weigh1 	= $request->weigh1;
				$weigh2 	= $request->weigh2;
				$weigh3 	= $request->weigh3;
				$narration 	= $request->narration;
				$product_id 	= $request->product_id;
				$wgt  	= $request->wgt;
				$qty 	= $request->qty;
				$totalwgt 	= $request->totalwgt;
				$rate 	= $request->rate;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$bro_comm 	= $request->bro_comm;
				$bro_comm_total 	= $request->bro_comm_total;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;
				
				$model  = TblPurchase::where('purchase_id',$purchase_id)->first();
				$model->purchase_type_id = $purchase_type_id;
				$model->purchase_source = $purchase_source;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id      = $warehouse_id;
				$model->broker_id      = $broker_id;
				$model->veh_no      = $veh_no;
				$model->weigh1      = $weigh1;
				$model->weigh2      = $weigh2;
				$model->weigh3      = $weigh3;
				$model->narration   = $narration;
				$model->bro_comm   = $bro_comm;
				$model->bro_comm_total   = $bro_comm_total;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save()) {
					$salesitems = PurchasesItem::where('purchase_id', $purchase_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
						foreach($salesitems as $salesitem) {
							PurchasesItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_id);$i++){
						if($product_id[$i] != '0' && $product_id[$i] != 0){
							$SalesItem    			  = new PurchasesItem;
							$SalesItem->purchase_id    = $model->purchase_id;
							$SalesItem->paddy_id    = $product_id[$i];
							$SalesItem->wgt    = $wgt[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = $totalwgt[$i];
							$SalesItem->rate      = $rate[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'Paddy Purchase Details Updated Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to update Paddy Purchase information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to update Paddy Purchase information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Purchase.index',['vendorlist'=>$result,'vendor_details'=>$vendor_details,'warehouseList'=>$warehouseList,'brokerlist'=>$result2,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'categoryList'=>$categoryList,'productList'=>$productList,'actionUrl'=>$actionUrl,'purchase_type'=>$purchase_type,'purchase_action'=>$purchase_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
    public function PaddyView(Request $request){
        $purchase_id 	= $request->purchase_id;
		$purchase_type ='Paddy';
		$purchase_type_id =1;
		$purchase_action ='View';
      
        $categoryList = ProductCategories::where('status', 1)->orderBy('category_name')->get();   
        $inv_details  = TblPurchase::where('purchase_id', $purchase_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = PurchasesItem::where('purchase_id', $purchase_id)->get();
		$brokerlistGet    = UserDetails::where('user_id', $inv_details['broker_id'])->where('user_details.account',5)->first();
		$productList  = Paddy::where('status', 1)->get();
		$brokerlist='';
		$warehouseList='';
		if(isset($brokerlistGet['first_name']) && $brokerlistGet['first_name']!="") {
			$brokerlist  = $brokerlistGet['first_name']." ". $brokerlistGet['last_name'] ;
		}
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
        return view('admin.Purchase.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'warehouseList'=>$warehouseList,'brokerlist'=>$brokerlist,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'categoryList'=>$categoryList,'productList'=>$productList,'purchase_type'=>$purchase_type,'purchase_action'=>$purchase_action]);
    }
	// Delete
    public function PaddyDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblPurchase::where('purchase_id', $id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Paddy Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
    public function getVendordetails(Request $request) {
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
	public function BagIndex()
    {
        return view('admin.Purchase.Bag.List');
    }
	public function BagList(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        $model  = TblPurchase::select('tbl_purchase.*');
		$model->join('users', 'users.id', '=', 'tbl_purchase.user_id');
        $model->join('user_details', 'user_details.user_id', '=', 'tbl_purchase.vendor_id');
        $model->where('users.status','!=',0);
        $model->where('users.user_type',4);
        $model->where('users.type',2);
        $model = TblPurchase::where('tbl_purchase.purchase_type_id',2)->where('delete_flag',0)->where('status',1);
		// Filters Parameters
        parse_str($_POST['formData'], $filterArray);
		$invoice_no = "";
		if(isset($filterArray['invoice_no'])){
            $invoice_no = trim($filterArray['invoice_no']);
        }
		if($invoice_no!=""){
            $model->where('tbl_purchase.invoice_no','like','%'.$invoice_no.'%');
        }
        $model->orderBy('tbl_purchase.created_at', 'desc');
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
                $action = '<a href="'.url('/admin/bag/purchase-view/'.$user->purchase_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>&nbsp;&nbsp;<a href="'.url('/admin/bag/purchase-edit/'.$user->purchase_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;<button title="Delete" data-id="'.$user->purchase_id.'"   data-url="'.url('/admin/bag-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
	public function BagAdd(Request $request) {
		$actionUrl    = 'bag/purchase/add/';
		$redirectUrl  = '/admin/bag/purchase';
		$purchase_type ='Bag';
		$purchase_type_id =2;
		$purchase_action ='Add';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$purchase_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$purchaseMaxId = TblPurchase::orderBy('purchase_id', 'desc')->value('purchase_id');
		$nextInvoiceId='';
		if(isset($purchaseMaxId) && $purchaseMaxId!='') {
			$idPostfix = (int)$purchaseMaxId+1;
			$nextInvoiceId = 'STI_PADDY_THIRDPARTY_'.STR_PAD((string)$idPostfix,5,"0",STR_PAD_LEFT);
		}
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
				$bro_comm_total   = $request->bro_comm_total;
				$narration 	= $request->narration;
				$product_type_id 	= $request->product_type_id;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$rate 	= $request->rate;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$bro_comm_total 	= $request->bro_comm_total;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model    			  = new TblPurchase;
				$model->purchase_type_id = $purchase_type_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->narration   = $narration;
				$model->bro_comm_total   = $bro_comm_total;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					for($i=0;$i<count($product_type_id);$i++){
						if($product_type_id[$i] != '0' && $product_type_id[$i] != 0){

							$SalesItem    			 = new PurchasesItem;
							$SalesItem->purchase_id  = $model->purchase_id;
							$SalesItem->paddy_id     = $product_type_id[$i];
							$SalesItem->wgt    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = NULL;
							$SalesItem->rate      = $rate[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'Bag Purchase Details Created Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Create Bag Purchase information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Create Bag Purchase information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
		$inv_details = array('invoice_no'=>$request->invoice_no,'inv_date'=>date('Y-m-d'),'vendor_id'=>$request->supplname,'warehouse_id'=>$request->warehouse,'narration'=>$request->narration,'bro_comm_total'=>$request->bro_comm_total,'sub_total'=>$request->sub_total,'total'=>$request->total);
		$vendor_details = array('current_balance'=>$request->balance,'address'=>$request->address);
		$salesitems = array(array("id"=>'',"purchase_id"=>'',"paddy_id"=>'',"wgt"=>'',"qty"=>'',"rate"=>'',"total"=>''));
        return view('admin.Purchase.Bag.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>array(),'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'purchase_type'=>$purchase_type,'purchase_action'=>$purchase_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	public function BagView(Request $request){
        $purchase_id 	= $request->purchase_id;
		$purchase_type ='Bag';
		$purchase_type_id =2;
		$purchase_action ='View';
     
        $inv_details  = TblPurchase::where('purchase_id', $purchase_id)->where('delete_flag',0)->first();
		$warehouseListGet  = Warehouses::where('status', 1)->where('id', $inv_details['warehouse_id'])->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = PurchasesItem::where('purchase_id', $purchase_id)->get();
		$riceList  = Rice::where('status', 1)->get();
		if(isset($warehouseListGet['name']) && $warehouseListGet['name']!="") {
			$warehouseList  = $warehouseListGet['name'];
		}
		$result=array();
        if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $bagDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $bagDetails->name;
				}
				$riceBagName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result[$index]['name'] = $riceBagName;
                $result[$index]['id'] = $rice->id;
            }
        }
		return view('admin.Purchase.Bag.view',['vendorlist'=>'','vendor_details'=>$vendor_details,'riceList'=>$result,'warehouseList'=>$warehouseList,'inv_details'=>$inv_details,'salesitems'=>$salesitems,'purchase_type'=>$purchase_type,'purchase_action'=>$purchase_action]);
    }
	public function BagEdit(Request $request) {
		$purchase_id 	= $request->purchase_id;
		$actionUrl    = 'admin/bag/purchase-edit/'.$purchase_id;
		$redirectUrl  = '/admin/bag/purchase';
		$purchase_type ='Bag';
		$purchase_type_id =2;
		$purchase_action ='Edit';
        $user  = User::select('users.*');
        $user->join('user_details', 'user_details.user_id', '=', 'users.id');
        $user->where('users.status','!=',0);
        $user->where('users.user_type',4);
        $user->where('users.type',1);
        $user->where('user_details.account',$purchase_type_id);
        $user->orderBy('users.date_time', 'desc');
        $totalRecords 	= count($user->get());
        $data    		= $user->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
		$inv_details  = TblPurchase::where('purchase_id', $purchase_id)->where('delete_flag',0)->first();
        $vendor_details =  UserDetails::where('user_id', $inv_details['vendor_id'])->first();
        $salesitems = PurchasesItem::where('purchase_id', $purchase_id)->get();
		$purchaseMaxId = TblPurchase::orderBy('purchase_id', 'desc')->value('purchase_id');
		$riceList  = Rice::where('status', 1)->get();
		$nextInvoiceId='';
		if(isset($purchaseMaxId) && $purchaseMaxId!='') {
			$idPostfix = (int)$purchaseMaxId+1;
			$nextInvoiceId = 'STI_PADDY_THIRDPARTY_'.STR_PAD((string)$idPostfix,5,"0",STR_PAD_LEFT);
		}
        $result = $result2 = array();
        if(!empty($data)){
            foreach ($data as $index=>$vendors) {
                $result[$index]['first_name'] = $vendors->userDetails->first_name;
                $result[$index]['last_name'] = $vendors->userDetails->last_name;
                $result[$index]['id'] = $vendors->id;
            }
        }
		if(!empty($riceList)){
            foreach ($riceList as $index=>$rice) {
				if(isset($rice->bag_type)){
                    $bagDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $bagDetails->name;
				}
				$riceBagName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
                $result2[$index]['name'] = $riceBagName;
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
				$bro_comm_total   = $request->bro_comm_total;
				$narration 	= $request->narration;
				$product_type_id 	= $request->product_type_id;
				$product_id 	= $request->product_id;
				$qty 	= $request->qty;
				$rate 	= $request->rate;
				$total 	= $request->total;
				$sub_total 	= $request->sub_total;
				$bro_comm_total 	= $request->bro_comm_total;
				$total_amount 	= $request->total_amount;
				$loginId  = Auth::guard('admin')->user()->id;

				$model  = TblPurchase::where('purchase_id',$purchase_id)->first();
				$model->purchase_type_id = $purchase_type_id;
				$model->invoice_no    = $inv_no1;
				$model->inv_date    = $billdate;
				$model->vendor_id    = $supplname;
				$model->user_id      = $loginId;
				$model->warehouse_id  = $warehouse_id;
				$model->narration   = $narration;
				$model->bro_comm_total   = $bro_comm_total;
				$model->sub_total   = $sub_total;
				$model->total       = $total_amount;
				$model->status      = 1;
				$model->delete_flag = 0;
				$model->save();
				if($model->save())
				{
					$salesitems = PurchasesItem::where('purchase_id', $purchase_id)->get();					
					if(isset($salesitems) && !empty($salesitems)) {
						foreach($salesitems as $salesitem) {
							PurchasesItem::where('id', $salesitem->id)->delete();
						}
					}
					for($i=0;$i<count($product_type_id);$i++){
						if($product_type_id[$i] != '0' && $product_type_id[$i] != 0){

							$SalesItem    			 = new PurchasesItem;
							$SalesItem->purchase_id  = $model->purchase_id;
							$SalesItem->paddy_id     = $product_type_id[$i];
							$SalesItem->wgt    = $product_id[$i];
							$SalesItem->qty    = $qty[$i];
							$SalesItem->totalwgt    = NULL;
							$SalesItem->rate      = $rate[$i];
							$SalesItem->total       = $total[$i];
							$SalesItem->save();
						}
					}
					if ($model && $SalesItem){
						return response()->json(['success'=>'Bag Purchase Details Updated Successfully.']);
					} else {
						return response()->json(['singleerror'=>'Failed to Update Bag Purchase information. Try again after sometime.']);
					}
				} else {
					return response()->json(['singleerror'=>'Failed to Updat Bag Purchase information. Try again after sometime.']);
				}
			}
			else {
				return response()->json(['error'=>$validator->errors()->all()]);
			}
		}
        return view('admin.Purchase.Bag.index',['warehouseList'=>$warehouseList,'vendorlist'=>$result,'riceList'=>$result2,'inv_details'=>$inv_details,'vendor_details'=>$vendor_details,'salesitems'=>$salesitems,'actionUrl'=>$actionUrl,'purchase_type'=>$purchase_type,'purchase_action'=>$purchase_action,'redirectUrl'=>$redirectUrl,'nextInvoiceId'=>$nextInvoiceId]);
    }
	// Delete
    public function BagDelete(Request $request) {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model      = TblPurchase::where('purchase_id', $id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Bag Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	public function getRicedetailsbag() {
		$bag_type='';
        $riceList  = Rice::where('status', 1)->get();
		foreach ($riceList as $rice) {
			if(isset($rice->bag_type)){
                    $bagDetails 	= Bag::where('id',$rice->bag_type)->first();
                    $bag_type = $bagDetails->name;
            }
			$riceBagName = ($bag_type) ? $rice->original_name.' '.$bag_type : $rice->original_name;
			echo '<option value="'.$rice->id.'">'.$riceBagName.'</option>';
        }
        //return response()->json($response);
    }
	public function production()
    {
        $productList  = Bag::where('status', 1)->get();
        $VendorType  = VendorType::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
        $next_ids  = UniqueNo::where('status', 1)->where('name', 'production')->first();
        $next_id  = $next_ids->number;

        $SourceItems =  SourceItems::where('production_id', $next_id)->get();
        $i = 0;
        foreach($SourceItems As $SourceItem){
            $SourceItems[$i]['item_id']=$SourceItem->get_products->name;
            $SourceItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
            $i++;
        }

        $DestinationItems =  DestinationItems::where('production_id', $next_id)->get();
        $j = 0;
        foreach($DestinationItems As $SourceItem){
            $DestinationItems[$j]['item_id']=$SourceItem->get_products->name;
            $DestinationItems[$j]['warehouse_id']=$SourceItem->get_warehouse->name;
            $i++;
        }

        $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
        $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');
        $dessum = DestinationItems::where('production_id', $next_id)->sum('amount');

        return view('admin.Purchase.production',['srcqty'=>$srcqty,'srcsum'=>$srcsum,'dessum'=>$dessum,'SourceItems'=>$SourceItems,'DestinationItems'=>$DestinationItems,'next_id'=>$next_id,'VendorType'=>$VendorType,'vendorlist'=>$productList,'warehouseList'=>$warehouseList]);
    }

    public function productionview(Request $request)
    {
        $productList  = Bag::where('status', 1)->get();
        $VendorType  = VendorType::where('status', 1)->get();
        $warehouseList  = Warehouses::where('status', 1)->get();
        $next_id	= $request->next_id;

        $SourceItems =  SourceItems::where('production_id', $next_id)->get();
        $i = 0;
        foreach($SourceItems As $SourceItem){
            $SourceItems[$i]['item_id']=$SourceItem->get_products->name;
            $SourceItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
            $i++;
        }

        $DestinationItems =  DestinationItems::where('production_id', $next_id)->get();
        $j = 0;
        foreach($DestinationItems As $SourceItem){
            $DestinationItems[$j]['item_id']=$SourceItem->get_products->name;
            $DestinationItems[$j]['warehouse_id']=$SourceItem->get_warehouse->name;
            $i++;
        }

        $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
        $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');
        $dessum = DestinationItems::where('production_id', $next_id)->sum('amount');

        return view('admin.Purchase.productionview',['srcqty'=>$srcqty,'srcsum'=>$srcsum,'dessum'=>$dessum,'SourceItems'=>$SourceItems,'DestinationItems'=>$DestinationItems,'next_id'=>$next_id,'VendorType'=>$VendorType,'vendorlist'=>$productList,'warehouseList'=>$warehouseList]);
    }
	
    public function getitemlist(Request $request)
    {
        $type	= $request->type;

        $vendor_details =  Bag::where('type', $type)->get();
        $response = array(
            "vendor_details" => $vendor_details
        );
        return response()->json($response);

    }

    public function addsrcitems(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'srcVendorType' => 'required',
            'srcitemlist' => 'required',
            'srcwarehouseid' => 'required',
            'srcqty' => 'required',
            'srcrate' => 'required',
            'next_id' => 'required'
        ]);

        if($validator->passes())
        {
            $srcVendorType	= $request->srcVendorType;
            $srcitemlist	= $request->srcitemlist;
            $srcwarehouseid	= $request->srcwarehouseid;
            $srcqty	= $request->srcqty;
            $srcrate	= $request->srcrate;
            $next_id	= $request->next_id;

            $model =  TblProduction::find($next_id);
            if($model){
                $model->source_qty    = 0;
                $model->id    = $next_id;
                $model->source_total    = 0;
                $model->destination_total    = 0;
                $model->narration    = 'NA';
                $model->save();
            }else{
                $model =  new TblProduction;
                $model->source_qty    = 0;
                $model->id    = $next_id;
                $model->source_total    = 0;
                $model->destination_total    = 0;
                $model->narration    = 'NA';
                $model->save();
            }


            $next_id=$model->id;

            $amount = $srcqty * $srcrate;

            $SrcItem    = new SourceItems;
            $SrcItem->item_id    = $srcitemlist;
            $SrcItem->warehouse_id    = $srcwarehouseid;
            $SrcItem->qty    = $srcqty;
            $SrcItem->rate    = $srcrate;
            $SrcItem->amount    = $amount;
            $SrcItem->production_id    = $next_id;
            $SrcItem->save();

            $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
            $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');

            $price_model = TblProduction::where('id', $next_id)->first();
            if(!empty($price_model))
            {
                $price_model->source_qty = $srcqty;
                $price_model->source_total = $srcsum;
                $price_model->save();
            }

            $SourceItems =  SourceItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($SourceItems As $SourceItem){
                $SourceItems[$i]['item_id']=$SourceItem->get_products->name;
                $SourceItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "SourceItems" => $SourceItems,
                "srcqty" => $srcqty,
                "srcsum" => $srcsum
            );
        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function adddesitems(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'srcVendorType' => 'required',
            'srcitemlist' => 'required',
            'srcwarehouseid' => 'required',
            'srcqty' => 'required',
            'srcrate' => 'required',
            'next_id' => 'required'
        ]);

        if($validator->passes())
        {
            $srcVendorType	= $request->srcVendorType;
            $srcitemlist	= $request->srcitemlist;
            $srcwarehouseid	= $request->srcwarehouseid;
            $srcqty	= $request->srcqty;
            $srcrate	= $request->srcrate;
            $next_id	= $request->next_id;

            $model =  TblProduction::find($next_id);
            if($model){
                $model->source_qty    = 0;
                $model->id    = $next_id;
                $model->source_total    = 0;
                $model->destination_total    = 0;
                $model->narration    = 'NA';
                $model->save();
            }else{
                $model =  new TblProduction;
                $model->source_qty    = 0;
                $model->id    = $next_id;
                $model->source_total    = 0;
                $model->destination_total    = 0;
                $model->narration    = 'NA';
                $model->save();
            }


            $next_id=$model->id;

            $amount = $srcqty * $srcrate;

            $SrcItem    = new DestinationItems;
            $SrcItem->item_id    = $srcitemlist;
            $SrcItem->warehouse_id    = $srcwarehouseid;
            $SrcItem->qty    = $srcqty;
            $SrcItem->rate    = $srcrate;
            $SrcItem->amount    = $amount;
            $SrcItem->production_id    = $next_id;
            $SrcItem->save();

            $srcqty = DestinationItems::where('production_id', $next_id)->sum('qty');
            $srcsum = DestinationItems::where('production_id', $next_id)->sum('amount');

            $price_model = TblProduction::where('id', $next_id)->first();
            if(!empty($price_model))
            {
                $price_model->destination_total = $srcsum;
                $price_model->save();
            }

            $DestinationItems =  DestinationItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($DestinationItems As $SourceItem){
                $DestinationItems[$i]['item_id']=$SourceItem->get_products->name;
                $DestinationItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "DestinationItems" => $DestinationItems,
                "desqty" => $srcqty,
                "dessum" => $srcsum
            );
        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function changesrcqty(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'qty' => 'required',
            'id' => 'required'
        ]);

        if($validator->passes())
        {
            $qty	= $request->qty;
            $id	= $request->id;

            $model =  SourceItems::find($id);
            if($model){
                $model->qty    = $qty;
                $model->amount    = $qty*$model->rate;
                $model->save();
            }
            $next_id = $model->production_id ;

            $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
            $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');

            $SourceItems =  SourceItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($SourceItems As $SourceItem){
                $SourceItems[$i]['item_id']=$SourceItem->get_products->name;
                $SourceItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "SourceItems" => $SourceItems,
                "srcqty" => $srcqty,
                "srcsum" => $srcsum
            );

        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function changesrcrate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'rate' => 'required',
            'id' => 'required'
        ]);

        if($validator->passes())
        {
            $rate	= $request->rate;
            $id	= $request->id;

            $model =  SourceItems::find($id);
            if($model){
                $model->rate    = $rate;
                $model->amount    = $rate*$model->qty;
                $model->save();
            }
            $next_id = $model->production_id ;

            $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
            $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');

            $SourceItems =  SourceItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($SourceItems As $SourceItem){
                $SourceItems[$i]['item_id']=$SourceItem->get_products->name;
                $SourceItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "SourceItems" => $SourceItems,
                "srcqty" => $srcqty,
                "srcsum" => $srcsum
            );

        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function deletesrcitem(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if($validator->passes())
        {
            $id	= $request->id;

            $model =  SourceItems::find($id);
            $next_id = $model->production_id ;
            $model->delete(); 

            $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
            $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');

            $SourceItems =  SourceItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($SourceItems As $SourceItem){
                $SourceItems[$i]['item_id']=$SourceItem->get_products->name;
                $SourceItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "SourceItems" => $SourceItems,
                "srcqty" => $srcqty,
                "srcsum" => $srcsum
            );

        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function changedesqty(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'qty' => 'required',
            'id' => 'required'
        ]);

        if($validator->passes())
        {
            $qty	= $request->qty;
            $id	= $request->id;

            $model =  DestinationItems::find($id);
            if($model){
                $model->qty    = $qty;
                $model->amount    = $qty*$model->rate;
                $model->save();
            }
            $next_id = $model->production_id ;

            $srcqty = DestinationItems::where('production_id', $next_id)->sum('qty');
            $srcsum = DestinationItems::where('production_id', $next_id)->sum('amount');

            $DestinationItems =  DestinationItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($DestinationItems As $SourceItem){
                $DestinationItems[$i]['item_id']=$SourceItem->get_products->name;
                $DestinationItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "DestinationItems" => $DestinationItems,
                "desqty" => $srcqty,
                "dessum" => $srcsum
            );

        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function changedesrate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'rate' => 'required',
            'id' => 'required'
        ]);

        if($validator->passes())
        {
            $rate	= $request->rate;
            $id	= $request->id;

            $model =  DestinationItems::find($id);
            if($model){
                $model->rate    = $rate;
                $model->amount    = $rate*$model->qty;
                $model->save();
            }
            $next_id = $model->production_id ;

            $srcqty = DestinationItems::where('production_id', $next_id)->sum('qty');
            $srcsum = DestinationItems::where('production_id', $next_id)->sum('amount');

            $DestinationItems =  DestinationItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($DestinationItems As $SourceItem){
                $DestinationItems[$i]['item_id']=$SourceItem->get_products->name;
                $DestinationItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "DestinationItems" => $DestinationItems,
                "desqty" => $srcqty,
                "dessum" => $srcsum
            );

        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function deletedesitem(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if($validator->passes())
        {
            $id	= $request->id;

            $model =  DestinationItems::find($id);
            $next_id = $model->production_id ;
            $model->delete();

            $srcqty = DestinationItems::where('production_id', $next_id)->sum('qty');
            $srcsum = DestinationItems::where('production_id', $next_id)->sum('amount');

            $DestinationItems =  DestinationItems::where('production_id', $next_id)->get();
            $i = 0;
            foreach($DestinationItems As $SourceItem){
                $DestinationItems[$i]['item_id']=$SourceItem->get_products->name;
                $DestinationItems[$i]['warehouse_id']=$SourceItem->get_warehouse->name;
                $i++;
            }
            $response = array(
                "DestinationItems" => $DestinationItems,
                "desqty" => $srcqty,
                "dessum" => $srcsum
            );

        }else
        {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        return response()->json($response);

    }

    public function formsubmit(Request $request)
    {

        $next_id	= $request->next_id;
        $srcqty = SourceItems::where('production_id', $next_id)->sum('qty');
        $srcsum = SourceItems::where('production_id', $next_id)->sum('amount');
        $dessum = DestinationItems::where('production_id', $next_id)->sum('amount');

        $model =  TblProduction::find($next_id);
        if($model){
            $model->source_qty    = $srcqty;
            $model->source_total    = $srcsum;
            $model->destination_total    =$dessum;
            $model->status    = 1;
            $model->save();
        }
        $next_id = $model->id;

        $next_ids  = UniqueNo::where('status', 1)->where('name', 'production')->first();
        $next_ids->number  = $next_id+1;
        $next_ids->save();
        return view('admin.Purchase.productionList');
    }

    public function productiongetList(){
        return view('admin.Purchase.productionList');
    }

    public function productionList(Request $request){

        $start 	= $request->start;
        $length = $request->length;

        $model  = TblProduction::where('status',1);
        // Get data with pagination
        $model->orderBy('created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        
        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {

                $id  	= $user->id;
                $source_qty   	= $user->source_qty;
                $source_total   	= $user->source_total;
                $destination_total   	= $user->destination_total;
                $created_at   	= $user->created_at;

                $result[$index]['snumber'] = $start+1;
                $result[$index]['id'] = $id;
                $result[$index]['source_qty'] = $source_qty;
                $result[$index]['source_total'] = $source_total;
                $result[$index]['destination_total'] = $destination_total;
                $result[$index]['created_at'] = $created_at;

                // action buttons
                $action = '<a href="'.url('/admin/production-view/'.$id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-eye"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'" data-url="'.url('/admin/delete-production').'" class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
    /***********************END************************/
    public function purchase_print(Request $request){

        $bill_id 	= $request->bill_id;

        $inv_details  = TblSales::where('bill_id', $bill_id)->where('delete_flag',0)->first();

        $dealer_details =  UserDetails::where('user_id', $inv_details['dealer_id'])->first();

        $salesitems = SalesItem::where('bill_id', $bill_id)->get();

        return view('admin.Purchase.invoicepdf',['vendorlist'=>$dealer_details,'inv_details'=>$inv_details,'salesitems'=>$salesitems]);
    }
}
