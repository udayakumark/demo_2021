@extends('layouts.admin-auth-layout')
@section('title', 'Login')
@section('script-src', asset('public/admin-assets/js/forgot-password.js'))
@section('script-src', asset('public/admin-assets/pages/forgot-password.js'))
@section('script-src', asset('public/site-assets/pages/forgot-password.js'))
@section('content')

<section class="section">
  <div class="container mt-5">
    <div class="row">
      <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="card card-primary">
            <div class="card-header">
                <h4 style="text-align: center;">Forgot Password</h4>
            </div>
            @include('partials.alert-message')
            <div class="alert alert-danger print-error-msg" style="display:none">
                <ul></ul>
            </div>
          <div class="card-body">
                <form action="{{ url('admin/forgot-pwd') }}" id="forgotpassword-form" method="POST">
                    @csrf                   

                    <div class="form-group">
                            <input type="number" name="mobile_number" maxlength="9" class="form-control {{ $errors->has('mobile_number') ? 'is-invalid' : '' }}" placeholder="Enter registered mobile number">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" >Send</button>
                    </div>                    
                </form>
                <p class="mt-10">Know your password ? <a href="{{ url('admin') }}">Login here.</a></p>

            <!-- <form method="POST" action="">
              @csrf
              <div class="form-group">
                <label for="email">User Name</label>
                <input type="text" name="user_name" placeholder="Enter UserName" class="form-control {{ $errors->has('user_name') ? 'is-invalid' : '' }}" value="{{ old('user_name') }}" tabindex="1" autofocus>
                @error('user_name')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
              </div>
              <div class="form-group">
                <div class="d-block">
                  <label for="password" class="control-label">Password</label>
                  <div class="float-right">
                    <a href="{{'admin/forgot-pwd'}}" class="text-small">Forgot Password?</a>
                  </div>
                </div>
                <input id="password" type="password" placeholder="Enter Password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" tabindex="2">
                @error('password')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
              </div>
              
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                Login
                </button>
              </div>
              <div class="form-group">
                <div class="mt-5 text-muted text-center">
                  Lost your password? <a href="{{'admin/forgot-pwd'}}">Click here</a>
                </div>
              </div>
            </form> -->
            
          </div>
        </div>
       
      </div>
    </div>
  </div>
</section>


@endsection