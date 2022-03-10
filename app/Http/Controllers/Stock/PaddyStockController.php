<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\ProductCategories;
use App\Models\Products;
use App\Models\InvoiceSettings;
use App\Models\TblSales;
use App\Models\Paddy;
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
use App\Models\BillsPurchases;
use App\helpers;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Auth;

class PaddyStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Index()
    {
        return view('stock.Paddy.List');
    }

    public function PaddyStockList(Request $request)
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
        
        $warehouses  = Warehouses::select('*')->where('status','!=',0);
        // $model->where('status','!=',0);


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
                // $action = '<a href="'.url('/admin/purchase-view/'.$user->purchase_id).'" title="View" class="btn btn-icon btn-sm btn-info update-button"><i class="fas fa-eye"></i></a>
                // &nbsp;&nbsp; <a href="'.url('/admin/purchase-edit/'.$user->purchase_id).'" title="View" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fas fa-edit"></i></a>&nbsp;&nbsp;
                // <button title="Delete" data-id="'.$user->purchase_id.'"   data-url="'.url('/admin/purchase-delete/').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
                // $result[$index]['action'] = $action;
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
// Create
public function PaddyFilter(Request $request)
{

    $actionUrl    = '/stock/paddystocklist';
    $redirectUrl  = '/stock/paddystocklist';
    $historyUrl  = '/stock/paddyhistory';
    $actionName   = 'List';
    $type 		  = 1;
    
    $start 	= $request->start;
    $length = $request->length;
    
    $warehouseList  = Warehouses::where('status', 1)->get(); 
    $paddyItems     = Paddy::where('status', 1)->where('type', 1)->get(); 
    
    


    
    // Filters Parameters
    // parse_str($_POST['formData'], $filterArray);
    // parse_str($_POST['form'], $filterArray);
    $warehouse_search = ($request->warehouse_search?$request->warehouse_search:"");
    $paddy_item_search = ($request->paddy_item_search?$request->paddy_item_search:"");

    $data = "";
    $sql = "";
    $model = "";
    if(isset($_POST['_token']) && $warehouse_search!="" && $warehouse_search!="all") {
       
            // $model  = TblPurchase::select('w.name as warehousesname', 'pp.name as paddyItem', DB::raw('SUM(pi.wgt) as weight, SUM(pi.qty) as qty'), 'tbl_purchase.invoice_no');
            $model  = TblPurchase::select('w.name as warehousesname', 'pp.name as paddyItem', 'pi.paddy_id', 'pi.wgt as weight', 'pi.qty as qty',
             'pi.rate as rate',  'pi.total as total_amt','tbl_purchase.invoice_no');
            
    }else {
        // echo "NO_TOKEN - ALL:";        
        $model  = TblPurchase::select('w.id as wh_id','w.name as warehousesname', 'pp.name as paddyItem', 'pi.paddy_id', 'pi.wgt as weight', 'pi.qty as qty',
        'pi.rate as rate', 'pi.total as total_amt', 'tbl_purchase.invoice_no');

        $model->leftJoin('warehouses as w', 'w.id', '=', 'tbl_purchase.warehouse_id');
        $model->leftJoin('purchases_items as pi', 'pi.purchase_id', '=', 'tbl_purchase.purchase_id'); 
        $model->leftJoin('paddy_products as pp', 'pi.paddy_id', '=', 'pp.id'); 
        $model->where('tbl_purchase.purchase_type_id', '=', '1');
        
    }   
    
    if(isset($warehouse_search) && $warehouse_search!="" && $warehouse_search !="all" ){
        $model->leftJoin('warehouses as w', 'w.id', '=', 'tbl_purchase.warehouse_id');        
        $model->leftJoin('purchases_items as pi', 'pi.purchase_id', '=', 'tbl_purchase.purchase_id');  
        $model->leftJoin('paddy_products as pp', 'pi.paddy_id', '=', 'pp.id');       
        $model->where('tbl_purchase.purchase_type_id', '=', '1');
        $model->where('tbl_purchase.warehouse_id', '=', ''.$request->warehouse_search.' ');
    }
    if(isset($paddy_item_search) && $paddy_item_search !="" && $paddy_item_search >= 1 ) {
        $model->where('pp.id', '=', ''.$paddy_item_search.' ');
    }
    
    $data   = $model->get();
    $sql    = $model->toSql();

   
    return view('stock.PaddyStockList',['model'=>$model,'data'=>$data, 'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,
    'historyUrl'=>$historyUrl,
    'name'=>$actionName, 'type'=>$type, 'warehouseList'=>$warehouseList,
     'paddyItems'=>$paddyItems,
      'ware_house'=>$warehouse_search,
      'paddy_item'=>$paddy_item_search,
      'sql_query'=>$sql]);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function PaddyHistory(Request $request, $rid, $whid)
    {
        $start 	= $request->start;
        $length = $request->length;
        $backUrl      = '/stock/paddystocklist';
        $actionName   = 'List';
        $type 		  = 1;
        
        $warehouseList  = Warehouses::where('status', 1)->get(); 
        $paddyItems     = Paddy::where('status', 1)->where('type', 1)->get();

        
        // Filters Parameters
        // parse_str($_POST['formData'], $filterArray);
        // parse_str($_POST['form'], $filterArray);
        $warehouse_search = ($request->warehouse_search?$request->warehouse_search:"");
        $paddy_item_search = ($request->paddy_item_search?$request->paddy_item_search:"");

        $data = "";
        $sql = "";
        $model = "";

        if(isset($_POST['_token']) && $warehouse_search!="" && $warehouse_search!="all") {
        //DB::raw('SUM(pi.wgt) as weight, SUM(pi.qty) as qty')
                $model  = PurchasesItem::select('w.name as warehousesname', 'rt.original_name as ricename', 'bt.name as kg',
                  DB::raw('SUM(purchases_items.qty) as qty'), 'u.first_name as user_name');
                
        } else {
            // echo "NO_TOKEN - ALL:";
        
            $model  = BillsPurchases::select('bill_purchases.id','bill_purchases.bill_id','bill_purchases.wh_id','tb.invoice_no','tb.inv_date',
            'w.name as warehousesname','pp.name as paddyname', 
            'bill_purchases.qty as qty','bill_purchases.price','bill_purchases.final_price','bill_purchases.totalwgt','bill_purchases.total','u.first_name as customer_name' );


            $model->leftJoin('paddy_products as pp', 'pp.id', '=', 'bill_purchases.product_id');
            // $model->leftJoin('rice_types as rt', 'rt.id', '=', 'bill_purchases.product_id');
            // $model->leftJoin('bag_types as bt', 'bt.id', '=', 'rt.bag_type');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'bill_purchases.wh_id');
            $model->leftJoin('tbl_billing as tb', 'tb.bill_id', '=', 'bill_purchases.bill_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tb.vendor_id');
            
            $model->where('tb.bill_type_id', '=', 11); // For paddy id(vendor type) 
            $model->where('bill_purchases.product_id', '=', $rid); // For paddy product id 
            $model->where('bill_purchases.wh_id', '=', $whid); //  For wh-whare house ID
        }   
        
        if(isset($warehouse_search) && $warehouse_search!="" && $warehouse_search !="all" ){
            
            $model->leftJoin('rice_types as rt', 'rt.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('bag_types as bt', 'bt.id', '=', 'rt.bag_type');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.user_id');
            
            $model->where('tp.warehouse_id', '=', ''.$request->warehouse_search.' ');

            $model->where('tp.purchase_type_id', '=', '3');// 3- For Rice
            $model->where('rt.id', '=', '14');// 3- For Rice
            $model->groupBy('w.id', 'rt.id', 'bt.id');
        }

       
        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data   = $model->get();
        $sql    = $model->toSql();        
    
        return view('stock.PaddyHistory',['model'=>$model,'data'=>$data, 'backUrl'=>$backUrl,
        'name'=>$actionName, 'type'=>$type, 'warehouseList'=>$warehouseList,
        'paddyItems'=>$paddyItems,
        'ware_house'=>$warehouse_search,
        'paddy_item'=>$paddy_item_search,
        'sql_query'=>$sql]);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
