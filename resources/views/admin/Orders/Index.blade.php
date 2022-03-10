@extends('layouts.admin-layout')
@section('title', 'Manage Orders')
@section('script-src', asset('public/admin-assets/pages/manage-orders.js'))
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>Manage Orders</h4></div>
              <div class="col-4">
                <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
              </div>
            </div>
            <div class="card-body">
              <!-- Advanced searchForm -->
              <div class="col-md-12 collapse" id="search-form">
                <form name="search-form" id="SearchForm" method="post" action="#">
                  <div class="form-row">
                    <div class="col-md-4 form-group">
                      <select class="form-control" name="user_id">
                        <option>Select User</option>
                        @foreach ($usersList as $user)
                        <option value="{{ $user->id }}">{{ $user->userDetails->first_name.' '.$user->userDetails->last_name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control" name="supplier_id">
                        <option>Select Supplier</option>
                        @foreach ($supplierList as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->userDetails->first_name.' '.$supplier->userDetails->last_name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control" name="payment_type">
                        <option>Select Payment Type</option>
                        <option value="1">Razor Pay</option>
                        <option value="2">Cash On Delivery</option>
                      </select>
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control" name="payment_status">
                        <option>Select Payment Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Success</option>
                        <option value="2">Failed</option>
                      </select>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" name="payment_reference" class="form-control" placeholder="Enter the payment reference number">
                    </div>
                    <div class="col-md-4 form-group">
                      <select class="form-control" name="order_status">
                        <option>Select Order Status</option>
                        <option value="1">Order Placed</option>
                        <option value="2">Order InProgress</option>
                        <option value="3">Order Shipped</option>
                        <option value="4">Order Delivered</option>
                        <option value="5">Order Cancelled</option>
                      </select>
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="date" name="from_date" class="form-control" placeholder="Enter the From Date">
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="date" name="to_date" class="form-control" placeholder="Enter the To Date">
                    </div>
                    <div class="col-md-4 form-group">
                      <button type="submit" name="search" class="btn btn-success">Search</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-striped" id="order-table" data-url="{{ url('/admin/order-list') }}">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>Order ID</th> 
                      <th>Customer Name</th>
                      <th>Supplier Name</th>
                      <th>Amount</th>
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
      </div>
    </div>
  </section>
</div>
@endsection