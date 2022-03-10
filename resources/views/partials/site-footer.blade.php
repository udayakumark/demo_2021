<!--====== Brand Part Start ======-->
<div class="brand-area pt-80">
    <div class="container-fluid custom-container">
        <div class="row brand-active">
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-1.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-2.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-3.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-4.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-5.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-6.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="single-brand">
                    <a href="#">
                        <img src="{{ asset('public/site-assets/images/brand/brand-4.jpg') }}" alt="brand">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--====== Brand Part Ends ======-->

<!--====== Features Banner Part Start ======-->
<section class="features-banner-area pt-80 pb-100">
    <div class="container-fluid custom-container">
        <div class="features-banner-wrapper d-flex flex-wrap">
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <img src="{{ asset('public/site-assets/images/banner-icon/icon1.png') }}" alt="Icon">
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">Free Shipping</h3>
                    <p>Free shipping on all US order</p>
                </div>
            </div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <img src="{{ asset('public/site-assets/images/banner-icon/icon2.png') }}" alt="Icon">
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">Support 24/7</h3>
                    <p>Contact us 24 hours a day</p>
                </div>
            </div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <img src="{{ asset('public/site-assets/images/banner-icon/icon3.png') }}" alt="Icon">
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">100% Money Back</h3>
                    <p>You have 30 days to Return</p>
                </div>
            </div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <img src="{{ asset('public/site-assets/images/banner-icon/icon4.png') }}" alt="Icon">
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">90 Days Return</h3>
                    <p>If goods have problems</p>
                </div>
            </div>
            <div class="single-features-banner d-flex">
                <div class="banner-icon">
                    <img src="{{ asset('public/site-assets/images/banner-icon/icon5.png') }}" alt="Icon">
                </div>
                <div class="banner-content media-body">
                    <h3 class="banner-title">Payment Secure</h3>
                    <p>We ensure secure payment</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Features Banner Part Ends ======-->


<!--====== Footer Part Start ======-->
<section class="footer-area bg_cover" style="background-image: url('{{ asset('public/site-assets/images/bg-footer.jpg') }}')">
    <div class="footer-widget pt-30 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-contact mt-50">
                        <h4 class="footer-title">Contact Details</h4>
                        <ul>
                            <li><i class="fas fa-map-marker-alt"></i>SF.No:137/1B, Kullampalayam Village, Karukkampalli,(Pachamalai Back Side Road), Gobichettipalayam-638476, Erode(Dt),TamilNadu.</li>
                            <li><i class="fas fa-phone"></i> <a href="tell:04285-222253">04285-222253</a></li>
                            <li><i class="fas fa-phone"></i> <a href="tell:9442228244">9442228244</a></li>
                            <li><i class="fas fa-phone"></i> <a href="tell:9600655556">9600655556</a></li>
                            <li><i class="fas fa-envelope"></i><a href="mailto://email@yourwebsitename.com">ricesakthi.c@gmail.com</a></li>
                            <li><i class="far fa-clock"></i> Mon-Sat 9:00am - 5:00pm Sun:Closed</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="footer-link-wrapper d-flex flex-wrap justify-content-between">
                        <div class="footer-link mt-50">
                            <h4 class="footer-title">Information</h4>
                            <ul class="link">
                                <li><a href="{{ url('rice-benefits') }}">Rice Benefits</a></li>
                                <li><a href="{{ url('rice-details') }}">More About Rice</a></li>
                                <li><a href="{{ url('gallery') }}">Gallery</a></li>
                                <li><a href="{{ url('contact-us') }}">Contact us</a></li>
                            </ul>
                        </div>
                        <div class="footer-link mt-50">
                            <h4 class="footer-title">Customer</h4>
                            <ul class="link">
                                <li><a href="{{ url('login') }}">Login</a></li>
                            </ul>
                        </div>
                        <div class="footer-link mt-50">
                            <h4 class="footer-title">About Us</h4>
                            <ul class="link">
                                <li><a href="{{ url('shop') }}">Secure Shopping </a></li>
                            </ul>
                        </div>
                        <div class="footer-link mt-50">
                            <h4 class="footer-title">My account</h4>
                            <ul class="link">
                                <li><a href="{{ url('myaccount') }}">Personal info</a></li>
                                <li><a href="{{ url('myaccount') }}">Order</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            <div class="footer-copyright-payment text-center d-lg-flex justify-content-between align-items-center">
                <div class="copyright-text">
                    <p>Copyright 2020 &copy; All rights reserved | by Kitkat Software Technologies.</p>
                </div>
                <div class="payment">
                    <img src="{{ asset('public/site-assets/images/payment.png') }}" alt="payment">
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== Footer Part Ends ======-->