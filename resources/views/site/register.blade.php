@extends('layouts.site-layout')
@section('title', 'Register')
@section('script-src', asset('public/site-assets/pages/register.js'))
@section('content')
<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Register</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->
<!--====== Register Part Start ======-->
<section class="register-area pt-10">
    <div class="container-fluid custom-container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="login-register-wrapper mt-30">
                    <h4 class="allup-title">Creat New Account</h4>
                    <p class="mt-10">Already have an account? <a href="{{ url('login') }}">Log in instead!</a></p>
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul
                        ></ul>
                    </div>
                    <form method="POST" action="{{ url('register') }}" id="register-form">
                        @csrf
                        <div class="single-form">
                            <input type="text" name="first_name" placeholder="First Name">
                        </div>
                        <div class="single-form">
                            <input type="text" name="last_name" placeholder="Last Name">
                        </div>
                        <div class="single-form">
                            <input type="text" name="email_id" placeholder="Email Address">
                        </div>
                        <div class="single-form">
                            <input type="text" name="mobile_number" placeholder="Mobile Number">
                        </div>
                        <div class="single-form">
                            <input type="text" name="user_name" placeholder="Username">
                        </div>
                        <div class="single-form">
                            <input type="password" name="password" placeholder="Password">
                        </div>
                        <div class="single-form">
                            <input type="password" name="password_confirmation" placeholder="Confirm Password">
                        </div>
                        <div class="single-form">
                            <input type="text" name="pincode" placeholder="Pin Code">
                        </div>
                        <div class="single-form">
                            <textarea name="address" placeholder="Address"></textarea>
                        </div>
                        <div class="single-form">
                            <button type="submit" class="main-btn main-btn-2">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Register Part Ends ======-->
@endsection