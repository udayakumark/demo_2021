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

class BagStockController extends Controller
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
    public function BagFilter(Request $request)
    {

        $actionUrl    = '/stock/bagstocklist';
        $redirectUrl  = '/stock/bagstocklist';
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
        
                 $model  = TblPurchase::select('w.name as warehousesname', 'rt.original_name as bag_name','bt.name as kg', 'pi.qty as qty', 'pi.rate as rate', 
                'pi.total as total_amt', 'tbl_purchase.invoice_no','u.first_name as vendor_name');
                
        }else {
            // echo "NO_TOKEN - ALL:";
            $model  = TblPurchase::select('w.name as warehousesname', 'rt.original_name as bag_name','bt.name as kg', 'pi.qty as qty', 'pi.rate as rate', 
            'pi.total as total_amt', 'tbl_purchase.invoice_no','u.first_name as vendor_name', 'rt.original_name as bag_name','bt.name as kg' );
            
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tbl_purchase.vendor_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tbl_purchase.warehouse_id');
            $model->leftJoin('purchases_items as pi', 'pi.purchase_id', '=', 'tbl_purchase.purchase_id'); 
 
            $model->leftJoin('rice_types as rt', 'pi.wgt', '=', 'rt.id');
            $model->leftJoin('bag_types as bt', 'rt.bag_type', '=', 'bt.id');
            
            $model->where('tbl_purchase.purchase_type_id', '=', '2'); 
        }   
        
        if(isset($warehouse_search) && $warehouse_search!="" && $warehouse_search !="all" ){
            $model->leftJoin('user_details as u', 'u.user_id', '=', 'tbl_purchase.vendor_id');
            $model->leftJoin('warehouses as w', 'w.id', '=', 'tbl_purchase.warehouse_id');        
            $model->leftJoin('purchases_items as pi', 'pi.purchase_id', '=', 'tbl_purchase.purchase_id');  
            $model->where('tbl_purchase.warehouse_id', '=', ''.$request->warehouse_search.' ');

            $model->leftJoin('rice_types as rt', 'pi.wgt', '=', 'rt.id');
            $model->leftJoin('bag_types as bt', 'rt.bag_type', '=', 'bt.id');

            $model->where('tbl_purchase.purchase_type_id', '=', '2');
        }

        if(isset($paddy_item_search) && $paddy_item_search !="" && $paddy_item_search >= 1 ) {
            $model->where('pp.id', '=', ''.$paddy_item_search.' ');
        }
        
        $data   = $model->get();
        $sql    = $model->toSql();

    
        return view('stock.BagStockList',['model'=>$model,'data'=>$data, 'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,
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
