@extends('layouts.admin-layout')
@section('title', 'Paddy Bill History')
<!-- @section('script-src', asset('public/admin-assets/pages/manage-purchase.js')) -->
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-6"><h4> Paddy Bill History</h4></div>
                                <div class="col-6">
                                <a href="{{ url($backUrl) }}" class="btn btn-warning btn-lg back pull-right"><i class="fas fa-arrow-left"></i> Back</a>
                                    <!-- <button type="button" class="btn btn-success pull-right advanced-searchbtn" data-toggle="collapse" data-target="#search-form" id="search"><i class="fas fa-search"></i> Advanced Search</button> -->
                                </div>
                            </div>
                            <div class="card-body">
                                
                                <div>
                                    <div class="col-lg-4 mb-4 f-lt">
                                        <span class="fb">Warehouse Name:</span>   @if ( count($data) > 0  ) {{ $data[0]['warehousesname'] }} @endif
                                    </div>
                                    <div class="col-lg-4 mb-4 f-lt">
                                        <span class="fb">Product Name:</span>  @if( count($data) > 0  ) {{ $data[0]['paddyname'] }} @endif
                                    </div>
                                    <table class="table table-striped" >
                                        <thead>
                                            <tr>
                                                <th class="text-center">#S.no</th>
                                                <th class="text-left">Invoice no</th>
                                                <th class="text-left">Inv Date</th>
                                                <th class="text-left">Vendor Name</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Final Price</th>
                                                <th class="text-center">Total Weight</th>
                                                <th class="text-center">Total Amount</th>
                                            </tr>
                                        </thead>
                                        
                                        <?php $qty = 0;  $price = 0; $finalprice = 0;  $total_wgt = 0;$total_amt = 0; $sno= '1';?>

                                        @if( count($data) > 0 )
                                        @foreach ($data as $datas)

                                        <tr>
                                            <td style="text-align:center">{{ $sno++}} </td>
                                            <td>{{$datas->invoice_no }} </td>
                                            <td> {{ date("d-m-Y", strtotime($datas->inv_date)) }} </td>
                                            <td>{{$datas->customer_name }} </td>                                                
                                            <td style="text-align:center">@if ( $datas->qty ) {{ $datas->qty }} @endif</td>                                             
                                            <td> ₹ {{ number_format($datas->price,2) }} </td>
                                            <td> ₹  @if( $datas->final_price){{ number_format($datas->final_price,2) }}
                                                    @else
                                                    {{ number_format($datas->price,2) }}
                                                    @endif
                                            </td>
                                            <td style="text-align:center"> {{ $datas->totalwgt }} </td>
                                            <td> ₹ {{ number_format($datas->total,2) }} </td>
                                            </tr>    
                                            <?php $qty += $datas->qty; 
                                                  $price += $datas->price;
                                                  $finalprice += ($datas->final_price? $datas->final_price : $datas->price);
                                                  $total_wgt += $datas->totalwgt;
                                                  $total_amt += $datas->total;
                                            ?>
                                        @endforeach
                                        <thead>
                                            <tr>
                                                <th class="text-right" colspan="4"> Total</th>
                                                <th class="text-center"> {{ $qty }} </th>
                                                <th class="text-center"> ₹ {{ number_format($price,2) }}</th>
                                                <th class="text-center"> ₹ {{ number_format($finalprice,2) }}</th>
                                                <th class="text-center"> {{ $total_wgt }} </th>
                                                <th class="text-center"> ₹ {{ number_format($total_amt,2) }}</th>
                                            </tr>
                                        </thead>
                                        @else if
                                        <tr>
                                            <th class="text-center" colspan="9"> No records</th>
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