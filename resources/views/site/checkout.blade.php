@extends('layouts.site-layout')
@section('title', 'Checkout')
@section('script-src', asset('public/site-assets/pages/checkout.js'))
@section('content')


<!--====== Breadcrumb Part Start ======-->

<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <input type="hidden" name="getPincodeUrl" id="getPincodeUrl" value="{{ url('getPincode') }}">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<!--====== Breadcrumb Part Ends ======-->

<!--====== Checkout Part Start ======-->

<section class="checkout-area pt-10">
    <div class="container-fluid custom-container">
        <div class="row">
            <form id="checkout-form" action="{{ url('placeOrder') }}" method="POST">
            @csrf
            <div class="col-xl-8 col-lg-7">
                <div class="checkout-form">
                    <!-- Error Messages -->
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                        <div class="billing-address mt-30">
                            <h4 class="allup-title">Billing Address</h4>
                            <div class="row">
                                <input type="hidden" id="keyCode" name="keyCode" value="{{ env('RAZORPAY_KEY') }}">
                                <input type="hidden" id="responseUrl" name="responseUrl" value="{{ url('razorpayResponse') }}">
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>First Name*</label>
                                        <input type="text" name="billing_firstname" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Last  Name*</label>
                                        <input type="text" name="billing_lastname" placeholder="Last Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Email Address*</label>
                                        <input type="text" name="billing_email" placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Phone no*</label>
                                        <input type="text" name="billing_mobile" placeholder="Phone no">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="single-form">
                                        <label>Address</label>
                                        <input type="text" name="billing_address" placeholder="Address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Town/City*</label>
                                        <select name="billing_city" id="billing_city">
                                            <option value="">Select City</option>
                                            @foreach (App\Models\Cities::where('flag', 1)->orderBy('name')->get() as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Zip Code*</label>
                                        <select name="billing_pincode" id="billing_pincode">
                                            <option value="">Select Pincode</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="checkout-checkbox">
                            <ul>
                                <li>
                                    <div class="custom-checkbox">
                                        <input type="checkbox" name="same_address" id="address" data-shipping>
                                        <label for="address"></label>
                                        <p>Ship to Different Address </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div id="shipping-address" class="shipping-address mt-30">
                            <h4 class="allup-title">Shipping Address</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>First Name*</label>
                                        <input type="text" name="shipping_firstname" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Last  Name*</label>
                                        <input type="text" name="shipping_lastname" placeholder="Last Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Email Address*</label>
                                        <input type="text" name="shipping_email" placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Phone no*</label>
                                        <input type="text" name="shipping_mobile" placeholder="Phone no">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="single-form">
                                        <label>Address</label>
                                        <input type="text" name="shipping_address" placeholder="Address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Town/City*</label>
                                        <select name="shipping_city" id="shipping_city">
                                            <option value="">Select City</option>
                                            @foreach (App\Models\Cities::where('flag', 1)->orderBy('name')->get() as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single-form">
                                        <label>Zip Code*</label>
                                        <select name="shipping_pincode" id="shipping_pincode">
                                            <option value="">Select Pincode</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="checkout-total-wrapper mt-30">
                    <h4 class="allup-title">Cart Total</h4>
                    <div class="checkout-total mt-30">
                        <h4 class="title">Product  <span>Total</span></h4>
                        <ul>
                            @if(!empty($cartList))
                            @foreach($cartList as $cart)
                            <li>
                                <p class="total-value">{{ $cart['product_name'] }} ({{ $cart['product_size'] }} KG) X {{ $cart['quantity'] }}</p>
                                <p class="total-amount">Rs. {{ $cart['product_price']*$cart['quantity'] }}</p>
                            </li>
                            @endforeach
                            @else
                            <li>
                                <p class="total-value">No products in your cart</p>
                            </li>
                            @endif
                        </ul>
                        <div class="checkout-total-sub">
                            <p class="total-value">Sub Total</p>
                            <p class="total-amount">Rs. {{ $totalPrice }}</p>
                        </div>
                        <div class="checkout-total-sub">
                            <p class="total-value">Shipping Fee </p>
                            <p class="total-amount">Rs. 0</p>
                        </div>
                        <h4 class="title mt-15">Product  <span>Rs. {{ $totalPrice }}</span></h4>
                    </div>
                </div>
                <div class="checkout-payment-wrapper mt-30">
                    <h4 class="allup-title">Payment Method</h4>
                    <div class="checkout-payment mt-30">
                        <ul>
                            <li>
                                <div class="single-payment">
                                    <div class="payment-radio">
                                        <input type="radio" name="payment" value="1" id="razorpay" checked="checked">
                                        <label for="razorpay"><span></span> RazorPay</label>
                                        <div class="payment-details">
                                            <p>Pay your amount securely through our payment gateway.</p>
                                        </div>
                                    </div>
                                    <div class="payment-radio">
                                        <input type="radio" name="payment" value="2" id="cash">
                                        <label for="cash"><span></span> Cash on delivery</label>
                                        <div class="payment-details">
                                            <p>Delivery time the amount will be collected.</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="checkout-btn">
                    @if(!empty($cartList))
                    <button type="submit" class="main-btn main-btn-2">Place Order</button>
                    @else
                    <a class="main-btn main-btn-2" href="url('shop')">Continue Shopping</a>
                    @endif
                </div>
            </div>
            </form>
        </div>
    </div>
</section>

<!--====== Checkout Part Ends ======-->

@endsection