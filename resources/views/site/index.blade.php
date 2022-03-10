@extends('layouts.site-layout')
@section('title', 'Sakthi Rice | Home')
@section('content')


<!--====== Slider Part Start ======-->
@if(!empty($Banners))
<section class="slider-area slider-active">
    @foreach ($Banners as $banner)
    <div class="single-slider">
        <div class="slider-image">
            <img src="{{ asset('public/'.$banner->image) }}" style="height: 480px;" alt="slider">
        </div>
        <div class="slider-content-wrapper">
            <div class="container-fluid custom-container">
                <div class="slider-content">
                    <h4 data-animation="fadeInDown" data-delay="0.5s" class="sub-title">{{ $banner->sub_title }}</h4>
                    <h1 data-animation="fadeInLeft" data-delay="0.5s" class="main-title ">{{ $banner->title }}</h1>
                    <p data-animation="fadeInRightBig" data-delay="0.5s">{{ $banner->description }}</p>
                    <a data-animation="zoomIn" data-delay="0.5s" class="main-btn " href="{{ url('/product-detail',array('id'=>$banner->product_id)) }}">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</section>
@endif
<!--====== Slider Part Ends ======-->


<!--====== New Arrivals Part Start ======-->

<section class="product-area pt-100">
    <div class="container-fluid custom-container">
        @if(!empty($latest_products))
        <div class="row">
            <div class="col-lg-12">
                <div class="product-menu pb-30">
                    <ul class="nav justify-content-center" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="active" id="computer-tab" data-toggle="tab" href="#computer" role="tab" aria-controls="computer" aria-selected="true">New Arrivals</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="computer" role="tabpanel" aria-labelledby="new-tab">
                <div class="product-items">
                    <div class="row product-active">
                        @foreach ($latest_products as $product)
                        <div class="col-lg-12">
                            <div class="single-product mt-30">
                                <div class="product-image">
                                    <a href="{{ url('/product-detail',array('id'=>$product['product']->id)) }}">
                                        <img class="first-image" style="height: 298px;" src="{{ asset('public/'.$product['product']->product_image) }}" alt="product">
                                        <img class="second-image" style="height: 298px;" src="{{ asset('public/'.$product['product']->product_image) }}" alt="product">
                                    </a>
                                </div>
                                <div class="product-content">
                                    <h4 class="product-title"><a href="{{ url('/product-detail',array('id'=>$product['product']->id)) }}">{{ $product['product']->product_name }} ({{ $product['product_prices'][0]->quantity }} KG)</a></h4>
                                    <div class="product-price">
                                        <span class="price-sale">RS. {{ $product['product_prices'][0]->selling_price }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!--====== New Arrivals Part Ends ======-->


<!--====== Testimonial Part Start ======-->
@if(!empty($Testimonials))
<section class="testimonial-area bg_cover pt-100 pb-100" style="margin-top:20px;background-image: url('{{ asset('public/site-assets/images/bg_testimonial.jpg') }}');">
    <div class="container">
        <div class="row">
            <div class="col-lg-10">
                <div class="testimonial-active">
                    @foreach ($Testimonials as $testimonial)
                    <div class="single-testimonial d-sm-flex">
                        <div class="testimonial-author">
                            <img src="{{ asset('public/'.$testimonial->image) }}" style="height: 98px;width: 94px;" alt="author">
                        </div>
                        <div class="testimonial-content media-body">
                            <p>{{ $testimonial->content }}</p>
                            <h4 class="author-name">{{ $testimonial->name }}</h4>
                            <span class="designation">{{ $testimonial->email_id }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<!--====== Testimonial Part Ends ======-->

@endsection