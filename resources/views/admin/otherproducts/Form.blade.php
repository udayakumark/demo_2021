@extends('layouts.admin-layout')
@section('title', $name.' Other Products Types')
@section('script-src', asset('public/admin-assets/pages/manage-otherproducts.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>{{ $name }} Other Products Types</h4></div>
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
                                            <label>Name</label>
                                            <input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{ $model->name }}">
                                        </div>
                                       
										<input type="hidden" name="type" placeholder="" class="form-control" value="1">
                                        <div class="col-md-12 form-group text-center">
                                            <a href="{{ url('/admin/otherproducts') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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