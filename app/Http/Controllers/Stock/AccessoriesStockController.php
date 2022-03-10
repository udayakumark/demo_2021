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
use App\helpers;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Auth;

class AccessoriesStockController extends Controller
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
    // Create
    public function AccessoriesFilter(Request $request)
    {

        $actionUrl      = '/stock/accessoriesstocklist';
        $redirectUrl    = '/stock/accessoriesstocklist';
        $historyUrl     = '/stock/accessorieshistory';
        $actionName     = 'List';
        $type 		    = 1;
        
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
                $model  = PurchasesItem::select('a.id as accessories_id', 'a.name as accessories', 'u.first_name as vendor_name',
                    DB::raw('SUM(purchases_items.qty) as qty, SUM(bi.qty) as used_qty, SUM(purchases_items.rate) as rate, SUM(purchases_items.total) as total_amt'), 'u.first_name as vendor_name');
                
        }else {
            // 'w.id as wh_id','w.name as warehousesname',
            $model  = PurchasesItem::select('a.id as accessories_id', 'a.name as accessories', 'u.first_name as vendor_name',
             DB::raw('SUM(purchases_items.qty) as qty, SUM(bi.qty) as used_qty, SUM(purchases_items.rate) as rate, SUM(purchases_items.total) as total_amt') );

            $model->leftJoin('accessories as a', 'a.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            // $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.vendor_id');
            $model->leftJoin('bill_items as bi', 'bi.bill_id', '=', 'purchases_items.paddy_id');
            
            $model->where('tp.purchase_type_id', '=', '7'); // 7- For Accessories
            $model->groupBy('a.id','bi.paddy_id');
            // $model->groupBy('w.id', 'a.id','bi.paddy_id');
        }   
        
        if(isset($warehouse_search) && $warehouse_search!="" && $warehouse_search !="all" ){
            
            $model->leftJoin('accessories as a', 'a.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            // $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.vendor_id');
            $model->leftJoin('bill_items as bi', 'bi.bill_id', '=', 'purchases_items.paddy_id');
            
            // $model->where('tp.warehouse_id', '=', ''.$request->warehouse_search.' ');

            $model->where('tp.purchase_type_id', '=', '7');// 7- For Accessories
            $model->groupBy( 'a.id','bi.paddy_id');
        }

        if(isset($paddy_item_search) && $paddy_item_search !="" && $paddy_item_search >= 1 ) {
            $model->where('pp.id', '=', ''.$paddy_item_search.' ');
        }
        
        $data   = $model->get();
        $sql    = $model->toSql();

    
        return view('stock.AccessoriesStockList',['model'=>$model,'data'=>$data, 'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,
        'historyUrl'=>$historyUrl,'name'=>$actionName, 'type'=>$type, 'warehouseList'=>$warehouseList,
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
    public function AccessoriesHistory(Request $request)
    {

        $actionUrl    = '/stock/accessoriesstocklist';
        $redirectUrl  = '/stock/accessoriesstocklist';
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
                $model  = PurchasesItem::select('w.name as warehousesname', 'a.name as accessories', 'u.first_name as vendor_name',
                    DB::raw('SUM(purchases_items.qty) as qty, SUM(purchases_items.rate) as rate, SUM(purchases_items.total) as total_amt'), 'u.first_name as vendor_name');
                
        }else {
            // echo "NO_TOKEN - ALL:";
            $model  = PurchasesItem::select('w.name as warehousesname', 'a.name as accessories', 'u.first_name as vendor_name',
             DB::raw('SUM(purchases_items.qty) as qty, SUM(purchases_items.rate) as rate, SUM(purchases_items.total) as total_amt') );

            $model->leftJoin('accessories as a', 'a.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.vendor_id');
            
            $model->where('tp.purchase_type_id', '=', '7'); // 7- For Accessories
            $model->groupBy('w.id', 'a.id');
        }   
        
        if(isset($warehouse_search) && $warehouse_search!="" && $warehouse_search !="all" ){
            
            $model->leftJoin('accessories as a', 'a.id', '=', 'purchases_items.paddy_id');
            $model->leftJoin('tbl_purchase as tp', 'tp.purchase_id', '=', 'purchases_items.purchase_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tp.warehouse_id');
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tp.vendor_id');
            
            $model->where('tp.warehouse_id', '=', ''.$request->warehouse_search.' ');

            $model->where('tp.purchase_type_id', '=', '7');// 7- For Accessories
            $model->groupBy('w.id', 'a.id');
        }

        if(isset($paddy_item_search) && $paddy_item_search !="" && $paddy_item_search >= 1 ) {
            $model->where('pp.id', '=', ''.$paddy_item_search.' ');
        }
        
        $data   = $model->get();
        $sql    = $model->toSql();

    
        return view('stock.AccessoriesStockList',['model'=>$model,'data'=>$data, 'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,
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
