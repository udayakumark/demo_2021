@extends('layouts.admin-layout')
@section('title', $name.' Petty Cash')
@section('script-src', asset('public/admin-assets/pages/manage-pettycash.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Petty Cash</h4></div>
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
                                            <label>Amount<span class="err-red">*</span></label>
                                            <input type="text" name="amount" placeholder="Enter amount" class="form-control" value="{{ $model->amount }}">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>Comments</label>
                                            <textarea name="comments" placeholder="Enter comments" class="form-control">{{ ($model->comments ? $model->comments : '' ) }}</textarea>
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/petty-cash') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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