@extends('layouts.admin-layout')
@section('title', $purchase_action.' '.$purchase_type.' Purchase')
@section('script-src', asset('public/admin-assets/pages/manage-ricepurchase.js'))
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
                                <form id="rice_purchase_form" action="{{ url($actionUrl) }}" redirect-url="{{ url($redirectUrl) }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Purchase Date</label>
                                                <input class="form-control" type="date" name="billdate" id="billdate" value="<?php echo $inv_details['inv_date']; ?>" autocomplete="OFF" required="" required>
                                            </div>
                                        </div>
										<div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Warehouse</label>
                                                <select class="form-control select2" name="warehouse">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach ($warehouseList as $vendor)
                                                        <option value="{{ $vendor['id'] }}" {{ $vendor['id']==$inv_details['warehouse_id'] ? 'selected' : '' }} >{{ $vendor['name']." ". $vendor['mobile_number'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover  table-responsive" id="tab_logic">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="20%"> # </th>
                                                    <th class="text-center" width="40%"> Item name</th>
                                                    <th class="text-center" width="40%"> Quantity </th>
                                                </tr>
                                                </thead>
                                                <tbody id='addr'>
												<?php $i=0; ?>
                                                @foreach ($salesitems as $vendor)
                                                 <?php $i++; ?>
                                                    <tr id='addr<?php echo $i ?>' >
                                                        <td><?php echo $i ?></td>
                                                        <td width="50%"><select class="form-control select2" name="product_id[]" id="product_name<?php echo $i ?>">
														@foreach ($riceList as $rice)
                                                                    <option value="{{ $rice['id'] }}" {{ $rice['id'] ==$vendor['paddy_id'] ? 'selected' : '' }} >{{ $rice['name'] }}</option>
                                                         @endforeach
                                                    </td>
                                                     <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required  value="<?php echo $vendor['qty']; ?>"/></td>
                                                </tr>
                                                @endforeach
                                                <input type="hidden" name="serialno" value="<?php echo $i ?>" id="serialno">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row clearfix" style="margin-top:20px">
                                        <div class="pull-right col-md-12">
                                            <div class="form-group">
                                                <label for="username">Narration:</label>
                                                <textarea class="form-control" type="text" name="narration" id="narration"  autocomplete="OFF"><?php echo $inv_details['narration']; ?></textarea>
                                            </div>
                                        </div>
									</div>	
                                    <div class="row clearfix" style="margin-top:20px">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">
										    <a href="{{ url($redirectUrl) }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                            <button type="submit" id="submit" class="btn btn-primary btn-lg pull-center">Submit</button>
                                        </div>
                                        <div class="col-md-4">
										
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
@endsection