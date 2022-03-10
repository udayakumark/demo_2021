<div class="single-product-list mt-30">
	<div class="row">
		<div class="col-sm-5 col-xl-3">
			<div class="product-image">
				<a href="{{ url('/product-detail',array('id'=>$product->id)) }}">
					<img class="first-image" src="{{ asset('public/'.$product->product_image) }}" alt="product">
					<img class="second-image" src="{{ asset('public/'.$product->product_image) }}" alt="product">
				</a>
			</div>
		</div>
		<div class="col-sm-7 col-xl-9">
			<div class="product-content product_{{ $product->id }}">
				<h4 class="product-title"><a href="{{ url('/product-detail',array('id'=>$product->id)) }}">{{ $product->product_name }} ({{ $product_prices[0]->quantity }} KG)</a></h4>
				<!-- <span class="availability">Availability: 299 In Stock</span> -->
				<div class="product-price">
					<span class="price-sale">RS. {{ $product_prices[0]->selling_price }}</span>
					<span class="regular-price">RS. {{ $product_prices[0]->original_price }}</span>
				</div>
				<p>{{ $product->product_description }}</p>
				<div class="quantity mt-15">
				<div class="row align-items-center">
					<input type="hidden" name="quantity" class="quantity" value="1">
					<div class="shop-header-select d-flex flex-wrap" style="padding-left: 20px;">
						<select name="packs" class="form-control packs">
							@foreach (App\Models\ProductPrices::where(['status'=>1,'product_id'=>$product->id])->get() as $price)
							<option value="{{ $price->id }}">{{ $price->quantity }} KG</option>
							@endforeach
						</select>
					</div>
					<div class="shop-header-select" style="padding-left: 20px;">
						@if(empty($cart_products))
						<button class="btn btn-success addcart-btn" style="font-size: 12px;"><i class="icon ion-bag"></i> Add to cart</button>
						@else
						<button class="btn btn-success addcart-btn" style="font-size: 12px;"><i class="icon ion-bag"></i> Update cart</button>
						@endif
					</div>
				</div>
			</div>

			</div>
		</div>
	</div>
</div>