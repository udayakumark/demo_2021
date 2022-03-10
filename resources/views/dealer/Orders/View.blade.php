@extends('layouts.dealer-layout')
@section('title', 'View Order')
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>View Order Details</h4></div>
              <div class="col-4">
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Order details -->
                <div class="col-md-12">
                  <div class="list-group">
                    <li class="list-group-item active">Order Details</li>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="list-group">
                    <li class="list-group-item">Customer Name : {{ (isset($model->user->userDetails->first_name) && $model->user->userDetails->first_name!="") ? $model->user->userDetails->first_name." ".$model->user->userDetails->last_name : '' }}</li>
                    <li class="list-group-item">Supplier Name : {{ (isset($model->supplier->userDetails->first_name) && $model->supplier->userDetails->first_name!="") ? $model->supplier->userDetails->first_name." ".$model->supplier->userDetails->last_name : '' }}</li>
                    <li class="list-group-item">Order ID : {{ (isset($model->order_id) && $model->order_id!="") ? $model->order_id : '' }}</li>
                    <li class="list-group-item">Order Status : {{ (isset($model->order_status) && $model->order_status!="") ? OrderStatus($model->order_status) : '' }}</li>
                    <li class="list-group-item">Payment Type : {{ (isset($model->payment_type) && $model->payment_type!="") ? PaymentType($model->payment_type) : '' }}</li>
                    <li class="list-group-item">Payment Status : {{ (isset($model->payment_status) && $model->payment_status!="") ? PaymentType($model->payment_status) : '' }}</li>
                    <li class="list-group-item">Total Amount : {{ (isset($model->total_amount) && $model->total_amount!="") ? $model->total_amount : '' }}</li>
                    <li class="list-group-item">Delivery Amount : {{ (isset($model->shipping_amount) && $model->shipping_amount!="") ? $model->shipping_amount : '' }}</li>
                    <li class="list-group-item">SubTotal Amount : {{ (isset($model->subtotal_amount) && $model->subtotal_amount!="") ? $model->subtotal_amount : '' }}</li>
                    <li class="list-group-item">Order Date : {{ (isset($model->date_time) && $model->date_time!="") ? date('d-M-Y h:i a',strtotime($model->date_time)) : '' }}</li>
                  </div>
                </div>
                <div class="col-md-6">
                  <li class="list-group-item">Billing Name : {{ (isset($model->billing_firstname) && $model->billing_firstname!="") ? $model->billing_firstname." ".$model->billing_lastname : '' }}</li>
                  <li class="list-group-item">Billing Email : {{ (isset($model->billing_email) && $model->billing_email!="") ? $model->billing_email : '' }}</li>
                  <li class="list-group-item">Billing Mobile : {{ (isset($model->billing_mobile) && $model->billing_mobile!="") ? $model->billing_mobile : '' }}</li>
                  <li class="list-group-item">Billing Address : {{ (isset($model->billing_address) && $model->billing_address!="") ? $model->billing_address : '' }}</li>
                  <li class="list-group-item">Billing Pincode : {{ (isset($model->billingPincode->pincode) && $model->billingPincode->pincode!="") ? $model->billingPincode->pincode : '' }}</li>
                  <li class="list-group-item">Shipping Name : {{ (isset($model->shipping_firstname) && $model->shipping_firstname!="") ? $model->shipping_firstname." ".$model->shipping_lastname : '' }}</li>
                  <li class="list-group-item">Shipping Email : {{ (isset($model->shipping_email) && $model->shipping_email!="") ? $model->shipping_email : '' }}</li>
                  <li class="list-group-item">Shipping Mobile : {{ (isset($model->shipping_mobile) && $model->shipping_mobile!="") ? $model->shipping_mobile : '' }}</li>
                  <li class="list-group-item">Shipping Address : {{ (isset($model->shipping_address) && $model->shipping_address!="") ? $model->shipping_address : '' }}</li>
                  <li class="list-group-item">Shipping Pincode : {{ (isset($model->shippingPincode->pincode) && $model->shippingPincode->pincode!="") ? $model->shippingPincode->pincode : '' }}</li>
                </div>
                <!-- Ordered Products -->
                <div class="col-md-12">
                  <div class="list-group">
                    <li class="list-group-item active">Ordered Products</li>
                  </div>
                </div>
                <div class="col-md-12">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>Product Name</th> 
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(count($products_model)>0)
                    @foreach($products_model as $index=>$product)
                    <tr>
                      <td class="text-center">{{ ($index+1) }}</td>
                      <td>{{ $product->product_name }}</td> 
                      <td>{{ $product->price }}</td>
                      <td>{{ $product->quantity }}</td>
                      <td>{{ $product->total_price }}</td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection