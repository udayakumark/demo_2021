@extends('layouts.admin-layout')
@section('title', 'Manage Branch')
@section('script-src', asset('public/admin-assets/pages/manage-dealers.js'))
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-6"><h4>Manage Branch</h4></div>
              <div class="col-6">
                <a href="{{ url('admin/create-dealer') }}" class="btn btn-primary pull-right"><i class="fas fa-plus"></i> Create Branch</a>
                <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
              </div>
            </div>
            <div class="card-body">
              <!-- Advanced searchForm -->
              <div class="col-md-12 collapse" id="search-form">
                <form name="search-form" id="SearchForm" method="post" action="#">
                  <div class="form-row">
                    <div class="col-md-4 form-group">
                      <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Enter the full name">
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="Enter the mobile number">
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" name="email_id" id="email_id" class="form-control" placeholder="Enter the email id">
                    </div>
                    <div class="col-md-4 form-group">
                      <input type="text" name="address" id="address" class="form-control" placeholder="Enter the address hint">
                    </div>
                    <div class="col-md-4 form-group">
                      <button type="submit" name="search" class="btn btn-success">Search</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-striped" id="dealer-table" data-url="{{ url('/admin/dealer-list') }}">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>Branch Name</th>
                      <th>Dealer Name</th>
                      <th>Mobile Number</th>
                      <th>Address</th>
                      <th>Profile Image</th>
                      <th style="width: 80px;">Date</th>
                      <th style="width: 60px;">Action</th>
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