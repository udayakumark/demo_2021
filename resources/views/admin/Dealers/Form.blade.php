@extends('layouts.admin-layout')
@section('title', $name.' Branch')
@section('script-src', asset('public/admin-assets/pages/manage-dealers.js'))
@section('content')
<div class="main-content">
  <section class="section" id="grid-section">
    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="col-8"><h4>{{ $name }} Branch</h4></div>
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
                  <label>Branch Name</label>
                  <input type="text" name="first_name" placeholder="Enter Branch Name" class="form-control" value="{{ $usermodel->first_name }}">
                </div>
                <div class="col-md-3 form-group">
                  <label>Dealer Name</label>
                  <input type="text" name="last_name" placeholder="Enter Dealer Name" class="form-control" value="{{ $usermodel->last_name }}">
                </div>

                <div class="col-md-3 form-group">
                  <label>Mobile Number</label>
                  <input type="number" max="10" name="mobile_number" placeholder="Enter Mobile Number" class="form-control" value="{{ $model->mobile_number }}">
                </div>
                <div class="col-md-3 form-group">
                  <!-- <label>Email Id</label>
                  <input type="text" name="email_id" placeholder="Enter Email-ID" class="form-control" value="{{ $model->email_id }}"> -->
                </div>
                
                @if($type==1)
                <div class="col-md-3 form-group">
                  <label>User Name</label>
                  <input type="text" name="user_name" placeholder="Enter User Name" class="form-control" value="{{ $model->user_name }}">
                </div>
                <div class="col-md-3 form-group">
                  <label>Password</label>
                  <input type="password" name="password" placeholder="Enter Password" class="form-control" value="{{ $model->password }}">
                </div>
                @endif

                <div class="col-md-3 form-group">
                  <label>DOB</label>
                  <input type="date" name="dob" placeholder="Enter DOB" class="form-control" value="{{ $usermodel->dob }}">
                </div>
                <div class="col-md-3 form-group">
                  <label>City</label>
                  <select class="form-control select2" name="city">
                    <option value="">Select City</option>
                    @foreach ($cityList as $city)
                    <option value="{{ $city->id }}" {{ $usermodel->city_id==$city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                    @endforeach
                  </select> 
                </div>
                <div class="col-md-3 form-group">
                  <label>Pincode</label>
                  <input type="number" max="6" name="pincode" placeholder="Enter Pincode" class="form-control" value="{{ $usermodel->pincode }}">
                </div>
                <div class="col-md-3 form-group">
                  <label>Address</label>
                  <textarea rows="4" name="address" placeholder="Enter Address" class="form-control">{{ $usermodel->address }}</textarea>
                </div>
                <div class="col-md-3 form-group">
                  <label>Profile Image</label>
                  <input type="file" name="profile_image" placeholder="Select Profile Image" class="form-control" value="">
                </div>
                <div class="col-md-1 form-group">
                  @if($usermodel->profile_image!="")
                  <img src="{{ url('public/'.$usermodel->profile_image) }}" class="grid-image">
                  @endif
                </div>
                <div class="col-md-12 form-group">
                  <h4>Allocated Pincodes</h4>
                </div>
                <div class="col-md-12 form-group">
                  <label>Order Pincodes</label>
                  <select class="form-control select2" name="dealer_pincodes[]" multiple="">
                    @foreach ($pincodeList as $pincode)
                    <option value="{{ $pincode->id }}" {{ in_array($pincode->id,$userPincodeList) ? 'selected' : '' }}>{{ $pincode->pincode }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-12 form-group text-center">
                  <a href="{{ url('/admin/dealers') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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