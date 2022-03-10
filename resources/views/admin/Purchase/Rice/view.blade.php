@extends('layouts.admin-layout')
@section('title', $purchase_action.' '.$purchase_type.' Purchase')
@section('content')
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                          <h4 class="card-title">{{ $purchase_action}} {{ $purchase_type }} Purchase</h4>
                                    </div>
                                    <div class="col-lg-6 mb-4" style="text-align: right">

                                    </div>
                                </div>
								<div class="row">
								 <div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Purchase Date</strong></td>
										<td>{{ $inv_details['inv_date'] }}</td>
									</tr>
								</tbody>
								</table>
								</div>
								<div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Warehouse</strong></td>
										<td>{{ $warehouseList }}</td>
									</tr>
								</tbody>
								</table>
								</div>
								</div>
								<div class="row clearfix">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover" id="tab_logic">
                                                <thead>
                                                 <tr>
                                                    <th class="text-center"> # </th>
                                                    <th class="text-center"> Item name</th>
                                                    <th class="text-center"> Quantity </th>
                                                </tr>
                                                </thead>
                                                <tbody id='addr'>
												<?php $i=0; ?>
                                                @foreach ($salesitems as $vendor)
                                                 <?php $i++; ?>
                                                    <tr id='addr<?php echo $i ?>' >
                                                    <td class="text-center"><?php echo $i ?></td>
                                                    <td class="text-center">@foreach ($riceList as $rice)
													{{ $rice['id'] ==$vendor['paddy_id'] ? $rice['name'] : '' }}
                                                         @endforeach</td>
                                                    <td class="text-center"><?php echo $vendor['qty']; ?></td>
                                                </tr>
                                                @endforeach
                                                <input type="hidden" name="serialno" value="<?php echo $i ?>" id="serialno">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
									<div class="row">
									<div class="col-md-6">
										<table class="table">
										<tbody>
											<tr>
												<td><strong>Narration</strong></td>
												<td>{{ $inv_details['narration'] }}</td>
											</tr>
										</tbody>
										</table>
								</div>
								<div class="col-md-12 form-group text-center">
									<a href="{{ url($redirectUrl) }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                 </div>	
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="formModal" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <script> 
@endsection


