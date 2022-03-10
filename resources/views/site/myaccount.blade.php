@extends('layouts.site-layout')
@section('title', 'My Account')
@section('script-src', asset('public/site-assets/pages/myaccount.js'))
@section('content')
<!-- Hidden Urls -->
<input type="Hidden" name="ajax-url" id="ajax-url" value="{{ url('/myorder-list') }}">
<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->
<!--====== My Account Part Start ======-->
<section class="my-account-area pt-10">
    <div class="container-fluid custom-container">
        <div class="row">
            <div class="col-xl-4 col-md-4">
                <div class="my-account-menu mt-30">
                    <ul class="nav account-menu-list flex-column nav-pills" id="pills-tab" role="tablist">
                        <li>
                            <a class="active" id="pills-dashboard-tab" data-toggle="pill" href="#pills-dashboard" role="tab" aria-controls="pills-dashboard" aria-selected="true"><i class="far fa-tachometer-alt-fast"></i> Dashboard</a>
                        </li>
                        <li>
                            <a id="pills-order-tab" data-toggle="pill" href="#pills-order" role="tab" aria-controls="pills-order" aria-selected="false"><i class="far fa-shopping-cart"></i> Order</a>
                        </li>
                        <li>
                            <a id="pills-account-tab" data-toggle="pill" href="#pills-changepassword" role="tab" aria-controls="pills-changepassword" aria-selected="false"><i class="far fa-user"></i> Change Password</a>
                        </li>
                        <li>
                            <a href="{{ url('/logout') }}"><i class="far fa-sign-out-alt"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-8 col-md-8">
                <!-- Error Messages -->
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <div class="tab-content my-account-tab mt-30" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-dashboard" role="tabpanel" aria-labelledby="pills-dashboard-tab">
                        <div class="my-account-dashboard account-wrapper">
                            <h4 class="account-title">Dashboard</h4>
                            <div class="welcome-dashboard">
                                <p>Hello, <strong>{{ Auth::guard('web')->user()->userDetails->first_name.' '.Auth::guard('web')->user()->userDetails->last_name }}</strong></p>
                            </div>
                            <p class="mt-25">From your account dashboard. you can easily check & view your recent orders, manage your shipping and billing addresses and edit your password and account details.</p>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab">
                        <div class="my-account-order account-wrapper">
                            <h4 class="account-title">Orders</h4>
                            <div class="account-table text-center mt-30 table-responsive">
                                <!-- Advanced searchForm -->
                                <div class="col-md-12 collapse" id="search-form">
                                    <form name="search-form" id="SearchForm" method="post" action="#">
                                        <div class="form-row">
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter the product name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="product_code" id="product_code" class="form-control" placeholder="Enter the product code">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <button type="submit" name="search" class="btn btn-success">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="orders-table" data-url="{{ url('/myorder-list') }}">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Order ID</th>
                                                <th>Total Amount</th>
                                                <th>Payment Type</th>
                                                <th>Payment Status</th>
                                                <th>Order Status</th>
                                                <th>Date & Time</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-changepassword" role="tabpanel" aria-labelledby="pills-account-tab">
                        <div class="my-account-details account-wrapper">
                            <h4 class="account-title">Change Password</h4>
                            <div class="account-details">
                            <form action="{{ url('changepassword') }}" id="changepassword-form" method="POST">
                            @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="single-form">
                                            <input type="password" name="current_password" placeholder="Current Password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-form">
                                            <input type="password" name="password" placeholder="New Password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-form">
                                            <input type="password" name="password_confirmation" placeholder="Confirm Password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-form">
                                            <button class="main-btn main-btn-2" type="submit">Save Change</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== My Account Part Ends ======-->
@endsection