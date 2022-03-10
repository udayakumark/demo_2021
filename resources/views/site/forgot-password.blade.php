@extends('layouts.site-layout')
@section('title', 'Sakthi Rice | Forgot Password')
@section('script-src', asset('public/site-assets/pages/forgot-password.js'))
@section('content')

<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Forgot Password</li>
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
                    <h4 class="allup-title">Forgot Password</h4>
                    <form action="{{ url('forgot-password') }}" id="forgotpassword-form" method="POST">
                        @csrf
                        <div class="single-form">
                            <input type="number" name="mobile_number" placeholder="Enter registered mobile number">
                        </div>
                        <div class="single-form">
                            <button class="main-btn main-btn-2" type="submit">Send</button>
                        </div>
                    </form>
                    <p class="mt-10">Know your password ? <a href="{{ url('login') }}">Login here.</a></p>
                    <p class="mt-10">No account? <a href="{{ url('register') }}">Create one here.</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Login Part Ends ======-->

@endsection