@extends('layouts.admin-layout')
@section('title', 'View Bank Details')
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>View Bank Details</h4></div>
              <div class="col-4 text-right">  <a href="{{ url('/admin/bank') }}" class="btn btn-info btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>            </div>
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Order details -->
                <div class="col-md-12">
                  <div class="list-group">
                    <li class="list-group-item active">Bank Details</li>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="list-group">
                    <li class="list-group-item"><b>Bank Name</b> : {{ (isset($model->bank_name) && $model->bank_name!="") ? $model->bank_name : '' }}</li>
                    <li class="list-group-item"><b>Account Name</b> : {{ (isset($model->account_name) && $model->account_name!="") ? $model->account_name: '' }}</li>
                    <li class="list-group-item"><b>Account Number</b> : {{ (isset($model->account_no) && $model->account_no!="") ? $model->account_no: '' }}</li>
                    <li class="list-group-item"><b>IFSC Code</b> : {{ (isset($model->ifsc) && $model->ifsc!="") ? $model->ifsc : '' }}</li>
                  </div>
                </div>
                <div class="col-md-6">
                  <li class="list-group-item"><b>Branch </b>: {{ (isset($model->branch) && $model->branch!="") ? $model->branch: '' }}</li>
                  <li class="list-group-item"><b>Address</b> : {{ (isset($model->bank_address) && $model->bank_address!="") ? $model->bank_address: '' }}</li>
                  <li class="list-group-item"><b>Account Type</b> : {{ (isset($model->type) && $model->type!="") ? $model->type: '' }}</li>
                  <li class="list-group-item"><b>Current Balance</b> : {{ (isset($model->current_balance) && $model->current_balance!="") ? "₹ ".number_format($model->current_balance, 2) : '' }}</li>
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