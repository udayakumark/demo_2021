@extends('layouts.admin-layout')
@section('title', 'Change Password')
@section('script-src', asset('public/admin-assets/pages/changepassword.js'))
@section('content')
<div class="main-content">
  <section class="section">
    <div class="row">
      <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>Change Password</h4>
          </div>
          <div class="card-body">
            <!-- Error Messages -->
            <div class="alert alert-danger print-error-msg" style="display:none">
              <ul></ul>
            </div>
            <form action="{{ url('admin/changepassword') }}" redirect-url="{{ url('admin/changepassword') }}" id="changepassword-form" method="POST">
              @csrf
              <div class="form-row">
                <div class="col-md-4 form-group"></div>
                <div class="col-md-4 form-group">
                  <label>Current Password</label>
                  <input type="password" class="form-control" name="current_password" placeholder="Current Password">
                  <br>
                  <label>New Password</label>
                  <input type="password" class="form-control" name="password" placeholder="New Password">
                  <br>
                  <label>Confirm Password</label>
                  <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                </div>
                <div class="col-md-4 form-group"></div>
                <div class="col-md-12 form-group text-center">
                  <a href="{{ url('/admin/dashboard') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                  Submit
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection