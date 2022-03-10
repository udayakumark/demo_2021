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
use App\Models\Accessories;
use App\Models\SalesItem;
use App\Models\PurchasesItem;
use App\Models\UserDetails;
use App\Models\Cashbook;
use App\Models\Warehouses;
use App\Models\VendorType;
use App\Models\TblProduction;
use App\Models\SourceItems;
use App\Models\DestinationItems;
use App\Models\TblBill;
use App\Models\BillsItem;
use App\Models\UniqueNo;
use App\helpers;
use Validator;
use Redirect;
use Illuminate\Support\Facades\Auth;

class AccessoryStockManualController extends Controller {
    public function Index() {
        return view('stock.accessory.Index');
    }
    // Ajax data load into table
    public function List(Request $request) {
        $start 	= $request->start;
        $length = $request->length;
        $bill_type_id =7;

        $model  = TblBill::select('a.name as accessoriesName','bi.qty as accessoriesQty','user_details.first_name','tbl_billing.bill_id', 'tbl_billing.inv_date',
         'tbl_billing.comments');
		$model->leftJoin('user_details', 'user_details.user_id', '=', 'tbl_billing.user_id');
		$model->leftJoin('bill_items as bi', 'bi.bill_id', '=', 'tbl_billing.bill_id');
		$model->leftJoin('accessories as a', 'a.id', '=', 'bi.paddy_id');
        $model->where('tbl_billing.bill_type_id',$bill_type_id);
        $model->where('tbl_billing.delete_flag',0)->where('tbl_billing.status',1);
		

        // Filters Parameters
        // parse_str($_POST['formData'], $filterArray);
       
        // Get data with pagination
        $model->orderBy('tbl_billing.created_at', 'desc');
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();
        $result = [];

        if(!empty($data)){
            foreach ($data as $index=>$datas) {
                if(isset($datas->comments)){
                    $comments = $datas->comments;
                }
                if(isset($datas->accessoriesName)){
                    $accessoriesName = $datas->accessoriesName;
                }
                if(isset($datas->accessoriesQty)){
                    $accessoriesQty = $datas->accessoriesQty;
                }
                    // $result[$index]['first_name'] = $datas->first_name;
                    $result[$index]['snumber'] = $start+1;
                    $result[$index]['accessories_name'] = $accessoriesName;
                    $result[$index]['aqty'] = $accessoriesQty;
          
                $result[$index]['comments'] = $comments;
                $result[$index]['inv_date'] = date('d-m-Y',strtotime($datas->inv_date));

                // action buttons
                $action = '<a href="'.url('/stock/update-accessory-manual/'.$datas->bill_id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$datas->bill_id.'"   data-url="'.url('/stock/delete-accessory-manual/'.$datas->bill_id).'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
                $result[$index]['action'] = $action;
                $start++;
            }
        }
        $sql    = $model->toSql();

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
        $actionUrl    = '/stock/create-accessory-manual';
        $redirectUrl  = '/stock/accessory-manual';
        $actionName   = 'Create';
        $bill_type_id = '7'; //accessories manual stock entry

        $loginId            = Auth::guard('admin')->user()->id;        
        $warehouseList      = Warehouses::where('status', 1)->get();
		$accessoriesList    = Accessories::where('status', 1)->get();
        
        $model 			= new TblBill;
        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'accessory_id'  => 'required',
                'qty'           => 'required',
                'comments'      => 'required|string',
            ]);
            if($validator->passes()) {				
					$model->bill_type_id          		= $bill_type_id;
					$model->user_id         		    = $loginId;	
					$model->comments        			= $request->comments;
                    $model->inv_date        			= $request->billdate;
					$model->status            		    = 1;

					if($model->save()) {

                        $UsedItem               = new BillsItem;
                        $UsedItem->bill_id      = $model->bill_id;
                        $UsedItem->paddy_id	    =	$request->accessory_id;
                        $UsedItem->qty		    =	$request->qty;
						$UsedItem->save();
                        
                        if($model && $UsedItem) {
                            return response()->json(['success'=>'Accessory manual stock is created successfully.']);
                        } else {
                            return response()->json(['singleerror'=>'Failed to add Accessory manual stock. try again after sometime.']);
                        }						
					}
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('stock.accessory.Form',['model'=>$model,'accessoryList'=>$accessoriesList,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName]);
    }
    // Update
    public function Update($id=null,Request $request) {
        $actionUrl    = '/stock/update-accessory-manual/'.$id;
        $redirectUrl  = '/stock/accessory-manual';
        $actionName   = 'Update';
        $bill_type_id = 7; //accessories manual stock entry        
        
        $loginId            = Auth::guard('admin')->user()->id;
        $warehouseList      = Warehouses::where('status', 1)->get();
		$accessoriesList    = Accessories::where('status', 1)->get();
      
        $model  = TblBill::select('a.id as accessoriesId','bi.qty as accessoriesQty','tbl_billing.bill_id', 'tbl_billing.inv_date',
        'tbl_billing.comments');
        $model->leftjoin('bill_items as bi', 'bi.bill_id', '=', 'tbl_billing.bill_id');
        $model->leftJoin('accessories as a', 'a.id', '=', 'bi.paddy_id');
        $model->where('tbl_billing.bill_type_id', $bill_type_id);
        $model->where('tbl_billing.bill_id',$id);
        $data = $model->get();
        $sql    = $model->toSql();

        if(isset($_POST['_token'])) {
            $validator = Validator::make($request->all(), [
                'accessory_id'  => 'required',
                'qty'           => 'required',
                'comments'      => 'required|string',
            ]);

            if($validator->passes()) {
                    $model  = TblBill::where('bill_id',$id)->first();
                    $model->bill_type_id          		= $bill_type_id;
                    $model->user_id         		    = $loginId;	
                    $model->comments        			= $request->comments;
                    $model->inv_date        			= $request->billdate;
                    $model->status            		    = 1;

                    if($model->save()) {
                        BillsItem::where('bill_id', $id)->delete();

                        $UsedItem               =   new BillsItem;
                        $UsedItem->bill_id      =   $model->bill_id;
                        $UsedItem->paddy_id	    =	$request->accessory_id;
                        $UsedItem->qty		    =	$request->qty;
                        $UsedItem->save();
                        
                        if($model && $UsedItem) {
                            return response()->json(['success'=>'Accessory manual stock details is updated successfully.']);
                        } else {
                            return response()->json(['singleerror'=>'Failed to update Accessory manual stock. try again after sometime.']);
                        }
                    }
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }       
        
        return view('stock.accessory.Form',['model'=>$data[0],'accessoryList'=>$accessoriesList,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'sql'=>$sql]);
    }
    // Delete
    public function Delete(Request $request){

        $id 		= isset($request->id) ? $request->id : 0;
        $model        = TblBill::where('status', 1)->where('bill_id',$id)->delete();
        if(!empty($model)) {
            $model2        = BillsItem::where('bill_id',$id)->delete(); //Delete Bill Items table datas
            return response()->json(['success'=>'Other expense details is deleted successfully.']);
        } else {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}