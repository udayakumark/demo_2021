    <!--====== Header Part Start ======-->
<?php 
function set_active($type) 
{
    if($type==1){
        if(request()->is('/')){
            return 'active';
        }
    }else if($type==2){
        if(request()->is('rice-benefits') || request()->is('rice-details')){
            return 'active';
        }
    }else if($type==3){
        if(request()->is('shop') || request()->is('checkout') || request()->is('cart')){
            return 'active';
        }
    }else if($type==4){
        if(request()->is('gallery')){
            return 'active';
        }
    }else if($type==5){
        if(request()->is('contact-us')){
            return 'active';
        }
    }
}
?>
    <header class="header-area header-3">
        <div class="desktop-nav d-none d-lg-block">
            <div class="header-nav">
                <input type="hidden" id="invalidAuthUrl" value="{{ url('login') }}">
                <input type="hidden" id="cartProductsUrl" value="{{ url('userCartList') }}">
                <input type="hidden" id="cartMobileProductsUrl" value="{{ url('userCartMobileList') }}">
                <input type="hidden" id="cartProductAddUrl" value="{{ url('addtoCart') }}">
                <input type="hidden" id="cartProductRemoveUrl" value="{{ url('removeFromCart') }}">
                <input type="hidden" id="priceDetails" value="{{ url('getPriceDetails') }}">
                <div class="container-fluid custom-container">
                    <div class="header-nav-wrapper d-flex justify-content-between">
                        <div class="header-static-nav">
                            <p>Welcome to Sakthi Rice</p>
                        </div>
                        <div class="header-menu-nav">
                            <ul class="menu-nav">
                                @if(Auth::guard('web')->check())
                                <li><a href="{{ url('myaccount') }}"><i class="fal fa-user"></i> My account</a></li>
                                @else
                                <li><a href="{{ url('/login') }}">Register | Login</a></li>
                                @endif
                            </ul>
                        </div> <!-- header menu nav -->
                    </div> <!-- header nav wrapper -->
                </div> <!-- container -->
            </div> <!-- header nav -->

            <div class="header-menu">
                <div class="container-fluid custom-container">
                    <div class="row">
                        <div class="col-lg-2" style="margin-top: 8px;">
                             <div class="desktop-logo">
                                    <a href="{{ url('/') }}">
                                        <img src="{{ asset('public/site-assets/images/logo.png') }}" alt="Logo" style="height: 58px;">
                                    </a>
                                </div> <!-- desktop logo -->
                        </div>
                        <div class="col-lg-8 position-static">
                            <div class="header-horizontal-menu">
                                <ul class="menu-content">
                                    <li class="{{ set_active(1) }}"><a href="{{ url('/') }}">Home</a></li>
                                    <li class="menu-item-has-children {{ set_active(2) }}"><a href="javascript:void(0)">Rice <i class="fal fa-chevron-down"></i></a>
                                        <ul class="sub-menu">
                                            <li><a href="{{ url('rice-benefits') }}">Benefits of Rice</a></li>
                                            <li><a href="{{ url('rice-details') }}">Do you know</a></li>
                                        </ul>
                                    </li>
                                    <li class="{{ set_active(3) }}"><a href="{{ url('shop') }}">Shop</a></li>
                                    <li class="{{ set_active(4) }}"><a href="{{ url('gallery') }}">Gallery</a></li>
                                    <li class="{{ set_active(5) }}"><a href="{{ url('contact-us') }}">Contact Us</a></li>
                                </ul>
                            </div> <!-- header horizontal menu -->
                        </div>
                        <div class="col-lg-2" style="margin-top: 8px;" id="headerCartDiv">

                        </div>
                    </div> <!-- row -->
                </div> <!-- container -->
            </div> <!-- header menu -->
        </div> <!-- desktop nav -->

        <div class="mobile-nav d-lg-none">
            <div class="container-fluid">
                <div class="mobile-nav-top">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-3">
                            <div class="mobile-toggle">
                                <a class="mobile-menu-open" href="javascript:;"><i class="fal fa-bars"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-4 col-5">
                            <div class="mobile-logo text-center">
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('public/site-assets/images/logo.png') }}" alt="Logo">
                                </a>
                            </div> <!-- mobile logo -->
                        </div>
                        <div class="col-sm-4  col-4">
                            <div class="mobile-account-cart">
                                <ul class="account-cart text-right">
                                    <li>
                                        @if(Auth::guard('web')->check())
                                        <a href="{{ url('myaccount') }}">
                                            <i class="fal fa-user"></i>
                                        </a>
                                        @else
                                        <a href="{{ url('login') }}">
                                            <i class="fal fa-user"></i>
                                        </a>
                                        @endif
                                    </li>
                                    <li class="header-cart" id="headerCartMobileDiv">
                                        
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div> <!-- row -->
                </div> <!-- mobile nav top -->

                <div class="header-search">
                </div>
            </div> <!-- container -->

            <div class="mobile-off-canvas-menu">
                <div class="mobile-canvas-menu-top">
                    <ul class="mobile-canvas">
                    </ul>
                </div> <!-- mobile canvas menu -->

                <div class="mobile-canvas-close close-mobile-menu">
                    <p>Menu <i class="fal fa-arrow-left"></i></p>
                </div>

                <div class="mobile-main-menu">
                    <ul class="menu-content">
                        <li class="{{ set_active(1) }}"><a href="{{ url('/') }}">Home</a></li>
                        <li class="menu-item-has-children {{ set_active(2) }}"><a href="javascript:void(0)">Rice <i class="fal fa-chevron-down"></i></a>
                            <ul class="sub-menu">
                                <li><a href="{{ url('rice-benefits') }}">Benefits of Rice</a></li>
                                <li><a href="{{ url('rice-details') }}">Do you know</a></li>
                            </ul>
                        </li>
                        <li class="{{ set_active(3) }}"><a href="{{ url('shop') }}">Shop</a></li>
                        <li class="{{ set_active(4) }}"><a href="{{ url('gallery') }}">Gallery</a></li>
                        <li class="{{ set_active(5) }}"><a href="{{ url('contact-us') }}">Contact-Us</a></li>
                    </ul>
                </div> <!-- mobile main menu -->
            </div> <!-- mobile off canvas menu -->
            <div class="overlay"></div>
        </div> <!-- mobile nav -->
    </header>

    <!--====== Header Part Ends ======-->