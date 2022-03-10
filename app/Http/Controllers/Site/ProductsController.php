<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductPrices;
use App\Models\CartProducts;
use App\Models\Pincodes;
use App\Models\Orders;
use App\Models\OrderedProducts;
use App\Models\SupplierPincodes;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Validator;
use Razorpay\Api\Api;

class ProductsController extends Controller
{
	public function Shop()
    {
        return view('site.shop');
    } 

    public function ProductsList(Request $request)
    {
    	$limit  		= 8;
    	$pageNumber 	= $request->pageNumber ? $request->pageNumber-1 : 0;
        $sortBy         = $request->sortBy ? trim($request->sortBy) : 1;
        $productName    = $request->productName ? trim($request->productName) : "";
        $productCategory= $request->productCategory ? trim($request->productCategory) : "";
    	$start 			= $pageNumber*$limit;
    	$length 		= $start+$limit;

    	$model  = Products::where('status','!=',0);

        if($productName!=""){
            $model->where('product_name','like','%'.$productName.'%');
        }
        if($productCategory!=""){
            $model->where('category_id',$productCategory);
        }

        if($sortBy==1){
            $model->orderBy('date_time', 'desc');
        }else if($sortBy==2){
            $model->orderBy('product_name', 'asc');
        }else if($sortBy==3){
            $model->orderBy('product_name', 'desc');
        }

    	// Get data with pagination
    	$totalRecords 	= count($model->get());
    	$data    		= $model->offset($start)->limit($length)->get();
    	$isNextpage     = $totalRecords>$length ? true : false;
    	$grid_result = '<p style="font-weight:500;font-size:18px;margin-top:20px;margin-left:20px;">No Products</p>';
        $list_result = '<p style="font-weight:500;font-size:18px;margin-top:20px;margin-left:20px;">No Products</p>';

    	if(count($data)>0)
        {
            $grid_result = "";
            $list_result = "";
			foreach ($data as $index=>$product) {
				$product_prices = ProductPrices::where(['status'=>1,'product_id'=>$product->id])->orderBy('id','asc')->limit(1)->get();
				if(!empty($product_prices))
				{
                    $price_id = $product_prices[0]->id;
                    $cart_products  = [];
                    if(isset(Auth::guard('web')->user()->id) && Auth::guard('web')->user()->id!=""){
                        $user_id        = Auth::guard('web')->user()->id;
                        $cart_products  = CartProducts::where(['status'=>1,'user_id'=>$user_id,'price_id'=>$price_id])->first();
                    }

					$grid_result .= View::make('site.render-pages.product-grid',['product'=>$product,'product_prices'=>$product_prices,'cart_products'=>$cart_products]);
                    $list_result .= View::make('site.render-pages.product-list',['product'=>$product,'product_prices'=>$product_prices,'cart_products'=>$cart_products]);
				}
			}
    	}

        $end = $length<$totalRecords ? $length : $totalRecords;
        $pagination = 'Showing '.$start.' To '.$end.' Of '.$totalRecords;
    	$response = array(  
    		"gridContent" => $grid_result,
            "listContent" => $list_result,
    		"isNextpage"=>$isNextpage,
            "pagination"=>$pagination
  		);
    	return response()->json($response);
    } 

    public function ProductDetail($id)
    {
        $product_details = Products::where(['status'=>1,'id'=>$id])->get();
        if(!empty($product_details))
        {
            $product_prices = ProductPrices::where(['status'=>1,'product_id'=>$id])->get();
            $price_id = $product_prices[0]->id;
            $cart_products  = [];
            if(isset(Auth::guard('web')->user()->id) && Auth::guard('web')->user()->id!=""){
                $user_id        = Auth::guard('web')->user()->id;
                $cart_products  = CartProducts::where(['status'=>1,'user_id'=>$user_id,'price_id'=>$price_id])->first();
            }
            return view('site.product-detail',['product_details'=>$product_details,'product_prices'=>$product_prices,'cart_products'=>$cart_products]);
        }
        else
        {
            return redirect('/shop');
        }
    }

