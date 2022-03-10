@extends('layouts.admin-layout')
@section('title', $bill_action.' '.$bill_type.' Bill')
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
                                          <h4 class="card-title">{{ $bill_action}} {{ $bill_type }} Bill</h4>
                                    </div>
                                    <div class="col-lg-6 mb-4" style="text-align: right">

                                    </div>
                                </div>
								<div class="row">
								 <div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Invoice Type</strong></td>
										<td>{{ $inv_details['bill_source']==3 ? 'Manual' : 'Automatic' }}</td>
									</tr>
									<tr>
										<td><strong>Invoice No</strong></td>
										<td>{{ $inv_details['invoice_no'] }}</td>
									</tr>
									<tr>
										<td><strong>Bill Date</strong></td>
										<td>{{ $inv_details['inv_date'] }}</td>
									</tr>
									<tr>
										<td><strong>Vendor Name</strong></td>
										<td>{{ $vendor_details['first_name']." ". $vendor_details['last_name'] }}</td>
									</tr>
								</tbody>
								</table>
								</div>
								<div class="col-md-6">
								<table class="table">
								<tbody>
									<tr>
										<td><strong>Current Balance</strong></td>
										<td>{{ $vendor_details['current_balance'] }}</td>
									</tr>
									<tr>
										<td><strong>Address</strong></td>
										<td>{{ $vendor_details['address'] }}</td>
									</tr>
									<tr>
										<td><strong>Warehouse</strong></td>
										<td>{{ $warehouseList }}</td>
									</tr>
									<tr>
										<td><strong>HSN</strong></td>
										<td>{{ $inv_details['hsn']  }}</td>
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
                                                    <th class="text-center"> Item name </th>
                                                    <th class="text-center"> No.of Bags(Qty) </th>
                                                    <th class="text-center"> Price </th>
												    <th class="text-center"> Final Price</th>	
                                                    <th class="text-center"> Amount</th>
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
													<td class="text-center"><?php echo $vendor['price']; ?></td>
													<td class="text-center"><?php echo $vendor['final_price']; ?></td>
                                                    <td class="text-center"><?php echo $vendor['total']; ?></td>
                                                </tr>
                                                @endforeach
                                                <input type="hidden" name="serialno" value="<?php echo $i ?>" id="serialno">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
									<div class="row">
										<div class="col-md-4">
										<table class="table">
										<tbody>
											<tr class="bill_source_vendor">
												<td><strong>Sub Total</strong></td>
												<td>{{ number_format($inv_details['sub_total']) }}</td>
											</tr>
											<tr class="bill_source_vendor">
												<td><strong>Discount</strong></td>
												<td>{{ number_format($inv_details['discount']) }}</td>
											</tr>
											<tr class="bill_source_vendor">
												<td><strong>Grand Total</strong></td>
												<td>{{ number_format($inv_details['total']) }}</td>
											</tr>
										</tbody>
										</table>
								</div>
								<div class="col-md-4">
										<?php 
                                            $payment_mode='';
                                            if($inv_details['payment_type'] == 1) {
                                               $payment_mode = "Bank";
                                            }else if($inv_details['payment_type'] == 2) {
                                                $payment_mode = "Online Pay ";
                                            }else{
                                                $payment_mode = "Cash"; 
                                            }
                                         ?> 
										<table class="table">
										<tbody>
											<tr class="bill_source_vendor">
												<td><strong>Payment Mode</strong></td>
												<td>{{ $payment_mode }}</td>
											</tr>
											<tr class="bill_source_vendor">
												<td><strong>Cash</strong></td>
												<td>{{ number_format($bill_payment['cash_amount']) }}</td>
											</tr>
											<tr class="bill_source_vendor">
												<td><strong>Credit</strong></td>
												<td>{{ number_format($bill_payment['credit_amount']) }}</td>
											</tr>
										</tbody>
										</table>
								</div>
								<div class="col-md-4">
										<?php 
                                            $payment_type='';
                                            if($inv_details['payment_type'] == 1) {
                                               $payment_type = $banks;
                                            }else if($inv_details['payment_type'] == 2) {
                                                 $payment_type = $online_payment;
                                            } else{
                                                $payment_type = "Cash"; 
                                            }
                                         ?> 
										<table class="table">
										<tbody>
											<tr class="bill_source_vendor">
												<td><strong>Payment Source</strong></td>
												<td>{{ $payment_type }}</td>
											</tr>
											<tr class="bill_source_vendor">
												<td><strong>Comments</strong></td>
												<td>{{ $inv_details['comments'] }}</td>
											</tr>
										</tbody>
										</table>
								</div>
								<div class="col-md-12 form-group text-center">
									<a href="{{ $redirectUrl }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
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
	<?php if($inv_details['bill_source']==1) { ?>			
				$(".bill_source_vendor_items").show();
				$(".bill_source_vendor").show();
			<?php } ?>
			
			<?php if($inv_details['bill_source']==2) { ?>			
				$(".bill_source_vendor_items").hide();
				$(".bill_source_vendor").hide();
			<?php } ?>
	</script>
@endsection


