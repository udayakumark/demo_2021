@extends('layouts.site-layout')
@section('title', 'Cart')
@section('script-src', asset('public/site-assets/pages/cart.js'))
@section('content')

<!--====== Breadcrumb Part Start ======-->

<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active" onclick="clicks();">Cart</li>
            </ol>
        </nav>
        </div> <!-- container -->
    </div>

<!--====== Breadcrumb Part Ends ======-->

<!--====== Cart Part Start ======-->

<section class="cart-page pt-10">
    <div class="container-fluid custom-container">
        <div class="row">
            <div class="col-xl-8">
                <div class="shopping-cart mt-25">
                    <h4 class="allup-title">Shopping Cart</h4>
                    <form id="cart-form" action="{{ url('updateCart') }}" method="POST">
                    @csrf
                    <div class="shopping-cart-table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="image">Image</th>
                                    <th class="product">Product</th>
                                    <th class="price">Price</th>
                                    <th class="quantity">Quantity</th>
                                    <th class="total">Total</th>
                                    <th class="delete">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($cartList))
                                @foreach($cartList as $cart)
                                <tr>
                                    <td>
                                        <div class="product-image">
                                            <img src="{{ asset('public/'.$cart['product_image']) }}" alt="cart" style="height: 80px;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-title">
                                            <h4 class="title"><a href="single-product.html">{{ $cart['product_name'] }} ({{ $cart['product_size'] }} KG)</a></h4>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-price">
                                            <span class="price">Rs. {{ $cart['product_price'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-quantity">
                                            <input type="hidden" name="cart_id[]" value="{{ $cart['cart_id'] }}">
                                            <div class="quantity mt-15 d-flex">
                                                <button type="button" class="sub"><i class="fal fa-minus"></i></button>
                                                <input type="text" name="quantity[]" value="{{ $cart['quantity'] }}" />
                                                <button type="button" class="add"><i class="fal fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-total">
                                            <span class="total-amount">Rs. {{ $cart['product_price']*$cart['quantity'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-delete">
                                            <a href="javascript:void(0)" cart-id="{{ $cart['cart_id'] }}" class="removecart-btn"><i class="fal fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="6">No Products available in your cart</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="cart-btn text-right">
                        @if(!empty($cartList))
                        <button class="main-btn" type="submit">Update cart</button>
                        @else
                        <a class="main-btn" href="{{ url('shop') }}">Go to Purchase</a>
                        @endif
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="row">
                    @if(!empty($cartList))
<!--                     <div class="col-xl-12 col-md-6">
                        <div class="cart-coupon mt-30">
                            <h5 class="title">Discount Coupon Code</h5>
                            <form action="#">
                                <div class="single-form coupon-form d-flex flex-wrap">
                                    <input type="text" placeholder="Coupon Code">
                                    <button class="main-btn">Apply Coupon</button>
                                </div>
                            </form>
                        </div>
                    </div> -->
                    <div class="col-xl-12 col-md-6">
                        <div class="cart-total mt-30">
                            <div class="sub-total">
                                <div class="single-total">
                                    <span class="cart-value">Subtotal</span>
                                    <span class="cart-amount">Rs. {{ $totalPrice }}</span>
                                </div>
                                <div class="single-total">
                                    <span class="cart-value">Shipping Cost</span>
                                    <span class="cart-amount">Rs. 0</span>
                                </div>
                            </div>
                            <div class="total">
                                <div class="single-total">
                                    <span class="cart-value">Total</span>
                                    <span class="cart-amount">Rs. {{ $totalPrice }}</span>
                                </div>
                            </div>
                            <div class="cart-total-btn text-right">
                                <a class="main-btn" href="{{ url('checkout') }}">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!--====== Cart Part Ends ======-->

@endsection