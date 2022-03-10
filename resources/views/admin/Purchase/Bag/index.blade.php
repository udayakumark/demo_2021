@extends('layouts.admin-layout')
@section('title', $purchase_action.' '.$purchase_type.' Purchase')
@section('script-src', asset('public/admin-assets/pages/manage-bagpurchase.js'))
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
                                <form id="bag_purchase_form" action="{{ url($actionUrl) }}" redirect-url="{{ url($redirectUrl) }}" method="POST">
                                    {{ csrf_field() }}
									<div class="row">
                                        <div class="col-lg-12 mb-4">
                                            <div class="form-group">
                                                <label for="username">Invoice Type</label>
                                                <input class="form-control" type="radio" name="invoice_type" id="invoice_type_manual" value="3" required="" required {{ $inv_details['purchase_source']==3 ? 'checked="checked"' : '' }}>Manual
												<input class="form-control" type="radio" name="invoice_type" id="invoice_type_automatic" value="4" required="" required {{ $inv_details['purchase_source']==4 ? 'checked="checked"' : '' }}>Automatic
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Invoice No.</label>
                                                <input class="form-control" type="text" name="invoice_no" id="invoice_no"  autocomplete="OFF" required="" required value="<?php echo $inv_details['invoice_no'] ?>" {{ $inv_details['purchase_source']==4 ? 'disabled="disabled"' : '' }}>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Purchase Date</label>
                                                <input class="form-control" type="date" name="billdate" id="billdate" value="<?php echo $inv_details['inv_date']; ?>" autocomplete="OFF" required="" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Vendor Name</label>
                                                <select class="form-control select2 supplname" name="supplname">
                                                    <option value="">Select Vendor</option>
                                                    @foreach ($vendorlist as $vendor)
                                                        <option value="{{ $vendor['id'] }}" {{ $vendor['id']==$inv_details['vendor_id'] ? 'selected' : '' }}>{{ $vendor['first_name']." ". $vendor['last_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Current balance</label>
                                                <input class="form-control" type="text" name="balance" id="balance"  autocomplete="OFF" readonly="true" value="<?php echo $vendor_details['current_balance']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Address</label>
                                                <textarea class="form-control" type="text" name="address" id="address"  autocomplete="OFF" readonly="true"
                                                ><?php echo $vendor_details['address']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Warehouse</label>
                                                <select class="form-control select2" name="warehouse">
                                                    <option value="">Select warehouse</option>
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
                                                    <th class="text-center"> # </th>
                                                    <th class="text-center"> Item type </th>
                                                    <th class="text-center" width="20%"> Item name</th>
                                                    <th class="text-center"> No.of Bags(Qty) </th>
                                                    <th class="text-center"> Price </th>
                                                    <th class="text-center"> Amount</th>
                                                    <th class="text-center"> Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id='addr'>
												<?php $i=0; ?>
                                                @foreach ($salesitems as $vendor)
                                                 <?php $i++; ?>
                                                    <tr id='addr<?php echo $i ?>' >
                                                        <td><?php echo $i ?></td>
														<td><select class="form-control select2" name="product_type_id[]" id="product_type<?php echo $i ?>" onchange="return selectProductName(<?php echo $i ?>);">
                                                            <option value="">Select Bag Type</option>
                                                                    <option value="11" {{ $vendor['paddy_id'] ==11 ? 'selected' : '' }} >Paddy</option>
                                                                    <option value="22" {{ $vendor['paddy_id'] ==22 ? 'selected' : '' }} >Rice</option>
                                                        </select></td>
														<td>
                                                        <select class="form-control select2" name="product_id[]" id="product_name<?php echo $i ?>">
														@foreach ($riceList as $rice)
                                                                    <option value="{{ $rice['id'] }}" {{ $rice['id'] ==$vendor['wgt'] ? 'selected' : '' }} >{{ $rice['name'] }}</option>
                                                         @endforeach
                                                        </select>
                                                    </td>
                                                     <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required  value="<?php echo $vendor['qty']; ?>"/></td>
													<td><input type="text" name='rate[]'  placeholder='Enter the rate' class="form-control rate" autocomplete="OFF" step="0.00" min="0"  value="<?php echo $vendor['rate']; ?>" /></td>
                                                    <td><input type="text" name='total[]' placeholder='0.00' class="form-control total" autocomplete="OFF" readonly  value="<?php echo $vendor['total']; ?>" /></td>
                                                    <td><a data-id='<?php echo $i ?>' id="<?php echo $i ?>" onclick="deletethisrow(this.id)" style="color: white" class="deletethisrow pull-right btn btn-danger">Delete Row</a></td>
                                                </tr>
                                                @endforeach
                                                <input type="hidden" name="serialno" value="<?php echo $i ?>" id="serialno">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-md-4">

                                        </div>
                                        <div class="col-md-6">

                                        </div>
                                        <div class="col-md-2">
                                            <a id="add_row" style="color: white" class="btn btn-success btn-default pull-right">Add Row</a>
                                        </div>
                                    </div>
                                    <div class="row clearfix" style="margin-top:20px">
                                        <div class="pull-right col-md-7">
                                            <div class="form-group">
                                                <label for="username">Narration:</label>
                                                <textarea class="form-control" type="text" name="narration" id="narration"  autocomplete="OFF"><?php echo $inv_details['narration']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="pull-right col-md-4">
                                            <table class="table table-bordered table-hover" id="tab_logic_total">
                                                <tbody>
												 <tr>
                                                    <th class="text-center">Transport Charges</th>
                                                    <td class="text-center"><input type="text" name='bro_comm_total' placeholder='0.00' class="form-control" id="bro_comm_total" onkeyup="return calc_total();" value="<?php echo $inv_details['bro_comm_total']; ?>"></td>
                                                </tr>
												<tr>
                                                    <th class="text-center">Sub Total</th>
                                                    <td class="text-center"><input type="number" name='sub_total' placeholder='0.00' class="form-control" id="sub_total" value="<?php echo $inv_details['sub_total']; ?>" readonly/></td>
                                                </tr>
                                                <tr style="display: none">
                                                    <th class="text-center">Tax</th>
                                                    <td class="text-center"><div class="input-group mb-2 mb-sm-0">
                                                            <input type="number" class="form-control" id="tax" placeholder="0">
                                                            <div class="input-group-addon">%</div>
                                                        </div></td>
                                                </tr>
                                                <tr style="display: none">
                                                    <th class="text-center">Tax Amount</th>
                                                    <td class="text-center"><input type="number" name='tax_amount' id="tax_amount" placeholder='0.00' class="form-control" readonly/></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Grand Total</th>
                                                    <td class="text-center"><input type="text" name='total_amount' id="total_amount" placeholder='0.00' class="form-control" value="<?php echo $inv_details['total']; ?>"/></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row clearfix" style="margin-top:20px">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">
										    <a href="{{ url('/admin/bag/purchase') }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                            <button type="submit" id="submit" class="btn btn-primary btn-lg pull-center">Submit</button>
                                        </div>
                                        <div class="col-md-4">
										
                                        </div>
                                    </div>
									<input type="hidden" name="invoice_no_auto" id="invoice_no_auto" value="{{ $nextInvoiceId }}" />
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
    <script>
		var baseUrl = '<?php echo url('/'); ?>';
        $(document).ready(function(){
            $("#add_row").click(function(){
                var i = $("#serialno").val();
                var sno = parseInt(i);
                sno = sno + 1;
                $('#addr').append(`<tr id='addr`+i+`' >
                                                    <td>`+sno+`</td>
                                                    <td><select class="form-control select2" name="product_type_id[]" id="product_type`+sno+`" onchange="return selectProductName(`+sno+`);">
                                                            <option value="">Select Bag Type</option>
                                                                    <option value="11">Paddy</option>
                                                                    <option value="22">Rice</option>
                                                        </select></td>
														<td>
                                                        <select class="form-control select2" name="product_id[]" id="product_name`+sno+`">
                                                        </select>
                                                    </td>
                    <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required  value="<?php echo $vendor['qty']; ?>"/></td>
					<td><input type="text" name='rate[]'  placeholder='Enter the rate' class="form-control rate" autocomplete="OFF" step="0.00" min="0"  value="<?php echo $vendor['rate']; ?>" /></td>
					 <td><input type="text" name='total[]' placeholder='0.00' class="form-control total" autocomplete="OFF" readonly/></td>
                    <td><a data-id='`+i+`' id="`+i+`" onclick="deletethisrow(this.id)" style="color: white" class="deletethisrow pull-right btn btn-danger">Delete Row</a></td>
                </tr>`);
                i++;
                $("#serialno").val(i)
                calc();
            });
            $("#delete_row").click(function(){
                if(i>1){
                    $("#addr"+(i-1)).html('');
                    i--;
                }
                calc();
            });

            $('#tab_logic tbody').on('keyup change',function(){
                console.log('called');
                calc();
            });

            $('#tax').on('keyup change',function(e){
                calc_total();
            });
			
        });
        function deletethisrow(result){
            console.log(result);
            $('#addr'+result).remove();
            calc();
        }
        function calc()
        {
            $('#tab_logic tbody tr').each(function(i, element) {
                var html = $(this).html();
                if(html!='')
                {
                    var quantity = $(this).find('.quantity').val();
                    var rate = $(this).find('.rate').val();
                    var total = quantity*rate;
                    $(this).find('.total').val(total);
                    calc_total();
                }
            });
        }
        function calc_total()
        {
			total=0;
            $('.total').each(function() {
                total += parseInt($(this).val());
            });
			var bro_comm_total = Number($("#bro_comm_total").val());
			var final_total = (bro_comm_total) ? bro_comm_total + total : total;
            $('#sub_total').val(final_total.toFixed(2));
            tax_sum=final_total/100*$('#tax').val();
            $('#tax_amount').val(tax_sum.toFixed(2));
            $('#total_amount').val((tax_sum+final_total).toFixed(2));
        }
        $('.supplname').on('change', function() {
            var user_id = this.value;
            $.ajax(baseUrl+'/purchase/getVendordetails?user_id='+user_id,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        $("#balance").val(data.cashdetails);
                        $("#address").text(data.vendor_details.address);
						$("#balance,#address").attr('readonly',  true);
                    }
                });
        });
		function selectProductName(typeId) {
			
			var getProductType = $("#product_type"+typeId).val();
			if(getProductType==22) {
				$.ajax(baseUrl+'/bag/purchase/getRicedetails',   // request url
					{
						success: function (data, status, xhr) {// success callback function
							$("#product_name"+typeId).html(data);
						}
					});
			} else {
				$("#product_name"+typeId).html('<option value="22">Paddy Bag</option>');
			}
        }
		$('#invoice_type_manual').click(function(){
				$("#invoice_no").val('');
				$("#invoice_no").attr('readonly', false); 
				$("#invoice_no").attr('disabled', false); 
		});
		$('#invoice_type_automatic').click(function(){
			   var invoice_no_auto = $("#invoice_no_auto").val();
			   $("#invoice_no").val(invoice_no_auto);
			   $("#invoice_no").attr('readonly', true);
		});
    </script>
@endsection