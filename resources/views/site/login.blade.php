@extends('layouts.site-layout')
@section('title', 'Login')
@section('script-src', asset('public/site-assets/pages/login.js'))
@section('content')

<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Login</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->

<!--====== Login Part Start ======-->
<section class="login-area pt-10">
    <div class="container-fluid custom-container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                @include('partials.alert-message')
                <div class="login-register-wrapper mt-30">
                    <!-- Error Messages -->
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                    <h4 class="allup-title">Login to Your Account</h4>
                    <form action="{{ url('login') }}" id="login-form" method="POST">
                        @csrf
                        <div class="single-form">
                            <input type="text" name="user_name" placeholder="Username">
                        </div>
                        <div class="single-form">
                            <input type="password" name="password" placeholder="Password">
                        </div>
                        <div class="single-form">
                            <button class="main-btn main-btn-2" type="submit">Login</button>
                        </div>
                    </form>
                    <p class="mt-20"><a href="{{ url('forgot-password') }}">Lost your password?</a></p>
                    <p class="mt-10">No account? <a href="{{ url('register') }}">Create one here.</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Login Part Ends ======-->

@endsection