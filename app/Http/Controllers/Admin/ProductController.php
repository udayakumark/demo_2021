<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\ProductCategories;
use App\Models\Products;
use App\Models\ProductPrices;
use App\helpers;
use Validator;

class ProductController extends Controller
{
    public function Index()
    {
    	$categoryList = ProductCategories::where('status', 1)->orderBy('category_name')->get();
        return view('admin.Products.Index',['categoryList'=>$categoryList]);
    } 

    // Ajax data load into table
    public function List(Request $request)
    {
    	$start 	= $request->start;
    	$length = $request->length;
    	$model  = Products::where('status','!=',0);
    	$model->where('status','!=',0);

    	// Filters Parameters
    	parse_str($_POST['formData'], $filterArray);
    	$product_name = "";
    	$product_code = "";
    	$product_category = "";
    	if(isset($filterArray['product_name'])){
    		$product_name = trim($filterArray['product_name']);
    	}
    	if(isset($filterArray['product_code'])){
    		$product_code = trim($filterArray['product_code']);
    	}
    	if(isset($filterArray['product_category'])){
    		$product_category = trim($filterArray['product_category']);
    	}

    	// Filter Conditions
    	if($product_name!=""){
    		$model->where('product_name','like','%'.$product_name.'%');
    	}	
    	if($product_code!=""){
    		$model->where('product_code','like','%'.$product_code.'%');
    	}	
    	if($product_category!=""){
    		$model->where('category_id',$product_category);
    	}	

    	// Get data with pagination
    	$model->orderBy('date_time', 'desc');
    	$totalRecords 	= count($model->get());
    	$data    		= $model->offset($start)->limit($length)->get();

    	$result = [];
    	if(!empty($data)){
			foreach ($data as $index=>$product) {
				$result[$index]['snumber'] = $start+1;
    			$result[$index]['category_name'] = $product->category->category_name;
    			$result[$index]['product_name'] = $product->product_name;
    			$result[$index]['product_code'] = $product->product_code;

    			$date_time 		= "";
    			$product_image 	= "";
    			if(isset($product->date_time) && $product->date_time!="")
    			{
    				$date_time = date('d-m-Y h:i a',strtotime($product->date_time));
    			}
    			if(isset($product->product_image) && $product->product_image!="")
    			{
    				$imagePath = public_path($product->product_image);
    				$imageLink = url('public/'.$product->product_image);
    				if(file_exists($imagePath))
    				{
    					$product_image = '<img src="'.$imageLink.'" class="grid-image">';
    				}
    			}

    			// action buttons
    			$action = '<a href="'.url('/admin/update-product/'.$product->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
    			$action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$product->id.'"   data-url="'.url('/admin/delete-product').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';

    			$result[$index]['product_image'] = $product_image;
				$result[$index]['date_time'] = $date_time;
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
    	$actionUrl    = '/admin/create-product';
    	$redirectUrl  = '/admin/products';
    	$actionName   = 'Create';
    	$categoryList = ProductCategories::where('status', 1)->orderBy('category_name')->get();

        $model = new Products;
        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'product_category' => 'required|numeric',
            'product_name' => 'required|string',
            'product_code' => 'required|unique:products|string',
            'product_description'=>'required|string',
            'product_image'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $validator->after(function ($validator) {
            if(!isset($_POST['quantity']))
            {
            	$validator->errors()->add('product_prices', 'Product price configuration is missing.');
            }
            else
            {
            	if(in_array("",$_POST['quantity']) || in_array(0,$_POST['quantity'])){
            		$validator->errors()->add('product_prices', 'Product Quantity missing. Please fill it');
            	}
            	if(in_array("",$_POST['originalprice']) || in_array(0,$_POST['originalprice'])){
            		$validator->errors()->add('product_prices', 'Product Original price missing. Please fill it');
            	}
            	if(in_array("",$_POST['sellingprice']) || in_array(0,$_POST['sellingprice'])){
            		$validator->errors()->add('product_prices', 'Product Selling price missing. Please fill it');
            	}
            }
			});

            if($validator->passes()) 
            {
            	$ProductImage        = "Product".time().'.'.$request->product_image->extension(); 
            	$ProductImagePath    = 'uploads/productImages/'.$ProductImage; 
        		$request->product_image->move(public_path('uploads/productImages'), $ProductImage);
                $model->category_id        			= $request->product_category;
                $model->product_name       			= $request->product_name;
                $model->product_code       			= $request->product_code;
                $model->product_description       	= $request->product_description;
                $model->product_image       	    = $ProductImagePath;
                $model->date_time           		= date('Y-m-d H:i:s');
                if($model->save())
                {
                	$count = count($_POST['quantity']);
                	for($i=0;$i<$count;$i++)
                	{
                		$quantity = 0;
                		$sell_price = 0;
                		$original_price = 0;
                		$status = 2;
                		if(isset($_POST['quantity'][$i])){
                			$quantity = $_POST['quantity'][$i];
                		}
                		if(isset($_POST['sellingprice'][$i])){
                			$sell_price = $_POST['sellingprice'][$i];
                		}
                		if(isset($_POST['originalprice'][$i])){
                			$original_price = $_POST['originalprice'][$i];
                		}
                		if(isset($_POST['status'.$i])){
                			$status = 1;
                		}

                		$discount_price = $original_price-$sell_price;
                		$discount_percentage = round($discount_price/$original_price*100);

                		$price_model = new ProductPrices;
                		$price_model->category_id = $request->product_category;
                		$price_model->product_id = $model->id;
                		$price_model->price_type = 1;
                		$price_model->quantity = $quantity;
                		$price_model->original_price = $original_price;
                		$price_model->selling_price = $sell_price;
                		$price_model->discount_price = $discount_price;
                		$price_model->discount_percentage = $discount_percentage;
                		$price_model->date_time = date('Y-m-d H:i:s');
                		$price_model->status = $status;
                		$price_model->save();
                	}
                }
                return response()->json(['success'=>'Product Details Added Successfully.']);
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Products.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'categoryList'=>$categoryList]);
    } 



    // Update
    public function Update($id=null,Request $request)
    {
    	$actionUrl    = '/admin/update-product/'.$id;
    	$redirectUrl  = '/admin/products';
    	$actionName   = 'Update';
    	$categoryList = ProductCategories::where('status', 1)->orderBy('category_name')->get();
    	$priceList    = ProductPrices::where('status','!=', 0)->where('product_id',$id)->get();

        $model      = Products::where('status', 1)->where('id',$id)->first();
        if(empty($model)){
			return response()->view('admin.ExceptionHandler.404');
        }

        if(isset($_POST['_token']))
        {
            $validator = Validator::make($request->all(), [
            'product_category' => 'required|numeric',
            'product_name' => 'required|string',
            'product_code' => 'required|string|unique:products,product_code,'.$id,
            'product_description'=>'required|string',
            'product_image'=>'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $validator->after(function ($validator) {
            if(!isset($_POST['quantity']))
            {
            	$validator->errors()->add('product_prices', 'Product price configuration is missing.');
            }
            else
            {
            	if(in_array("",$_POST['quantity']) || in_array(0,$_POST['quantity'])){
            		$validator->errors()->add('product_prices', 'Product Quantity missing. Please fill it');
            	}
            	if(in_array("",$_POST['originalprice']) || in_array(0,$_POST['originalprice'])){
            		$validator->errors()->add('product_prices', 'Product Original price missing. Please fill it');
            	}
            	if(in_array("",$_POST['sellingprice']) || in_array(0,$_POST['sellingprice'])){
            		$validator->errors()->add('product_prices', 'Product Selling price missing. Please fill it');
            	}
            }
        	});

            if($validator->passes()) 
            {
            	if($request->product_image && $request->product_image!="")
            	{
            		$ProductImage    = "Product".time().'.'.$request->product_image->extension(); 
            		$ProductImagePath    = 'uploads/productImages/'.$ProductImage; 
        			$request->product_image->move(public_path('uploads/productImages'), $ProductImage);
        			$model->product_image       	    = $ProductImagePath;
            	}

                $model->category_id        			= $request->product_category;
                $model->product_name       			= $request->product_name;
                $model->product_code       			= $request->product_code;
                $model->product_description       	= $request->product_description;
                if($model->save())
                {
                	ProductPrices::where('product_id', $model->id)->where('status','!=',0)->update(['status' => 0]);

                	$count = count($_POST['quantity']);
                	for($i=0;$i<$count;$i++)
                	{
                		$quantity = 0;
                		$sell_price = 0;
                		$original_price = 0;
                		$status = 2;
                		$price_id = "";
                		if(isset($_POST['quantity'][$i])){
                			$quantity = $_POST['quantity'][$i];
                		}
                		if(isset($_POST['sellingprice'][$i])){
                			$sell_price = $_POST['sellingprice'][$i];
                		}
                		if(isset($_POST['originalprice'][$i])){
                			$original_price = $_POST['originalprice'][$i];
                		}
                		if(isset($_POST['price_id'][$i])){
                			$price_id = $_POST['price_id'][$i];
                		}
                		if(isset($_POST['status'.$i])){
                			$status = 1;
                		}

                		$discount_price = $original_price-$sell_price;
                		$discount_percentage = round($discount_price/$original_price*100);

                		if($price_id=="")
                		{
							$price_model = new ProductPrices;
                			$price_model->category_id = $request->product_category;
                			$price_model->product_id = $model->id;
                			$price_model->price_type = 1;
                			$price_model->quantity = $quantity;
                			$price_model->original_price = $original_price;
                			$price_model->selling_price = $sell_price;
                			$price_model->discount_price = $discount_price;
                			$price_model->discount_percentage = $discount_percentage;
                			$price_model->date_time = date('Y-m-d H:i:s');
                			$price_model->status = $status;
                			$price_model->save();
                		}
                		else
                		{
                			$price_model = ProductPrices::where('id',$price_id)->first();
                			if(!empty($price_model))
                			{
                				$price_model->quantity = $quantity;
                				$price_model->original_price = $original_price;
                				$price_model->selling_price = $sell_price;
                				$price_model->discount_price = $discount_price;
                				$price_model->discount_percentage = $discount_percentage;
                				$price_model->status = $status;
                				$price_model->save();
                			}
                		}

                	}
                }
                return response()->json(['success'=>'Product Details Updated Successfully.']);
            }
            else
            {
                return response()->json(['error'=>$validator->errors()->all()]);
            }
        }
        return view('admin.Products.Form',['model'=>$model,'action'=>$actionUrl,'redirectUrl'=>$redirectUrl,'name'=>$actionName,'categoryList'=>$categoryList,'priceList'=>$priceList]);
    } 



    // Delete Product
    public function Delete(Request $request)
    {
    	$id 		= isset($request->productId) ? $request->productId : 0;
    	$model      = Products::where('status', 1)->where('id',$id)->first();
    	if(!empty($model))
    	{
    		$model->status       = 0;
    		if($model->save())
    		{
				ProductPrices::where('status','!=',0)->where('product_id',$request->productId)->update(['status' => 0]);
    		}
    		return response()->json(['success'=>'Product Details Deleted Successfully']);
    	}
    	else
    	{
    		return response()->json(['error'=>'Invalid Request. Try Again.']);
    	}
    } 

}
