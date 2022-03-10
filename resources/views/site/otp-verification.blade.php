@extends('layouts.site-layout')
@section('title', 'Otp Verification')
@section('script-src', asset('public/site-assets/pages/otp-verification.js'))
@section('content')

<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Otp Verification</li>
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
                    <h4 class="allup-title">Otp Verification</h4>
                    <p class="mt-10">Registration otp code is send to your Mobile Number end with <b>********{{ substr($data->mobile_number,8,9) }}</b></p>
                    <!-- Error Messages -->
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                    <form method="POST" action="{{ url('otp-verification/'.$key) }}" id="otp-form">
                        @csrf
                        <div class="single-form">
                            <input type="hidden" name="request_type" id="type" value="1">
                            <input type="hidden" name="key" value="{{ $key }}">
                            <input type="text" name="otp_code" placeholder="Enter your otp code">
                        </div>
                        <div class="single-form">
                            <button type="submit" onclick="$('#type').val(1)" class="main-btn main-btn-2">Verify</button>
                            <button type="submit" onclick="$('#type').val(2)" class="main-btn main-btn-2">Resend</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Register Part Ends ======-->

@endsection