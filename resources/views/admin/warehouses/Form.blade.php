@extends('layouts.admin-layout')
@section('title', $name.' Warehouse')
@section('script-src', asset('public/admin-assets/pages/manage-warehouse.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Warehouse</h4></div>
                                <div class="col-4">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger print-error-msg" style="display:none">
                                    <ul></ul>
                                </div>
                                <form method="POST" action="{{ url($action) }}" redirect-url="{{ url($redirectUrl) }}" id="form" novalidate="">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-3 form-group">
                                            <label>Place Name</label>
                                            <input type="text" name="name" placeholder="Enter Place Name" class="form-control" value="{{ $model->name }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Mobile Number</label>
                                            <input type="number" max="12" name="mobile_number" placeholder="Enter Mobile Number"  autocomplete="OFF" class="form-control" value="{{ $model->mobile_number }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Address</label>
                                            <textarea rows="4" name="address" placeholder="Enter Address" class="form-control">{{ $model->address }}</textarea>
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/warehouses') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                            <button type="submit" class="btn btn-success btn-lg" tabindex="4">
                                                {{ $name }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection