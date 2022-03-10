@extends('layouts.site-layout')
@section('title', 'Product Detail')
@section('script-src', asset('public/site-assets/pages/products-detail.js'))
@section('content')
<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/shop') }}">Shop</a></li>
                <li class="breadcrumb-item active">Product Details</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->

<!--====== Single Product Part Start ======-->
<section class="single-product-page pt-20">
    <div class="container-fluid custom-container">
        <div class="row">
            <div class="col-lg-6">
                <div class="single-product-image mt-30">
                    <div class="product-single-image">
                        <div class="single-image">
                            <img src="{{ asset('public/'.$product_details[0]->product_image) }}" alt="">
                        </div>
                    </div>
                    <div class="product-single-thumb-image">
                        <ul class="product-single-thumb">
                            <li><img src="{{ asset('public/'.$product_details[0]->product_image) }}" alt="product"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="single-product-content mt-30">
                    <h2 class="product-title" id="product-title">{{ $product_details[0]->product_name }} ({{ $product_prices[0]->quantity }} KG)</h2>
                    <div class="review-rating d-flex flex-wrap">
                    </div>
                    <div class="product-price">
                        <span class="price-sale" id="original_price">RS. {{ $product_prices[0]->original_price }}</span>
                        <span class="regular-price" id="selling_price">RS. {{ $product_prices[0]->selling_price }}</span>
                    </div>
                    <div class="product-basic-info">
                        <div class="single-info">
                            <span class="title">Discount :</span>
                            <span class="value" id="discount_percentage">{{ $product_prices[0]->discount_percentage }} %</span>
                        </div>
                        <div class="single-info">
                            <span class="title">Category :</span>
                            <span class="value"><a href="javascript:void(0)">{{ $product_details[0]->category->category_name }}</a></span>
                        </div>
                        <div class="single-info">
                            <span class="title"> Product Code :</span>
                            <span class="value">{{ $product_details[0]->product_code }}</span>
                        </div>
                        <div class="single-info">
                            <span class="title"> Availability :</span>
                            <span class="value">In stock </span>
                        </div>
                    </div>
                    <div class="product-size-color-quantity">
                        <div class="product-quantity mt-25">
                            <h5 class="title">Available Packs</h5>
                            <div class="quantity mt-15">
                                <select name="packs" id="packs" class="form-control">
                                    @foreach ($product_prices as $price)
                                    <option value="{{ $price->id }}">{{ $price->quantity }} KG</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="product-size-color-quantity">
                        <div class="product-quantity mt-25">
                            <h5 class="title">Quantity</h5>
                            <div class="quantity mt-15 d-flex">
                                <button type="button" class="sub"><i class="fal fa-minus"></i></button>
                                <input type="text" id="quantity" value="1" />
                                <button type="button" class="add"><i class="fal fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="addcart">
                        @if(empty($cart_products))
                        <button class="addcart-button" id="cart_button"><i class="icon ion-bag"></i> Add to cart</button>
                        @else
                        <button class="addcart-button" id="cart_button"><i class="icon ion-bag"></i> Update cart</button>
                        @endif
                    </div>
                    <div class="product-wishlist-compare">
                    </div>
                    <div class="product-share d-flex">
                    </div>
                    <div class="reassurance-block">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="single-reassurance d-flex">
                                    <div class="reassurance-icon">
                                        <img src="{{ asset('public/site-assets/images/icon-1.png') }}" alt="icon">
                                    </div>
                                    <div class="reassurance media-body">
                                        <p>Security policy (edit with Customer reassurance module)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="single-reassurance d-flex">
                                    <div class="reassurance-icon">
                                        <img src="{{ asset('public/site-assets/images/icon-2.png') }}" alt="icon">
                                    </div>
                                    <div class="reassurance media-body">
                                        <p>Delivery policy (edit with Customer reassurance module)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="single-reassurance d-flex">
                                    <div class="reassurance-icon">
                                        <img src="{{ asset('public/site-assets/images/icon-3.png') }}" alt="icon">
                                    </div>
                                    <div class="reassurance media-body">
                                        <p>Return policy (edit with Customer reassurance module)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Single Product Part Ends ======-->

<!--====== Product Description Part Start ======-->
<section class="product-description-area pt-50">
    <div class="container-fluid custom-container">
        <ul class="nav justify-content-center" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">Description</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                <div class="description">
                    <p>{{ $product_details[0]->product_description }}</p>
                </div>
            </div>
            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
            </div>
        </div>
    </div>
</section>
<!--====== Product Description Part Ends ======-->

@endsection