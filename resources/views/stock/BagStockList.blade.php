@extends('layouts.admin-layout')
@section('title', 'Bag Stock List')
@section('script-src', asset('public/admin-assets/pages/manage-purchase.js'))
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-6"><h4> Bag Stock</h4></div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Advanced searchForm -->
                                <div class="col-md-12 collapse show" id="search-form">
                                
                                <form method="POST" action="{{ url($action) }}" redirect-url="{{ url($redirectUrl) }}" id="form" novalidate="">
                                    @csrf   
                                        <div class="form-row">
										   <div class="col-md-3 form-group">
                                                <select class="form-control" name="warehouse_search" tabindex="-1" aria-hidden="true" required>
													<option value="all">All Warehouse</option>
                                                    @foreach ($warehouseList as $warehouse)
                                                    <option {{ ($ware_house == $warehouse['id']) ?'selected':'' }} value="{{ $warehouse['id'] }}" >{{ $warehouse['name'] }}</option>
                                                    @endforeach													
												</select>
                                            </div>
										   <!-- <div class="col-md-3 form-group">
                                                <select class="form-control" name="paddy_item_search" tabindex="-2" aria-hidden="true">
													<option value="0"> All Paddy Item </option>
                                                    @foreach ($paddyItems as $paddyItem)
                                                    <option {{ ($paddy_item == $paddyItem['id']) ?'selected':'' }} value="{{ $paddyItem['id'] }}" >{{ $paddyItem['name'] }}</option>
                                                    @endforeach													
												</select>
                                            </div> -->
                                            
                                            <div class="col-md-3 form-group">
                                                <button type="submit" name="search" class="btn btn-success">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div>
                                    <table class="table table-striped" >
                                        <thead>
                                            <tr>
                                                <th class="text-center">#S.no</th>
                                                <th class="text-left">Warehouse</th>
                                                <th class="text-left">Bag Type</th>
                                                <th class="text-left">Bag Name</th>
                                                <th class="text-left">Vendor Name</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Rate</th>
                                                <th class="text-center">Total Amount</th>
                                            </tr>
                                        </thead>
                                        
                                        <?php $qty = 0;  $rate = 0;  $total_amt = 0; $sno= '1';?>

                                        @if( count($data) > 0 )
                                        @foreach ($data as $datas)

                                        <tr>
                                            <td style="text-align:center">{{ $sno++}} </td>
                                            <td>{{$datas->warehousesname }} </td>
                                             
                                                 
                                            <td style="text-align:left">
                                            @if ( $datas->bag_name ) 
                                                Rice
                                            @else 
                                              Paddy 
                                            @endif  
                                            </td>
                                            <td style="text-align:left">
                                            @if ( $datas->bag_name ) 
                                                {{ $datas->bag_name}}  {{ $datas->kg}}
                                            @else 
                                              Paddy Bag   
                                            @endif  
                                            </td>
                                                

                                                <td style="text-align:left">
                                                @if ( $datas->vendor_name )
                                                {{ $datas->vendor_name}} 
                                                @endif

                                                </td>

                                                
                                                <td style="text-align:center">@if ( $datas->qty ) {{ $datas->qty }} @endif</td>
                                               
                                                <td style="text-align:center"> @if ( $datas->rate ) {{ $datas->rate }} @endif</td>                                              
                                                
                                                 
                                                <td style="text-align:center">@if ( $datas->total_amt ) ₹ {{ number_format($datas->total_amt,2) }} @endif</td>
                                               
                                            </tr>    
                                            <?php $qty += $datas->qty; 
                                                  $rate += $datas->rate;
                                                  $total_amt += $datas->total_amt;
                                            ?>
                                        @endforeach
                                        <thead>
                                            <tr>
                                                <th class="text-right" colspan="5"> Total</th>
                                                <th class="text-center"> {{ $qty }} </th>
                                                <th class="text-center"> {{ $rate}} </th>
                                                <th class="text-center"> ₹ {{ number_format($total_amt,2) }}</th>
                                            </tr>
                                        </thead>
                                        @else if
                                        <tr>
                                            <th class="text-center" colspan="8"> No records</th>
                                        </tr>
                                        @endif
                                    </table>
                               </div>
                               <div>                           
                                    <!-- @if( $sql_query )
                                        {{$sql_query}}
                                    @endif -->
                                <div>

                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection