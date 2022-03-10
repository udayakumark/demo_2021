@extends('layouts.admin-layout')
@section('title', $name.' Create Online Payment')
@section('script-src', asset('public/admin-assets/pages/manage-online-payment.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Manage Online Payment</h4></div>
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
                                            <label>Payment Type<span class="err-red">*</span> </label>
                                            <input type="text" name="payment_type" placeholder="enter payment type" class="form-control" value="{{ $model->payment_type }}">
                                        </div>

                                        <div class="col-md-3 form-group">
                                            <label>Mobile Number<span class="err-red">*</span></label>
                                            <input type="text" name="mobile_no" placeholder="enter mobile number" class="form-control" value="{{ $model->mobile_no }}">
                                        </div>
                                        
                                        <div class="col-md-3 form-group">
                                            <label>Select Bank<span class="err-red">*</span></label>                                                                                        
                                            <select class="form-control select2 type" name="bank_id">
                                                <option value="">Select Bank</option>
                                                @foreach ($bankList as $bank)
                                                    <option {{ ($model->bank_id == $bank['id']) ?'selected':'' }} value="{{ $bank['id'] }}" >{{ $bank['bank_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/online-payment') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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