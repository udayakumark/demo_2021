@extends('layouts.admin-layout')
@section('title', 'Create Product')
@section('script-src', asset('public/admin-assets/pages/manage-products.js'))
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>{{ $name }} Product</h4></div>
              <div class="col-4">
              </div>
            </div>
            <div class="card-body">
              <div class="alert alert-danger print-error-msg" style="display:none">
              <ul></ul>
            </div>
            <form method="POST" action="{{ url($action) }}" redirect-url="{{ url($redirectUrl) }}" id="form" novalidate="">
              @csrf
              <div class="form-row">
                <div class="col-md-3 form-group">
                  <label>Product Category</label>
                  <select class="form-control select2" name="product_category">
                    <option value="">Select Category</option>
                    @foreach ($categoryList as $category)
                    <option value="{{ $category->id }}" {{ $model->category_id==$category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 form-group">
                  <label>Product Name</label>
                  <input type="text" name="product_name" placeholder="Enter Product Name" class="form-control" value="{{ $model->product_name }}">
                </div>
                <div class="col-md-3 form-group">
                  <label>Product Code</label>
                  <input type="text" name="product_code" placeholder="Enter Product Code" class="form-control" value="{{ $model->product_code }}">
                </div>
                <div class="col-md-2 form-group">
                  <label>Product Image</label>
                  <input type="file" name="product_image" placeholder="Select Product Image" class="form-control" value="">
                </div>
                <div class="col-md-1 form-group">
                  @if($model->product_image!="")
                  <img src="{{ url('public/'.$model->product_image) }}" class="grid-image">
                  @endif
                </div>
                <div class="col-md-12 form-group">
                  <label>Product Description</label>
                  <textarea name="product_description" placeholder="Enter Product Description" class="form-control">{{ $model->product_description }}</textarea>
                </div>
                <!-- Product Prices -->
                <div class="table-responsive">
                  <table class="table table-striped" id="product-pricesform">
                    <thead>
                      <tr class="text-center">
                        <th>#</th>
                        <th>Price Type</th>
                        <th>Quantity</th>
                        <th>Original Price</th>
                        <th>Selling Price</th>
                        <th>Status</th>
                        <th>
                          <button type="button" class="btn btn-icon btn-success priceadd-button">
                          <i class="fas fa-plus"></i>
                          </button>
                        </th>
                      </tr>
                    </thead>
                    <tbody id="price-data">
                      @if (isset($priceList))
                      @foreach ($priceList as $key=>$price)
                      <tr class="text-center product-prices">
                        <input type="hidden" name="price_id[]" value="{{ $price->id }}">
                        <td width="4%" class="snumber">{{ $key+1 }}</td>
                        <td width="8%">KG</td>
                        <td width="20%">
                          <input type="number" name="quantity[]" placeholder="Enter Quantity" class="form-control" value="{{ $price->quantity }}">
                        </td>
                        <td width="20%">
                          <input type="number" name="originalprice[]" placeholder="Enter Original Price" class="form-control" value="{{ $price->original_price }}">
                        </td>
                        <td width="20%">
                          <input type="number" name="sellingprice[]" placeholder="Enter Selling Price" class="form-control" value="{{ $price->selling_price }}">
                        </td>
                        <td width="20%">
                          <label class="custom-switch mt-2">
                            <input type="checkbox" name="status{{ $key }}" class="custom-switch-input" {{ $price->status==1 ? 'checked' : '' }}>
                            <span class="custom-switch-indicator"></span>
                          </label>
                        </td>
                        <td width="8%">
                          <button type="button" class="btn btn-icon btn-danger  priceremove-button">
                          <i class="fas fa-times"></i>
                          </button>
                        </td>
                      </tr>
                      @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <div class="col-md 12 form-group text-center">
                  <a href="{{ url('/admin/products') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                  {{ $name }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</div>
@endsection
