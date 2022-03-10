@extends('layouts.site-layout')
@section('title', 'Sakthi Rice | ContactUs')
@section('script-src', asset('public/site-assets/pages/contact-us.js'))
@section('content')

<!--====== Breadcrumb Part Start ======-->
<div class="breadcrumb-area">
    <div class="container-fluid custom-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Contact Us</li>
            </ol>
        </nav>
    </div>
</div>
<!--====== Breadcrumb Part Ends ======-->
<!--====== Contact PART START ======-->
<div class="contact-map-area pt-50">
    <div id="contact-map"></div>
</div>
<!--====== Contact PART ENDS ======-->
<!--====== Contact Part Start ======-->
<section class="contact-page pt-20">
    <div class="container-fluid custom-container">
        <div class="row">
            <div class="col-lg-7 col-xl-8">
                <!-- Error Messages -->
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <div class="contact-form mt-30">
                    <h4 class="allup-title">Billing Address</h4>
                    <form action="{{ url('contactus') }}" id="contactus-form" method="POST">
                    @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="single-form">
                                    <input type="text" name="name" placeholder="Your Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="single-form">
                                    <input type="email" name="email" placeholder="Your Email">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="single-form">
                                    <input type="text" name="subject" placeholder="Subject">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="single-form">
                                    <textarea name="message" placeholder="Your Message"></textarea>
                                </div>
                            </div>
                            <p class="form-message"></p>
                            <div class="col-md-12">
                                <div class="single-form">
                                    <button class="main-btn main-btn-2" type="submit">Send Message</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 col-xl-4">
                <div class="contact-info mt-30">
                    <h4 class="title">Contact Us</h4>
                    <p class="text">24/7 Customer care service is available.</p>
                    <ul class="contact-info-block">
                        <li>
                            <div class="single-info">
                                <div class="info-icon-title d-flex align-item-center">
                                    <div class="info-icon">
                                        <i class="icon ion-map"></i>
                                    </div>
                                    <h5 class="info-title media-body">Address</h5>
                                </div>
                                <p>SF.No:137/1B, Kullampalayam Village, Karukkampalli,(Pachamalai Back Side Road), Gobichettipalayam-638476, Erode(Dt),TamilNadu.</p>
                            </div>
                        </li>
                        <li>
                            <div class="single-info">
                                <div class="info-icon-title d-flex align-item-center">
                                    <div class="info-icon">
                                        <i class="icon ion-ios-telephone-outline"></i>
                                    </div>
                                    <h5 class="info-title media-body">Phone</h5>
                                </div>
                                <p>Mobile: 9442228244, 9600655556</p>
                                <p>Hotline: 04285-222253</p>
                            </div>
                        </li>
                        <li>
                            <div class="single-info">
                                <div class="info-icon-title d-flex align-item-center">
                                    <div class="info-icon">
                                        <i class="icon ion-ios-email-outline"></i>
                                    </div>
                                    <h5 class="info-title media-body">Email</h5>
                                </div>
                                <p>ricesakthi.c@gmail.com</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Contact Part Ends ======-->
@endsection