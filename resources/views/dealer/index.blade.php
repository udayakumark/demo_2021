@extends('layouts.dealer-auth-layout')
@section('title', 'Login')
@section('content')
<section class="section">
  <div class="container mt-5">
    <div class="row">
      <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="card card-primary">
          <div class="card-header">
            <h4 style="text-align: center;">Dealer Login</h4>
          </div>
          <div class="card-body">
            <form method="POST" action="">
              @csrf
              <div class="form-group">
                <label for="email">User Name</label>
                <input type="text" name="user_name" placeholder="Enter UserName" class="form-control {{ $errors->has('user_name') ? 'is-invalid' : '' }}" value="{{ old('user_name') }}">
                @error('user_name')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
              </div>
              <div class="form-group">
                <div class="d-block">
                  <label for="password" class="control-label">Password</label>
                  <div class="float-right">
                    <!-- <a href="auth-forgot-password.html" class="text-small">Forgot Password?</a> -->
                  </div>
                </div>
                <input id="password" type="password" placeholder="Enter Password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" tabindex="2">
                @error('password')
                <div class="invalid-feedback">{{$message}}</div>
                @enderror
              </div>
              <!-- <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                  <label class="custom-control-label" for="remember-me">Remember Me</label>
                </div>
              </div> -->
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                Login
                </button>
              </div>
            </form>
            <!--<div class="text-center mt-4 mb-3">
              <div class="text-job text-muted">Login With Social</div>
            </div>
            <div class="row sm-gutters">
              <div class="col-6">
                <a class="btn btn-block btn-social btn-facebook">
                  <span class="fab fa-facebook"></span> Facebook
                </a>
              </div>
              <div class="col-6">
                <a class="btn btn-block btn-social btn-twitter">
                  <span class="fab fa-twitter"></span> Twitter
                </a>
              </div>
            </div>-->
          </div>
        </div>
        <!--<div class="mt-5 text-muted text-center">
          Don't have an account? <a href="auth-register.html">Create One</a>
        </div>-->
      </div>
    </div>
  </div>
</section>
@endsection