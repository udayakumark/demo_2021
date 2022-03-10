<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\PettyCashModel;
use Illuminate\Support\Facades\Hash;
use App\helpers;
use Validator;


class PettyCashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pettycash.Index');
    }
    // Ajax data load into table
    public function List(Request $request)
    {
        $start     = $request->start;
        $length    = $request->length;
        $model     = PettyCashModel::select('*');
        $model->where('status','!=',0);

        // Filters Parameters
        parse_str($_POST['formData'], $filterArray);
         $name = "";
        //  $mobile_no = "";
        //  $email_id  = "";
        //  $address   = "";

         if(isset($filterArray['name'])){
             $name = trim($filterArray['name']);
         }

         // Filter Conditions
         if($name!=""){
             $model->where(function($q) use ($name){
                 $q->where('comments','like','%'.$name.'%')->orWhere('petty_cash.amount','like','%'.$name.'%');
             });
         }

        // Get data with pagination
        $totalRecords 	= count($model->get());
        $data    		= $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$pettyCash) {
                $amount  	= "";
                $comments  	= "";
                
                if(isset($pettyCash->amount)){
                    $amount = $pettyCash->amount;
                }
                if(isset($pettyCash->comments)){
                    $comments = $pettyCash->comments;
                }
                $result[$index]['snumber']  = $start+1;
                $result[$index]['amount']   =  '₹ '.number_format($amount, 2);
                $result[$index]['comments'] = $comments;

                // action buttons
                $action = '<a href="'.url('/admin/update-petty-cash/'.$pettyCash->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$pettyCash->id.'"   data-url="'.url('/admin/delete-petty-cash').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Create(Request $request)
    {
        $actionUrl    = '/admin/create-petty-cash';
        $redirectUrl  = '/admin/petty-cash';
        $actionName   = 'Create';
        $type 		  = 1;
        
        $model 			= new PettyCashModel;

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric'
            ]);


            if($validator->passes())
            {
                $model->amount      =   $request->amount;
                $model->comments    =   $request->comments;
                $model->date_time   =   date('Y-m-d', time());
                $model->status      =   1;

                if($model->save())
                {
                    return response()->json(['success'=>'New Petty Cash Created Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to add new petty cash. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.pettycash.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
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
    // Update
    public function Update($id=null,Request $request)
    {
        $actionUrl    = '/admin/update-petty-cash/'.$id;
        $redirectUrl  = '/admin/petty-cash';
        $actionName   = 'Update';
        $type 	      = 2;
        
        $model        = PettyCashModel::where('status', 1)->where('id',$id)->first();

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric'
            ]);

            if($validator->passes())
            {
                $model->amount      = $request->amount;
                $model->comments    = $request->comments;
                $model->date_time   =   date('Y-m-d', time());
             
                if($model->save())
                {
                    return response()->json(['success'=>'Petty Cash Details Updated Successfully.']);
                }
                else
                {
                    return response()->json(['singleerror'=>'Failed to update Petty Cash. try again after sometime.']);
                }
            } else {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.pettycash.Form',['model'=>$model,'action'=>$actionUrl,
        'redirectUrl'=>$redirectUrl,'name'=>$actionName,'type'=>$type]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // Delete
    public function Delete(Request $request)
    {
        $id 		= isset($request->id) ? $request->id : 0;
        $model      = PettyCashModel::where('status', 1)->where('id',$id)->first();
        if(!empty($model))
        {
            $model->status       = 0;
            $model->save();
            return response()->json(['success'=>'Petty Cash Details Deleted Successfully.']);
        }
        else
        {
            return response()->json(['error'=>'Invalid Request. Try Again.']);
        }
    }
}
