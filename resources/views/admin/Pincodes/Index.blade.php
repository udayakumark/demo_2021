@extends('layouts.admin-layout')
@section('title', 'Manage Pincodes')
@section('script-src', asset('public/admin-assets/pages/manage-pincodes.js'))
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>Manage Pincodes</h4></div>
              <div class="col-4">
                <button type="button" class="btn btn-primary pull-right" id="create" data-url="{{ url('admin/create-pincode') }}"><i class="fas fa-plus"></i> Create</button>
                <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
              </div>
            </div>
            <div class="card-body">
              <!-- Advanced searchForm -->
              <div class="col-md-12 collapse" id="search-form">
                <form name="search-form" id="SearchForm" method="post" action="#">
                  <div class="form-row">
                    <div class="col-4 form-group">
                      <input type="number" name="pincode" id="pincode" class="form-control" placeholder="Enter the pincode number">
                    </div>
                    <div class="col-4 form-group">
                      <input type="text" name="city" id="city" class="form-control" placeholder="Enter the city name">
                    </div>
                    <div class="col-md-4 form-group">
                      <button type="submit" name="search" class="btn btn-success">Search</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="table-responsive">
                <table class="table table-striped" id="pincode-table" data-url="{{ url('/admin/pincode-list') }}">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>City Name</th>
                      <th>Pincode</th>
                      <th>Date & Time</th>
                      <th>Status</th>
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
<div class="modal fade" id="formModal" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
@endsection