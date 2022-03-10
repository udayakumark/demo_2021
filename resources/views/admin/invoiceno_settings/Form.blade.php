@extends('layouts.admin-layout')
@section('title', $name.' Invoice No. Settings')
@section('script-src', asset('public/admin-assets/pages/manage-VendorType.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Invoice No. Settings</h4></div>
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
										@foreach($model as $moedelItems)
										<div class="col-md-4 form-group">
                                            <label>{{ $moedelItems['name'] }} Invoce No.</label>
                                            <input type="text" name="invoice_no_{{ strtolower($moedelItems['name']) }}" placeholder="Enter {{ $moedelItems['name'] }} Invoice No." class="form-control" value="{{ $moedelItems['invoice_no'] }}">
                                        </div>
                                        @endforeach
                                        <div class="col-md-12 form-group">
                                            <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
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