@extends('layouts.admin-layout')
@section('title', $name.' Other Expense')
@section('script-src', asset('public/admin-assets/pages/manage-bag.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Other Expense</h4></div>
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
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Narration</label>
                                                <textarea class="form-control" name="comments" id="comments" autocomplete="OFF"> {{ $model->comments }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="text" name="cash_amount" placeholder="Enter amount" class="form-control" value="{{ $model->total }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Bill Date</label>
                                                <input class="form-control" type="date" name="billdate" id="billdate" value="{{ $model->inv_date }}" autocomplete="OFF" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/other-expense') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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