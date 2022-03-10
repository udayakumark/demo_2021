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
use App\Models\InvoicenoSettings;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;

class MasterController extends Controller
{
    public function Index()
    {
        return view('admin.vendortype.Index');
    }

    // Ajax data load into table
    public function List(Request $request)
    {
        $start 	= $request->start;
        $length = $request->length;
        $model  = VendorType::select('*');
        $model->where('status','!=',0);

        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
        $name = "";
        $mobile_no = "";
        $email_id  = "";
        $address   = "";

        if(isset($filterArray['name'])){
            $name = trim($filterArray['name']);
        }

        // Filter Conditions
        if($name!=""){
            $model->where(function($q) use ($name){
                $q->where('name','like','%'.$name.'%')->orWhere('vendor_type.name','like','%'.$name.'%');
            });
        }

        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$user) {
                $full_name  	= "";
                if(isset($user->name)){
                    $name = $user->name;
                }
                $result[$index]['snumber'] = $start+1;
                $result[$index]['name'] = $name;

                // action buttons
                $action = '<a href="'.url('/admin/update-vendortype/'.$user->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$user->id.'"   data-url="'.url('/admin/delete-vendortype').'"class="btn btn-icon btn-sm btn-danger delete-button" disabled><i class="fas fa-trash"></i></button>';
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
        $actionUrl    = '/admin/create-vendortype';
        $redirectUrl  = '/admin/vendortype';
        $actionName   = 'Create';
        $type 		  = 1;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status', 1)->orderBy('id')->get();
        $userPincodeList = [];

        $model 			= new VendorType;

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);

            if($validator->passes())
            {
				$vendorTypeIsExists = VendorType::select('name')->where('name',$request->name)->exists();
				if($vendorTypeIsExists) {
					return response()->json(['singleerror'=>'Vendor type already exists. Please choose a different name.']);
				} else {
					$model->name        			=     $request->name;
					$model->status            			= 1;
					if($model->save())
					{

						return response()->json(['success'=>'New Vendor type is created successfully.']);

					}
					else
					{
						return response()->json(['singleerror'=>'Failed to add new vendortype. try again after sometime.']);
					}
				}
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.vendortype.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'userPincodeList'=>$userPincodeList,'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-vendortype/'.$id;
        $redirectUrl  = '/admin/vendortype';
        $actionName   = 'Update';
        $type 	      = 2;
        $cityList     = Cities::where('flag', 1)->orderBy('name')->get();
        $pincodeList  = Pincodes::where('status','!=',0)->get();

        $model        = VendorType::where('status', 1)->where('id',$id)->first();

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);

            if($validator->passes())
            {
				$vendorTypeIsExists = VendorType::select('name')->where('name',$request->name)->where('id','!=',$id)->exists();
				if($vendorTypeIsExists) {
					return response()->json(['singleerror'=>'Vendor type already exists. Please choose a different name.']);
				} else {
					$model->name       			    = $request->name;
					if($model->save())
					{

						return response()->json(['success'=>'Vendor type details is updated successfully.']);
					}
					else
					{
						return response()->json(['singleerror'=>'Failed to update Vendor type. try again after sometime.']);
					}
				}
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.vendortype.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'cityList'=>$cityList,'pincodeList'=>$pincodeList,'type'=>$type]);
    }

    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->dealerId) ? $request->dealerId : 0;
        $model        = VendorType::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Vendor type details is deleted successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
	
	// Update InvoiceNo
    public function UpdateInvoice($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-invoiceno/';
        $redirectUrl  = '/admin/update-invoiceno/';
        $actionName   = 'Update';
        $type 	      = 2;
        $model        = InvoicenoSettings::where('status', 1)->get();
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
				'invoice_no_accessories' => 'required|string',
                'invoice_no_bag' => 'required|string'
            ]);

            if($validator->passes())
            {
			
				$modelInvoice  = InvoicenoSettings::where('type',7)->first();
				$modelInvoice->invoice_no = $request->invoice_no_accessories;
				$modelInvoice2  = InvoicenoSettings::where('type',2)->first();
				$modelInvoice2->invoice_no = $request->invoice_no_bag;
				$modelInvoice3  = InvoicenoSettings::where('type',1)->first();
				$modelInvoice3->invoice_no = $request->invoice_no_paddy;		
				if($modelInvoice->save() && $modelInvoice2->save() && $modelInvoice3->save())
				{
					return response()->json(['success'=>'Invoice No. settings details is updated successfully.']);
				}
				else
				{
					return response()->json(['singleerror'=>'Failed to update Invoice No. settings details. try again after sometime.']);
				}
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.invoiceno_settings.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
    }

}