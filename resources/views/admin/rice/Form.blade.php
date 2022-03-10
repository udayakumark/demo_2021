@extends('layouts.admin-layout')
@section('title', $name.' Rice Type')
@section('script-src', asset('public/admin-assets/pages/manage-rice.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Rice Type</h4></div>
                                <div class="col-4">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger print-error-msg" style="display:none">
                                    <ul></ul>
                                </div>
								@if($type==3)     
								<div class="col-lg-6 col-sm-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Original name</strong></td>
										<td>{{ $model->original_name }}</td>
									</tr>
									<tr>
										<td><strong>Bag type</strong></td>
										<td>{{ $bag_type }}</td>
									</tr>
									<tr>
										<td><strong>Duplicate name</strong></td>
										<td>{{ $model->duplicate_name }}</td>
									</tr>
									<tr>
										<td><strong>Dealer's price</strong></td>
										<td>{{ $model->dealers_price }}</td>
									</tr>
									<tr>
										<td><strong>Customer's price</strong></td>
										<td>{{ $model->customers_price }}</td>
									</tr>
									<tr>
										<td><strong>Online sales price</strong></td>
										<td>{{ $model->onlinesales_price }}</td>
									</tr>
								</tbody>
								</table>	
								<div class="col-md-12 form-group text-center">
									<a href="{{ url('/admin/rice') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                 </div>								
								@else
                                <form method="POST" action="{{ url($action) }}" redirect-url="{{ url($redirectUrl) }}" id="form" novalidate="">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-3 form-group">
                                            <label>Original name</label>
                                            <input type="text" name="original_name" placeholder="Enter Rice Original Name" class="form-control" value="{{ $model->original_name }}">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Choose Bag(Kg)</label>
                                           <select class="form-control select2 type" name="bag_type">
                                                <option value="">Select Type</option>
                                                @foreach ($BagTypesList as $Bag)
                                                    <option value="{{ $Bag['id'] }}" {{ ( $Bag['id'] == $bag_type) ? 'selected' : '' }} >{{ $Bag['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Duplicate name</label>
                                            <input type="text" name="duplicate_name" placeholder="Enter Rice Duplicate Name" class="form-control" value="{{ $model->duplicate_name }}">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Dealer's price</label>
                                            <input type="text" name="dealers_price" placeholder="Enter Dealer's Price" class="form-control" value="{{ $model->dealers_price }}">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Customer's price</label>
                                            <input type="text" name="customers_price" placeholder="Enter Customer's Price" class="form-control" value="{{ $model->customers_price }}">
                                        </div>
										<div class="col-md-3 form-group">
                                            <label>Online sales price</label>
                                            <input type="text" name="onlinesales_price" placeholder="Enter Online Sales Price" class="form-control" value="{{ $model->onlinesales_price }}">
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/rice') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                            <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                                                {{ $name }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
								@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection