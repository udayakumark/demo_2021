@extends('layouts.site-layout')
@section('title', 'Sakthi Rice | Gallery')
@section('content')
<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->
<!--====== Gallery image Part Start ======-->
<section class="shop-area pt-20">
    <div class="container-fluid custom-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="grid" role="tabpanel" aria-labelledby="grid-tab">
                        <div class="row">
                            @if(count($Gallery)>0)
                            @foreach($Gallery as $gallery)
                            <div class="col-xl-4 col-md-4 col-sm-6">
                                <div class="single-product mt-30">
                                    <div class="product-image">
                                        <img src="{{ asset('public/'.$gallery['image']) }}" style="height:340px;">
                                    </div>
                                    <div class="product-content">
                                        <p class="product-title">{{ $gallery['title'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Gallery image Part Ends ======-->
@endsection