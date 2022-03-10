<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Orders;
use App\Models\User;
use App\Models\OrderedProducts;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class MyaccountController extends Controller
{
    public function Index()
    {
        return view('site.myaccount');
    } 

    // Ajax data load into table
    public function MyorderList(Request $request)
    {
        $start      = $request->start;
        $length     = $request->length;
        $user_id    = Auth::guard('web')->user()->id;

        $model  = Orders::where('status',1);
        $model->where('user_id',$user_id);

        // Filters Parameters
        // parse_str($_POST['formData'], $filterArray);
        // $product_name = "";
        // $product_code = "";
        // $product_category = "";
        // if(isset($filterArray['product_name'])){
        //     $product_name = trim($filterArray['product_name']);
        // }
        // if(isset($filterArray['product_code'])){
        //     $product_code = trim($filterArray['product_code']);
        // }
        // if(isset($filterArray['product_category'])){
        //     $product_category = trim($filterArray['product_category']);
        // }

        // Filter Conditions
        // if($product_name!=""){
        //     $model->where('product_name','like','%'.$product_name.'%');
        // }   
        // if($product_code!=""){
        //     $model->where('product_code','like','%'.$product_code.'%');
        // }   
        // if($product_category!=""){
        //     $model->where('category_id',$product_category);
        // }   

        // Get data with pagination
        $model->orderBy('date_time', 'desc');
        $totalRecords   = count($model->get());
        $data           = $model->offset($start)->limit($length)->get();

        $result = [];
        if(!empty($data)){
            foreach ($data as $index=>$order) {
                $result[$index]['snumber']          = $start+1;
                $result[$index]['order_id']         = $order->order_id;
                $result[$index]['total_amount']     = $order->subtotal_amount;
                $result[$index]['payment_type']     = PaymentType($order->payment_type);
                $result[$index]['payment_status']   = PaymentStatus($order->payment_status);
                $result[$index]['order_status']     = OrderStatus($order->order_status);
                $result[$index]['date_time']        = DateTime($order->date_time);

                // action buttons
                // $action = '<a href="'.url('/admin/update-product/'.$product->id).'" title="Edit" class="btn btn-icon btn-sm btn-warning update-button"><i class="fas fa-edit"></i></a>';
                // $action .= '&nbsp;&nbsp;<button title="Delete" data-id="'.$product->id.'"   data-url="'.url('/admin/delete-product').'"class="btn btn-icon btn-sm btn-danger delete-button"><i class="fas fa-trash"></i></button>';

                $result[$index]['action'] = "";
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



    // Change password
    public function changepassword(Request $request)
    {
        if($request->ajax() && $request->_token)
        {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
            ]);

            if($validator->passes())
            {
                $user_id            = Auth::guard('web')->user()->id;
                $current_password   = $request->current_password;
                $new_password       = Hash::make($request->password);
                $check_password     = User::where(['id'=>$user_id])->first();
                if(Hash::check($current_password, $check_password->password))
                {
                    User::where(['id'=>$user_id])->update(['password'=>$new_password]);
                    return response()->json(['success'=>'New password changed successfully']);
                }
                else
                {
                    return response()->json(['error'=>'Current password is invalid. try again.']);
                }
            }
            else
            {
                return response()->json(['errors'=>$validator->errors()->all()]);
            }
        }
        else
        {
            return redirect('myaccount');
        }
    } 

    public function Logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        return redirect('login');
    }

}