    public function AddtoCart(Request $request)
    {
        $pack_id  = isset($request->pack) ? $request->pack : '';
        $quantity = isset($request->quantity) ? $request->quantity : '';
        if($quantity=="" || $pack_id=="")
        {
            return response()->json(['error'=>"Invalid request. try again."]);
        }
        else
        {
            $product_details = ProductPrices::where(['status'=>1,'id'=>$pack_id])->first();
            if(empty($product_details))
            {
                return response()->json(['error'=>"Product is not available. try again."]);
            }
            else
            {
                $product_id = 0;
                $user_id = Auth::guard('web')->user()->id;
                if(isset($product_details->product->id))
                {
                    $product_id = $product_details->product->id;
                }

                $checkCart = CartProducts::where(['status'=>1,'price_id'=>$pack_id,'user_id'=>$user_id])->first();
                if(empty($checkCart))
                {
                    $model = new CartProducts();
                    $model->product_id = $product_id;
                    $model->price_id = $pack_id;
                    $model->user_id = $user_id;
                    $model->quantity = $quantity;
                    $model->date_time = date('Y-m-d H:i:s');
                    $model->save();
                }
                else
                {
                    $checkCart->quantity = $quantity;
                    $checkCart->date_time = date('Y-m-d H:i:s');
                    $checkCart->save();
                }
                return response()->json(['success'=>"Product successfully added into cart."]);
            }
        }
    }



    public function userCartList(Request $request)
    {
        $cartCount = 0;
        $cartList = [];
        $cartPrice = 0;
        $cartUrl = url('login');

        if(Auth::guard('web')->check())
        {
            $cartUrl = url('cart');
            $user_id = Auth::guard('web')->user()->id;
            $cart_products = CartProducts::where(['status'=>1,'user_id'=>$user_id])->get();
            if(!empty($cart_products))
            {
                foreach ($cart_products as $cart) 
                {
                    $product_name = "";
                    $product_price = "";
                    $product_image = "";
                    $product_size = "";
                    $product_url = url('product-detail/'.$cart->product_id);
                    if(isset($cart->product->product_name))
                    {
                        $product_name = $cart->product->product_name;
                    }
                    if(isset($cart->product->product_image))
                    {
                        $product_image = $cart->product->product_image;
                    }
                    if(isset($cart->price->selling_price))
                    {
                        $product_price = $cart->price->selling_price;
                    }
                    if(isset($cart->price->quantity))
                    {
                        $product_size = $cart->price->quantity;
                    }

                    $data = array(
                        "quantity"=>$cart->quantity,
                        "product_name"=>$product_name,
                        "product_price"=>$product_price,
                        "product_image"=>$product_image,
                        "product_size"=>$product_size,
                        "product_url"=>$product_url,
                        "cart_id"=>$cart->id
                    );
                    $cartCount +=1;
                    $cartPrice += ($product_price*$cart->quantity);
                    array_push($cartList, $data);
                }
            }
        }
        return View::make('site.render-pages.header-cart',['cartList'=>$cartList,'cartPrice'=>$cartPrice,'cartCount'=>$cartCount,'cartUrl'=>$cartUrl]);
    } 

    public function userCartMobileList(Request $request)
    {
        $cartCount = 0;
        $cartList = [];
        $cartPrice = 0;
        $cartUrl = url('login');

        if(Auth::guard('web')->check())
        {
            $cartUrl = url('cart');
            $user_id = Auth::guard('web')->user()->id;
            $cart_products = CartProducts::where(['status'=>1,'user_id'=>$user_id])->get();
            if(!empty($cart_products))
            {
                foreach ($cart_products as $cart) 
                {
                    $product_name = "";
                    $product_price = "";
                    $product_image = "";
                    $product_size = "";
                    $product_url = url('product-detail/'.$cart->product_id);
                    if(isset($cart->product->product_name))
                    {
                        $product_name = $cart->product->product_name;
                    }
                    if(isset($cart->product->product_image))
                    {
                        $product_image = $cart->product->product_image;
                    }
                    if(isset($cart->price->selling_price))
                    {
                        $product_price = $cart->price->selling_price;
                    }
                    if(isset($cart->price->quantity))
                    {
                        $product_size = $cart->price->quantity;
                    }

                    $data = array(
                        "quantity"=>$cart->quantity,
                        "product_name"=>$product_name,
                        "product_price"=>$product_price,
                        "product_image"=>$product_image,
                        "product_size"=>$product_size,
                        "product_url"=>$product_url,
                        "cart_id"=>$cart->id
                    );
                    $cartCount +=1;
                    $cartPrice += ($product_price*$cart->quantity);
                    array_push($cartList, $data);
                }
            }
        }
        return View::make('site.render-pages.header-cartmobile',['cartList'=>$cartList,'cartPrice'=>$cartPrice,'cartCount'=>$cartCount,'cartUrl'=>$cartUrl]);
    } 



    public function RemoveFormCart(Request $request)
    {
        $cart_id  = isset($request->cart_id) ? $request->cart_id : '';
        if($cart_id=="")
        {
            return response()->json(['error'=>"Invalid request. try again."]);
        }
        else
        {
            $user_id = Auth::guard('web')->user()->id;
            $cart_details = CartProducts::where(['status'=>1,'id'=>$cart_id,'user_id'=>$user_id])->first();
            if(empty($cart_details))
            {
                return response()->json(['error'=>"Product is not available. try again."]);
            }
            else
            {
                CartProducts::where('id', $cart_details->id)->delete();
                return response()->json(['success'=>"Product successfully removed from cart."]);
            }
        }
    }



    public function CartList()
    {
        $cartList = [];
        $cartPrice = 0;
        $user_id = Auth::guard('web')->user()->id;
        $cart_details = CartProducts::where(['status'=>1,'user_id'=>$user_id])->get();
        if(!empty($cart_details))
        {
            foreach ($cart_details as $cart) 
            {
                $product_name = "";
                $product_price = "";
                $product_image = "";
                $product_size = "";
                $product_url = url('product-detail/'.$cart->product_id);
                if(isset($cart->product->product_name))
                {
                    $product_name = $cart->product->product_name;
                }
                if(isset($cart->product->product_image))
                {
                    $product_image = $cart->product->product_image;
                }
                if(isset($cart->price->selling_price))
                {
                    $product_price = $cart->price->selling_price;
                }
                if(isset($cart->price->quantity))
                {
                    $product_size = $cart->price->quantity;
                }

                $data = array(
                    "quantity"=>$cart->quantity,
                    "product_name"=>$product_name,
                    "product_price"=>$product_price,
                    "product_image"=>$product_image,
                    "product_size"=>$product_size,
                    "product_url"=>$product_url,
                    "cart_id"=>$cart->id
                );
                $cartPrice += ($product_price*$cart->quantity);
                array_push($cartList, $data);
            }
        }

        return view('site.cart',['cartList'=>$cartList,'totalPrice'=>$cartPrice]);
    } 



    public function UpdateCart(Request $request)
    {
        if(isset($_POST['_token']))
        {
            $user_id = Auth::guard('web')->user()->id;
            $count = isset($request->cart_id) ? count($request->cart_id) : 0;
            for($i=0;$i<$count;$i++)
            {
                $cart_details = CartProducts::where(['status'=>1,'id'=>$request->cart_id[$i],'user_id'=>$user_id])->first();
                if(!empty($cart_details))
                {
                    $cart_details->quantity = $request->quantity[$i];
                    $cart_details->save();
                }
            }
            return response()->json(['success'=>"Cart details updated successfully."]);
        }
        else
        {
            return response()->json(['error'=>"Something went wrong. try again."]);
        }
    }



    public function Checkout()
    {
        $cartList = [];
        $cartPrice = 0;
        $user_id = Auth::guard('web')->user()->id;
        $cart_details = CartProducts::where(['status'=>1,'user_id'=>$user_id])->get();
        if(count($cart_details)>0)
        {
            foreach ($cart_details as $cart) 
            {
                $product_name = "";
                $product_price = "";
                $product_image = "";
                $product_size = "";
                $product_url = url('product-detail/'.$cart->product_id);
                if(isset($cart->product->product_name))
                {
                    $product_name = $cart->product->product_name;
                }
                if(isset($cart->product->product_image))
                {
                    $product_image = $cart->product->product_image;
                }
                if(isset($cart->price->selling_price))
                {
                    $product_price = $cart->price->selling_price;
                }
                if(isset($cart->price->quantity))
                {
                    $product_size = $cart->price->quantity;
                }

                $data = array(
                    "quantity"=>$cart->quantity,
                    "product_name"=>$product_name,
                    "product_price"=>$product_price,
                    "product_image"=>$product_image,
                    "product_size"=>$product_size,
                    "product_url"=>$product_url,
                    "cart_id"=>$cart->id
                );
                $cartPrice += ($product_price*$cart->quantity);
                array_push($cartList, $data);
            }
            return view('site.checkout',['cartList'=>$cartList,'totalPrice'=>$cartPrice]);
        }
        else
        {
            return redirect('/shop');
        }
    } 



    public function PlaceOrder(Request $request)
    {
        if(isset($_POST['_token']))
        {
            if($request->same_address)
            {
                $validator = Validator::make($request->all(), [
                'billing_firstname' => 'required|string',
                'billing_lastname' => 'required|string',
                'billing_email' => 'required|email',
                'billing_mobile' => 'required|numeric|digits:10',
                'billing_address1'=>'required|string|max:180',
                'billing_country' => 'required|numeric',
                'billing_state' => 'required|numeric',
                'billing_city' => 'required|numeric',
                'billing_pincode' => 'required|numeric',
                'shipping_firstname' => 'required|string',
                'shipping_lastname' => 'required|string',
                'shipping_email' => 'required|email',
                'shipping_mobile' => 'required|numeric|digits:10',
                'shipping_address'=>'required|string',
                'shipping_city' => 'required|numeric',
                'shipping_pincode' => 'required|numeric',
                ]);
            }
            else
            {
                $validator = Validator::make($request->all(), [
                'billing_firstname' => 'required|string',
                'billing_lastname' => 'required|string',
                'billing_email' => 'required|email',
                'billing_mobile' => 'required|numeric|digits:10',
                'billing_address'=>'required|string',
                'billing_city' => 'required|numeric',
                'billing_pincode' => 'required|numeric',
                ]);
            }

            if($validator->passes())
            {
                $user_id      = Auth::guard('web')->user()->id;

                $billing_country  = 0;
                $billing_state    = 0;
                $billing_city     = 0;
                $billing_pincode  = 0;
                $shipping_country = 0;
                $shipping_state   = 0;
                $shipping_city    = 0;
                $shipping_pincode = 0;
                $supplier_id      = null;

                if(isset($request->billing_pincode))
                {
                    $billing_detail = Pincodes::where('id',$request->billing_pincode)->get();
                    if(!empty($billing_detail))
                    {
                        $billing_country  = $billing_detail[0]['country_id'];
                        $billing_state    = $billing_detail[0]['state_id'];
                        $billing_city     = $billing_detail[0]['city_id'];
                        $billing_pincode  = $billing_detail[0]['id']    ;
                    }
                }
                if(isset($request->shipping_pincode))
                {
                    $shipping_detail = Pincodes::where('id',$request->shipping_pincode)->get();
                    if(!empty($shipping_detail))
                    {
                        $shipping_country  = $shipping_detail[0]['country_id'];
                        $shipping_state    = $shipping_detail[0]['state_id'];
                        $shipping_city     = $shipping_detail[0]['city_id'];
                        $shipping_pincode  = $shipping_detail[0]['id'];
                    }
                }

                $supplier_pincode = $request->same_address ? $shipping_pincode : $billing_pincode;
                if($supplier_pincode!=""){
                    $checkSupplier = SupplierPincodes::where('pincode_id',$supplier_pincode)->where('status',1)->first();

                    if(isset($checkSupplier->user_id)){
                        $supplier_id = $checkSupplier->user_id;
                    }
                }

                $model                      = new Orders();
                $model->user_id             = $user_id;
                $model->supplier_id         = $supplier_id;
                $model->payment_type        = $request->payment;
                $model->order_id            = null;
                $model->billing_firstname   = $request->billing_firstname;
                $model->billing_lastname    = $request->billing_lastname;
                $model->billing_email       = $request->billing_email;
                $model->billing_mobile      = $request->billing_mobile;
                $model->billing_address     = $request->billing_address;
                $model->billing_country     = $billing_country;
                $model->billing_state       = $billing_state;
                $model->billing_city        = $billing_city;
                $model->billing_pincode     = $billing_pincode;
                if($request->same_address)
                {
                    $model->shipping_firstname  = $request->shipping_firstname;
                    $model->shipping_lastname   = $request->shipping_lastname;
                    $model->shipping_email      = $request->shipping_email;
                    $model->shipping_mobile     = $request->shipping_mobile;
                    $model->shipping_address    = $request->shipping_address;
                    $model->shipping_country    = $shipping_country;
                    $model->shipping_state      = $shipping_state;
                    $model->shipping_city       = $shipping_city;
                    $model->shipping_pincode    = $shipping_pincode;
                }
                else
                {
                    $model->shipping_firstname  = $request->billing_firstname;
                    $model->shipping_lastname   = $request->billing_lastname;
                    $model->shipping_email      = $request->billing_email;
                    $model->shipping_mobile     = $request->billing_mobile;
                    $model->shipping_address    = $request->billing_address;
                    $model->shipping_country    = $billing_country;
                    $model->shipping_state      = $billing_state;
                    $model->shipping_city       = $billing_city;
                    $model->shipping_pincode    = $billing_pincode;
                }

                $model->date_time           = date('Y-m-d H:i:s');
                if($model->save())
                {
                    $order_id      = "SAKTHI0".sprintf("%04d", $model->id);
                    $cart_products = CartProducts::where(['status'=>1,'user_id'=>$user_id])->get();
                    if(!empty($cart_products))
                    {
                        $total_amount     = 0;
                        foreach ($cart_products as $cart) 
                        {
                            $total_price = ($cart->price->selling_price*$cart->quantity);
                            $products                       = new OrderedProducts;
                            $products->productprice_id      = $cart->price->id;
                            $products->order_id             = $model->id;
                            $products->product_name         = $cart->product->product_name;
                            $products->quantity             = $cart->quantity;
                            $products->price                = $cart->price->selling_price;
                            $products->total_price          = $total_price;
                            $products->date_time            = date('Y-m-d H:i:s');
                            $products->save();
                            $total_amount += $total_price;
                        }
                    }

                    if($request->payment==1){ //Razorpay
                        $model->total_amount    = $total_amount;
                        $model->subtotal_amount = $total_amount;
                        $model->order_id        = $order_id;
                        $model->save();
                        return response()->json(['success'=>'Success','order_id'=>$model->id,'amount'=>1]);
                    }else{ //Cashon Delivery
                        $model->total_amount    = $total_amount;
                        $model->subtotal_amount = $total_amount;
                        $model->order_id        = $order_id;
                        $model->status          = 1;
                        $model->save();
                        CartProducts::where('user_id', $user_id)->delete();
                        return response()->json(['success'=>'Your order is placed successfully.']);
                    }
                }
                else
                {
                    return response()->json(['error'=>'Server problem. try again after sometime.']);
                }
            }
            else
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
        }
        else
        {
            return response()->json(['error'=>"Invalid request. try again after sometime."]);
        }
    } 

    public function razorpayResponse(Request $request)
    {
        $input      = $request->all();
        $api        = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $payment    = $api->payment->fetch($input['razorpay_payment_id']);
        $order_id   = $input['order_id'];
        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $user_id      = Auth::guard('web')->user()->id;
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount'])); 
                Orders::where(['id'=>$order_id])->update(['payment_status'=>1,'status'=>1,'payment_referenceno'=>$input['razorpay_payment_id'],'payment_response'=>json_encode($response)]);
                CartProducts::where('user_id', $user_id)->delete();
                return response()->json(['success'=>"Your Order is placed Successfully."]);
            } catch (Exception $e) {
                Orders::where(['id'=>$order_id])->update(['payment_status'=>2,'payment_referenceno'=>null,'payment_response'=>json_encode($e)]);
                return response()->json(['error'=>"Server Error. try again after sometime."]);
            }
        }else{
            Orders::where(['id'=>$order_id])->update(['payment_status'=>2,'payment_referenceno'=>null,'payment_response'=>"Server Error"]);
            return response()->json(['error'=>"Something went wrong. try again."]);
        }
    }

    public function GetPincode(Request $request)
    {
        $options = '<option value="">Select Pincode</option>';
        $city_id = isset($request->city_id) ? $request->city_id : 0;
        $model = Pincodes::where(['city_id'=>$city_id,'status'=>1]);
        $model->orderBy('pincode');
        $pincodes = $model->get();
        if(!empty($pincodes))
        {
            foreach ($pincodes as $pincode) {
                $options .= '<option value="'.$pincode->id.'">'.$pincode->pincode.'</option>';
            }
        }
        return $options;
    }

    public function PriceDetails(Request $request)
    {
        $pack_id        = $request->pack_id;
        $product_prices = ProductPrices::where(['status'=>1,'id'=>$pack_id])->first();
        $cart_products  = [];
        if(isset(Auth::guard('web')->user()->id) && Auth::guard('web')->user()->id!=""){
            $user_id        = Auth::guard('web')->user()->id;
            $cart_products  = CartProducts::where(['status'=>1,'user_id'=>$user_id,'price_id'=>$pack_id])->first();
        }
        $price_details  = [];
        if(!empty($product_prices))
        {
            $product_name = $product_prices->product->product_name.' ('.$product_prices->quantity.' KG)';
            $cart_status  = (!empty($cart_products)) ? '<i class="icon ion-bag"></i> Update cart' : '<i class="icon ion-bag"></i> Add to cart';
            $price_details = array(
                "product_name"=>$product_name,
                "product_id"=>$product_prices->product->id,
                "original_price"=>"Rs . ".$product_prices->original_price,
                "selling_price"=>"Rs . ".$product_prices->selling_price,
                "discount_price"=>$product_prices->discount_price,
                "discount_percentage"=>$product_prices->discount_percentage." %",
                "cart_status"=>$cart_status
            );
        }
        return $price_details;
    } 
}
