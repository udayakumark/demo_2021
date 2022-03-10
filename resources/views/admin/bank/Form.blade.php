@extends('layouts.admin-layout')
@section('title', $name.' Create Bank')
@section('script-src', asset('public/admin-assets/pages/manage-bank.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Manage Bank</h4></div>
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
                                            <label>Bank Name<span class="err-red">*</span> </label>
                                            <input type="text" name="bank_name" placeholder="enter bank name" class="form-control" value="{{ $model->bank_name }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Account Name<span class="err-red">*</span></label>
                                            <input type="text" name="account_name" placeholder="enter account name" class="form-control" value="{{ $model->account_name }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Account Number<span class="err-red">*</span></label>
                                            <input type="text" name="account_no" placeholder="enter account number" class="form-control" value="{{ $model->account_no }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>IFSC Code<span class="err-red">*</span></label>
                                            <input type="text" name="ifsc" placeholder="enter ifsc code" class="form-control" value="{{ $model->ifsc }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Branch<span class="err-red">*</span></label>
                                            <input type="text" name="branch" placeholder="enter branch " class="form-control" value="{{ $model->branch }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Address</label>
                                            <input type="text" name="bank_address" placeholder="enter bank address" class="form-control" value="{{ $model->bank_address }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Account Type<span class="err-red">*</span></label>                                            
                                            <select class="form-control select2 type" name="type">
                                                <option value="">Select account type</option>
                                                <option {{ ($model->type == 'saving') ?'selected':'' }} value="saving">Saving Account</option>
                                                <option {{ ($model->type == 'current') ?'selected':'' }} value="current">Current Account</option>                                                   
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Current Balance<span class="err-red">*</span></label>
                                            <input type="text" name="current_balance" placeholder="enter current balance" class="form-control" value="{{ $model->current_balance }}">
                                        </div>

                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/bank') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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