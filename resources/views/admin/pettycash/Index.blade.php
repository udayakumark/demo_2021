@extends('layouts.admin-layout')
@section('title', 'Petty Cash')
@section('script-src', asset('public/admin-assets/pages/manage-pettycash.js'))
@section('content')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>Manage Petty Cash</h4></div>
                                <div class="col-4">
                                    <a href="{{ url('admin/create-petty-cash') }}" class="btn btn-primary pull-right"><i class="fas fa-plus"></i> Create</a>
                                    <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Advanced searchForm -->
                                <div class="col-md-12 collapse" id="search-form">
                                    <form name="search-form" id="SearchForm" method="post" action="#">
                                        <div class="form-row">
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="name" id="name" class="form-control" placeholder="Search comments">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <button type="submit" name="search" class="btn btn-success">Search</button>
                                                <a href="{{ url('/admin/petty-cash') }}" class="btn btn-danger btn-small back"><i class="fas fa-refresh"></i> Remove</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dealer-table" data-url="{{ url('/admin/petty-cash-list') }}">
                                        <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Amount</th>
                                            <th>Comments</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection