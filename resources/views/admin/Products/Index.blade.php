@extends('layouts.admin-layout')
@section('title', 'Manage Products')
@section('script-src', asset('public/admin-assets/pages/manage-products.js'))
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>Manage Products</h4></div>
              <div class="col-4">
                <a href="{{ url('admin/create-product') }}" class="btn btn-primary pull-right"><i class="fas fa-plus"></i> Create</a>
                <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
              </div>
            </div>
            <div class="card-body">
              <!-- Advanced searchForm -->
              <div class="col-md-12 collapse" id="search-form">
                <form name="search-form" id="SearchForm" method="post" action="#">
                  <div class="form-row">
                    <div class="col-md-4 form-group">
                      <select class="form-control" name="product_category">
                        <option value="">Select Category</option>
                        @foreach ($categoryList as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                      </select>
                    </div>
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
                <table class="table table-striped" id="product-table" data-url="{{ url('/admin/product-list') }}">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th>Category Name</th>
                      <th>Product Name</th>
                      <th>Product Code</th>
                      <th>Product Image</th>
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