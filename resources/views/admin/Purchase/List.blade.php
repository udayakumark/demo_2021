@extends('layouts.admin-layout')
@section('title', 'Manage Paddy Purchases')
@section('script-src', asset('public/admin-assets/pages/manage-purchase.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-6"><h4>Manage Paddy Purchases</h4></div>
                                <div class="col-6"><a href="{{ url('purchase/add') }}" class="btn btn-primary pull-right" data-url="{{ url('purchase/add') }}"><i class="fas fa-plus"></i>Create</a>
                                    <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Advanced searchForm -->
                                <div class="col-md-12 collapse" id="search-form">
                                    <form name="search-form" id="SearchForm" method="post" action="#">
                                        <div class="form-row">
										   <div class="col-md-4 form-group">
                                                <select class="form-control" name="purchase_source_search" tabindex="-1" aria-hidden="true">
													<option value="">Select Purchase Source</option>
													<option value="1">Own</option>
													<option value="2">Third Party</option>
													</select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="invoice_no" id="invoice_no" class="form-control" placeholder="Enter the Invoice No.">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <button type="submit" name="search" class="btn btn-success">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dealer-table" data-url="{{ url('/purchase/list') }}">
                                        <thead>
                                        <tr>
                                            <th class="text-center">#</th>
											<th>Invoice No.</th>
                                            <th>Vendor Name</th>
                                            <th>Vendor Balance</th>
                                            <th>Broker Name</th>
                                            <th>Purchase Source</th>
                                            <th>Total Amount</th>
											<th>Invoice Date</th>
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