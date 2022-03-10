@extends('layouts.admin-layout')
@section('title', $name.' Cashbook')
@section('script-src', asset('public/admin-assets/pages/manage-cashbook.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Cashbook</h4></div>
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
                                            <label>Vendor Name</label>
                                            <select class="form-control select2" name="user_id">
                                                <option value="">Select Vendor</option>
                                                @foreach ($vendorlist as $vendor)
                                                    <option value="{{ $vendor['id'] }}" >{{ $vendor['first_name']." ". $vendor['last_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Type</label>
                                            <select class="form-control select2" name="type">
                                                <option value="">Select Type</option>
                                                    <option value="CR" >CREDIT</option>
                                                    <option value="DB" >DEBIT</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Date</label>
                                            <input type="date" name="date_time" placeholder="Enter date" class="form-control"  autocomplete="OFF" value="{{ $model->date }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Amount</label>
                                            <input type="number" name="amount" placeholder="Enter amount"  autocomplete="OFF" class="form-control" value="{{ $model->amount }}">
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/cashbook') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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