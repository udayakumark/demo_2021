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
										<td><strong>Purchase Source</strong></td>
										<td>{{ $inv_details['purchase_source']==1 ? 'Own' : 'Third Party' }}</td>
									</tr>
									<tr>
										<td><strong>Invoice No</strong></td>
										<td>{{ $inv_details['invoice_no'] }}</td>
									</tr>
									<tr>
										<td><strong>Purchase Date</strong></td>
										<td>{{ $inv_details['inv_date'] }}</td>
									</tr>
									<tr>
										<td><strong>Vendor Name</strong></td>
										<td>{{ $vendor_details['first_name']." ". $vendor_details['last_name'] }}</td>
									</tr>
									<tr>
										<td><strong>Current Balance</strong></td>
										<td>{{ $vendor_details['current_balance'] }}</td>
									</tr>
								</tbody>
								</table>
								</div>
								<div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Address</strong></td>
										<td>{{ $vendor_details['address'] }}</td>
									</tr>
									<tr class="purchase_source_vendor">
										<td><strong>Broker</strong></td>
										<td>{{ $brokerlist }}</td>
									</tr>
									<tr>
										<td><strong>Warehouse</strong></td>
										<td>{{ $warehouseList }}</td>
									</tr>
									<tr>
										<td><strong>Vehicle No.</strong></td>
										<td>{{ $inv_details['veh_no'] }}</td>
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
                                                    <th class="text-center"> Weight(Kgs) </th>
                                                    <th class="text-center"> No.of Bags(Qty) </th>
													<th class="text-center"> Total Weight(Kgs)</th>
                                                    <th class="text-center purchase_source_vendor"> Rate</th>
                                                    <th class="text-center purchase_source_vendor"> Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody id='addr'>
												<?php $i=0; ?>
                                                @foreach ($salesitems as $vendor)
                                                 <?php $i++; ?>
                                                    <tr id='addr<?php echo $i ?>' >
                                                    <td class="text-center"><?php echo $i ?></td>
													<td class="text-center">@foreach ($productList as $category)
                                                      {{ $category->id ==$vendor['paddy_id'] ? $category->name : '' }}
                                                     @endforeach</td>
                                                    <td class="text-center"><?php echo $vendor['wgt']; ?></td>
                                                    <td class="text-center"><?php echo $vendor['qty']; ?></td>
                                                    <td class="text-center"><?php echo $vendor['totalwgt']; ?></td>
													<td class="text-center purchase_source_vendor_items"><?php echo $vendor['rate']; ?></td>
                                                    <td class="text-center purchase_source_vendor_items"><?php echo $vendor['total']; ?></td>
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
											<tr>
												<td><strong>Weigh 1st</strong></td>
												<td>{{ $inv_details['weigh1'] }}</td>
											</tr>
											<tr>
												<td><strong>Weigh 2nd</strong></td>
												<td>{{ $inv_details['weigh2'] }}</td>
											</tr>
											<tr>
												<td><strong>Weigh 3rd</strong></td>
												<td>{{ $inv_details['weigh3'] }}</td>
											</tr>
										</tbody>
										</table>
								</div>
								<div class="col-md-6">
										<table class="table">
										<tbody>
											<tr class="purchase_source_vendor">
												<td><strong>Broker Commission</strong></td>
												<td>{{ $inv_details['bro_comm'] }}</td>
											</tr>
											<tr class="purchase_source_vendor">
												<td><strong>Broker Commission Total</strong></td>
												<td>{{ number_format($inv_details['bro_comm_total']) }}</td>
											</tr>
											<tr class="purchase_source_vendor">
												<td><strong>Sub Total</strong></td>
												<td>{{ number_format($inv_details['sub_total']) }}</td>
											</tr>
											<tr class="purchase_source_vendor">
												<td><strong>Grand Total</strong></td>
												<td>{{ number_format($inv_details['total']) }}</td>
											</tr>
										</tbody>
										</table>
								</div>
								<div class="col-md-12 form-group text-center">
									<a href="{{ url('/admin/purchase') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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
	<?php if($inv_details['purchase_source']==1) { ?>			
				$(".purchase_source_vendor_items").show();
				$(".purchase_source_vendor").show();
			<?php } ?>
			
			<?php if($inv_details['purchase_source']==2) { ?>			
				$(".purchase_source_vendor_items").hide();
				$(".purchase_source_vendor").hide();
			<?php } ?>
	</script>
@endsection


