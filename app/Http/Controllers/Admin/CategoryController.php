<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\ProductCategories;
use App\helpers;
use Validator;

class CategoryController extends Controller
{
    public function Index()
    {
        return view('admin.Categories.Index');
    } 

    // Ajax data load into table
    public function List(Request $request)
    {
    	$start 	= $request->start;
    	$length = $request->length;
    	$model  = ProductCategories::where('status','!=',0);
    	$model->where('status','!=',0);

    	// Filters Parameters
    	parse_str($_POST['formData'], $filterArray);
    	$category_name = "";
    	if(isset($filterArray['category_name'])){
    		$category_name = trim($filterArray['category_name']);
    	}

    	// Filter Conditions
    	if($category_name!=""){
    		$model->where('category_name','like','%'.$category_name.'%');
    	}	

    	// Get data with pagination
    	$model->orderBy('date_time', 'desc');
    	$totalRecords 	= count($model->get());
    	$data    		= $model->offset($start)->limit($length)->get();

    	$result = [];
    	if(!empty($data)){
			foreach ($data as $index=>$category) {
				$result[$index]['snumber'] = $start+1;
    			$result[$index]['category_name'] = $category->category_name;
    			$result[$index]['category_code'] = $category->category_code;
    			$result[$index]['date_time'] = $category->date_time!="" ? date('d-m-Y h:i a',strtotime($category->date_time)) : "";

    			// action buttons
    			$action = '<button title="Edit" data-id="'.$category->id.'" data-url="'.url('/admin/update-category').'" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></button>';
    			$action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$category->id.'"   data-url="'.url('/admin/delete-category').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';

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
        $model = new ProductCategories;
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'category_name' => 'required|string',
            'category_code' => 'required|unique:product_categories|string',
            ]);

            if($validator->passes()) 
            {
                $model->category_name       = $request->category_name;
                $model->category_code       = $request->category_code;
                $model->date_time           = date('Y-m-d H:i:s');
                $model->save();
                return response()->json(['success'=>'Category Details Added Successfully.']);
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Categories.Form',['model'=>$model,'action'=>'/admin/create-category','name'=>'Create']);
    } 

    // Update
    public function Update($id=null,Request $request)
    {
        $model = ProductCategories::find($id);
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'category_name' => 'required|string',
            'category_code' => 'required|string|unique:product_categories,category_code,'.$id,
            ]);

            if($validator->passes()) 
            {
                $model->category_name       = $request->category_name;
                $model->category_code       = $request->category_code;
                $model->save();
                return response()->json(['success'=>'Category Details Updated Successfully.']);
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Categories.Form',['model'=>$model,'action'=>'/admin/update-category/'.$id,'name'=>'Update']);
    } 

    // Delete
    public function Delete(Request $request)
    {
    	$id 		= isset($request->categoryId) ? $request->categoryId : 0;
    	$model    	= ProductCategories::find($id);
    	if(!empty($model)){
    		$model->status       = 0;
    		$model->save();
    		return response()->json(['success'=>'Category Details Deleted Successfully']);
    	}else{
    		return response()->json(['error'=>'Invalid Request. Try Again.']);
    	}
    }


}
