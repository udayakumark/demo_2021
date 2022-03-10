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

class RiceStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // Get Rice Stock
    public function RiceFilter(Request $request)
    {

        $actionUrl    = '/stock/ricestocklist';
        $redirectUrl  = '/stock/ricestocklist';
        $historyUrl  = '/stock/ricehistory';
        $actionName   = 'List';
        $type 		  = 1;
        
        $start 	        = $request->start;
        $length         = $request->length;
        
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
                $model  = PurchasesItem::select('w.id as wh_id','w.name as warehousesname', 'rt.id as rice_id' ,'rt.original_name as ricename', 'bt.name as kg',
                  DB::raw('SUM(purchases_items.qty) as qty'), 'u.first_name as user_name');
                
        }else {
            // echo "NO_TOKEN - ALL:";
            $model  = PurchasesItem::select('w.id as wh_id','w.name as warehousesname', 'rt.id as rice_id' ,'rt.original_name as ricename', 'bt.name as kg',
             DB::raw('SUM(purchases_items.qty) as qty'), 'u.first_name as user_name' );

            $model->leftJoin('rice_types as rt', 'rt.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('bag_types as bt', 'bt.id', '=', 'rt.bag_type');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.user_id');
            
            $model->where('tp.purchase_type_id', '=', '3'); // 3- For Rice
            $model->groupBy('w.id', 'rt.id', 'bt.id');
        }   
        
        if(isset($warehouse_search) && $warehouse_search!="" && $warehouse_search !="all" ){
            
            $model->leftJoin('rice_types as rt', 'rt.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('bag_types as bt', 'bt.id', '=', 'rt.bag_type');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.user_id');
            
            $model->where('tp.warehouse_id', '=', ''.$request->warehouse_search.' ');

            $model->where('tp.purchase_type_id', '=', '3');// 3- For Rice
            $model->groupBy('w.id', 'rt.id', 'bt.id');
        }

        if(isset($paddy_item_search) && $paddy_item_search !="" && $paddy_item_search >= 1 ) {
            $model->where('pp.id', '=', ''.$paddy_item_search.' ');
        }
        
        $data   = $model->get();
        $sql    = $model->toSql();
        // echo $sql; exit();
    
        return view('stock.RiceStockList',['model'=>$model,'data'=>$data, 'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,
        'historyUrl'=>$historyUrl,
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
    public function RiceHistory(Request $request, $rid, $whid)
    {

        $backUrl      = '/stock/ricestocklist';
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
            'w.name as warehousesname','rt.original_name as ricename', 'bt.name as kg', 
            'bill_purchases.qty as qty','bill_purchases.price','bill_purchases.final_price','bill_purchases.total','u.first_name as customer_name' );

            $model->leftJoin('rice_types as rt', 'rt.id', '=', 'bill_purchases.product_id');
            $model->leftJoin('bag_types as bt', 'bt.id', '=', 'rt.bag_type');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'bill_purchases.wh_id');
            $model->leftJoin('tbl_billing as tb', 'tb.bill_id', '=', 'bill_purchases.bill_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tb.vendor_id');
            
            $model->where('bill_purchases.product_id', '=', $rid); // For Rice product id 
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

        if(isset($paddy_item_search) && $paddy_item_search !="" && $paddy_item_search >= 1 ) {
            $model->where('pp.id', '=', ''.$paddy_item_search.' ');
        }
        
        $data   = $model->get();
        $sql    = $model->toSql();
        // echo $sql; exit();
    
        return view('stock.RiceHistory',['model'=>$model,'data'=>$data, 'backUrl'=>$backUrl,
        'name'=>$actionName, 'type'=>$type, 'warehouseList'=>$warehouseList,
        'paddyItems'=>$paddyItems,
        'ware_house'=>$warehouse_search,
        'paddy_item'=>$paddy_item_search,
        'sql_query'=>$sql]);
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
