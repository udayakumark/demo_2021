@extends('layouts.site-layout')
@section('title', 'Products')
@section('script-src', asset('public/site-assets/pages/products.js'))
@section('content')

<!-- Hidden Urls -->
<input type="Hidden" name="ajax-url" id="ajax-url" value="{{ url('/product-list') }}">

    <!--====== Breadcrumb Part Start ======-->

    <div class="breadcrumb-area">
        <div class="container-fluid custom-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Shop</li>
                </ol>
            </nav>
        </div>
    </div>

    <!--====== Breadcrumb Part Ends ======-->

    <!--====== Shop Left sidebar Part Start ======-->

    <section class="shop-area pt-20">
        <div class="container-fluid custom-container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-header">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <div class="shop-header-left d-flex flex-wrap align-items-center">
                                    <div class="shop-header-icon">
                                        <ul class="nav" id="myTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="active" id="grid-tab" data-toggle="tab" href="#grid" role="tab" aria-controls="grid" aria-selected="true"><i class="icon ion-grid show_grid"></i></a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a id="list-tab" data-toggle="tab" href="#list" role="tab" aria-controls="list" aria-selected="false"><i class="icon ion-android-menu show_list"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="shop-header-message">
                                        <p id="shop-pagination"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="shop-header-right d-flex flex-wrap align-items-center justify-content-sm-end">
                                    <div class="shop-header-select">
                                        <select id="product_category" style="padding: 6px;">
                                            <option value="">Select Product Category</option>
                                            @foreach (App\Models\ProductCategories::where('status',1)->get() as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="shop-header-select">
                                        <input type="text" name="product_name" id="product_name" style="border: 0;padding: 4px;" placeholder="Search By Product Name">
                                    </div>
                                    <div class="shop-header-select">
                                        <select id="order_filter" style="padding: 6px;">
                                            <option value="1">Default Order</option>
                                            <option value="2">Name (A-Z)</option>
                                            <option value="3">Name (Z-A)</option>
                                        </select>
                                    </div>
                                    <div class="shop-header-select">
                                        <button type="button" id="search" class="btn btn-success btn-xs" style="font-size: 8px;"><i class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- row -->
                    </div> <!-- shop header -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="grid" role="tabpanel" aria-labelledby="grid-tab">
                            <div class="row product-grids">
                            </div> <!-- row -->
                        </div>
                        <div class="tab-pane fade product-lists" id="list" role="tabpanel" aria-labelledby="list-tab">
                        </div>
                    </div> <!-- Tab Content -->
                    
                <div class="reach-loader" style="width: 100%;text-align: center;margin-top: 20px;display: none;">
                    <div class="ajax-loader">
                        <img src="{{ asset('public/site-assets/images/ajax-loader.gif') }}" style="height: 110px;">
                        <br>
                        <p style="font-weight: bold;color: green;">Loading Products !</p>
                    </div>
                </div>
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </section>

    <!--====== Shop Left sidebar Part Ends ======-->

@endsection