@extends('layouts.admin-layout')
@section('title', 'Accessory manual stock entry')
@section('script-src', asset('public/admin-assets/pages/manage-accessory-manual-stock-entry.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-8"><h4>Manage Accessory Manual Stock</h4></div>
                                <div class="col-4">
                                    
                                    <a href="{{ url('stock/create-accessory-manual') }}" class="btn btn-primary pull-right" style="margin-left: 10px;"><i class="fas fa-plus"></i> Create</a> 
                                    <a href="{{url('/stock/accessoriesstocklist') }}" class="btn btn-warning btn-lg back pull-right">Accessory Stock List <i class="fas fa-eye"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                
                                <div class="table-responsive">
                                    <?php
                                        // print_r($model);
                                    ?>
                                    <table class="table table-striped" id="dealer-table" data-url="{{ url('/stock/accessory-manual-list') }}">
                                        <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Accessories Name</th>
                                            <th>Quentity</th>
                                            <th>Comments</th>
                                            <th>Date</th>
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