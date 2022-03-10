<div class="col-xl-3 col-md-4 col-sm-6">
	<div class="single-product mt-30">
		<div class="product-image">
			<a href="{{ url('/product-detail',array('id'=>$product->id)) }}">
				<img class="first-image" style="height: 298px;" src="{{ asset('public/'.$product->product_image) }}" alt="product">
				<img class="second-image" style="height: 298px;" src="{{ asset('public/'.$product->product_image) }}" alt="product">
			</a>
		</div>
		<div class="product-content product_{{ $product->id }}">
			<p class="datas"></p>
			<h4 class="product-title"><a href="{{ url('/product-detail',array('id'=>$product->id)) }}">{{ $product->product_name }} ({{ $product_prices[0]->quantity }} KG)</a></h4>
			<div class="product-price">
				<span class="price-sale">RS. {{ $product_prices[0]->selling_price }}</span>
				<span class="regular-price">RS. {{ $product_prices[0]->original_price }}</span>
			</div>
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