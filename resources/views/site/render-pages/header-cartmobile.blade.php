@if(!empty($cartList))
<a href="javascript:void(0)">
    <i class="fal fa-shopping-cart"></i>
    <span>{{ count($cartList) }}</span>
</a>
<div class="mini-cart">
    <ul class="cart-items">
        @foreach ($cartList as $cart)
        <li style="width: 100%;">
            <div class="single-cart-item d-flex">
                <div class="cart-item-thumb">
                    <a href="{{ $cart['product_url'] }}"><img src="{{ asset('public/'.$cart['product_image']) }}" alt="product"></a>
                    <span class="product-quantity">{{ $cart['quantity'] }}</span>
                </div>
                <div class="cart-item-content media-body">
                    <h5 class="product-name"><a href="{{ $cart['product_url'] }}">{{ $cart['product_name'] }}  ({{ $cart['product_size'] }} KG)</a></h5>
                    <span class="product-price">{{ $cart['product_price'] }}</span>
                    <a href="javascript:void(0)" cart-id="{{ $cart['cart_id'] }}" class="product-close removecart-btn"><i class="fal fa-times"></i></a>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
    <div class="price_content">
        <div class="cart-total price_inline">
            <span class="label">Total</span>
            <span class="value">Rs. {{ $cartPrice }}</span>
        </div>
        </div> <!-- price content -->
        <div class="checkout text-center">
            <a href="{{ url('cart') }}" class="main-btn">View Cart</a>
        </div>
        <div class="checkout text-center">
            <a href="{{ url('checkout') }}" class="main-btn">Checkout</a>
        </div>
    </div>
</div>
@else
<a href="{{ url('cart') }}">
    <i class="fal fa-shopping-cart"></i>
    <span>0</span>
</a>
@endif