@extends('layouts.admin-layout')
@section('title', $bill_action.' '.$bill_type.' Bill')
@section('script-src', asset('public/admin-assets/pages/manage-b2cbill.js'))
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
                                <form id="b2c_bill_form" action="{{ url($actionUrl) }}" redirect-url="{{ url($redirectUrl) }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Invoice No.</label>
                                                <input class="form-control" type="text" name="invoice_no" id="invoice_no"  autocomplete="OFF" required="" required value="<?php echo $inv_details['invoice_no'] ?>" readonly="true">
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Bill Date</label>
                                                <input class="form-control" type="date" name="billdate" id="billdate" value="<?php echo $inv_details['inv_date']; ?>" autocomplete="OFF" required="" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Customer Name</label>
                                                <select class="form-control select2 supplname" name="supplname">
                                                    <option value="">Select Customer</option>
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
										 <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">HSN</label>
												<input class="form-control" type="text" name="hsn" id="hsn" value="<?php echo $hsnCodeList['name']; ?>" readonly="" autocomplete="OFF" required="" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover  table-responsive" id="tab_logic">
                                                <thead>
                                                <tr>
                                                    <th class="text-center"> # </th>
                                                    <th class="text-center"> Item</th>
                                                    <th class="text-center"> No.of Bags(Qty) </th>
                                                    <th class="text-center"> Price </th>
													<th class="text-center"> Final Price</th>
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
														<td>
                                                        <select class="form-control select2" name="product_id[]" id="product_name<?php echo $i ?>" onchange="return selectProductName(<?php echo $i ?>);" required>
														<option value="">Select Rice</option>
														@foreach ($riceList as $rice)
                                                                    <option value="{{ $rice['id'] }}" {{ $rice['id'] ==$vendor['paddy_id'] ? 'selected' : '' }} >{{ $rice['name'] }}</option>
                                                         @endforeach
                                                        </select>
                                                    </td>
                                                     <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required  value="<?php echo $vendor['qty']; ?>"/></td>
													<td><input type="text" name='price[]' id="price<?php echo $i ?>"  placeholder='Enter the price' class="form-control price" autocomplete="OFF" step="0.00" min="0"  value="<?php echo $vendor['price']; ?>" readonly /></td>
													<td><input type="text" name='final_price[]' id="final_price<?php echo $i ?>" placeholder='Enter the final price' class="form-control final_price" autocomplete="OFF" step="0.00" min="0"  value="<?php echo $vendor['final_price']; ?>" /></td>
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
                                        
                                        <div class="pull-right col-md-4">
                                            <table class="table table-bordered table-hover" id="tab_logic_total">
                                                <tbody>
												<tr>
                                                    <th class="text-center">Sub Total</th>
                                                    <td class="text-center"><input type="number" name='sub_total' placeholder='0.00' class="form-control" id="sub_total" value="<?php echo $inv_details['sub_total']; ?>" readonly/></td>
                                                </tr>
												<tr>
                                                    <th class="text-center">Discount</th>
                                                    <td class="text-center"><input type="text" name='discount' placeholder='0.00' class="form-control" id="discount" onkeyup="return calc_total();" value="<?php echo $inv_details['discount']; ?>"></td>
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

                                        <div class="pull-right col-md-4">
                                            <table class="table table-bordered table-hover" id="tab_logic_total">
                                                <tbody>
												<tr>
                                                    <th class="text-center">Payment</th>
                                                    <td class="text-center1">
                                                        <input type="radio" name='payment_type' id="cash" class="form-control1 bill-radio-bttn" value="3" {{ $inv_details['payment_type'] == 3 ? 'checked':'' }} required /> Cash <br/>
                                                        <input type="radio" name='payment_type' id="bank" class="form-control1 bill-radio-bttn" value="1" {{ $inv_details['payment_type'] == 1 ? 'checked':'' }} required /> Bank <br/>
                                                        <input type="radio" name='payment_type' id="online_pay" class="form-control1 bill-radio-bttn" value="2" {{ $inv_details['payment_type'] == 2 ? 'checked':'' }} required /> Online Pay 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Cash</th>
                                                    <td class="text-center"><input type="number" name='cash_amount' placeholder='0.00' class="form-control" id="cash_amount" onkeyup="return calc_cash_amount();" value='<?php echo $bill_payment['cash_amount']; //echo $inv_details['total']; ?>' /> </td>
                                                </tr>
												<tr>
                                                    <th class="text-center">Credit</th>
                                                    <td class="text-center"><input type="number" name='credit_amount' placeholder='0.00' class="form-control" id="credit_amount" value="<?php echo $bill_payment['credit_amount'];?>" readonly="true"/></td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>                                        
                                           
                                        <div class="col-lg-4 mb-4">
                                        <?php 
                                            $bank=''; $online_pay='';

                                            if($inv_details['payment_type'] == 1) {
                                               $bank = "display: block;";
                                               $online_pay = "display: none;";
                                            }else if($inv_details['payment_type'] == 2) {
                                                $online_pay = "display: block !important;";
                                                $bank = "display: none;";
                                            }else{
                                                $bank = "display: none;";
                                                $online_pay = "display: none;";   
                                            }
                                         ?> 
                                            <div class="form-group bank-pay" style="{{ $bank }}">
                                                <label for="username"><b>Bank</b></label> {{ $bill_payment['bank_id']}}
                                                <select class="form-control select2 bank_list" name="bank_list">
                                                    <!-- <option value="0">Select Bank</option> -->
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank['id'] }}" {{ $bank['id'] == $bill_payment['bank_id'] ? 'selected' : '' }} >{{ $bank['bank_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        
                                            <div class="form-group online-pay" style="{{ $online_pay }}">
                                                <label for="username"><b>Online Payment</b></label>
                                                <select class="form-control select2 online_payment_list" name="online_payment_list" >
                                                    <!-- <option value="0">Select Online Payment</option> -->
                                                    @foreach ($online_payment as $onlinepay)
                                                        <option value="{{ $onlinepay['id'] }}" {{ $onlinepay['id'] == $bill_payment['online_pay_id'] ? 'selected' : '' }} >{{ $onlinepay['payment_type'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="username">Comments</label>
                                                <textarea class="form-control" name="comments" id="comments"  autocomplete="OFF"><?php echo $inv_details['comments']; ?></textarea>
                                            </div>
                                           

                                          
                                        </div>
                                        
                                    </div>
                                    <div class="row clearfix" style="margin-top:20px">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">
										    <a href="{{ url($redirectUrl) }}" class="btn btn-danger btn-lg back"><i class="fas fa-arrow-left"></i> Back</a>
                                            <button type="submit" id="submit" class="btn btn-success btn-lg pull-center">{{$bill_action_label}}</button>
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
    <style>
        .online-pay .select2-container--default{
            width: 100% !important;
        }
        .bank-pay .select2-selection--single {
            width: 307px !important;
        }
    </style>

    <script>
		var baseUrl = '<?php echo url('/'); ?>';
        $(document).ready(function(){
            $("#add_row").click(function(){
                var i = $("#serialno").val();
                var sno = parseInt(i);
                sno = sno + 1;
                $('#addr').append(`<tr id='addr`+i+`' >
                                                    <td>`+sno+`</td>
														<td>
														 <select class="form-control select2" name="product_id[]" id="product_name`+sno+`" onchange="return selectProductName(`+sno+`);" required>
														 <option value="">Select Rice</option>
														@foreach ($riceList as $rice)
                                                                    <option value="{{ $rice['id'] }}" {{ $rice['id'] ==$vendor['paddy_id'] ? 'selected' : '' }} >{{ $rice['name'] }}</option>
                                                         @endforeach
                                                        </select>
                                                        
                                                    </td>
                    <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required  value="<?php echo $vendor['qty']; ?>"/></td>
					<td><input type="text" name='price[]' id="price`+sno+`" placeholder='Enter the price' class="form-control price" autocomplete="OFF" step="0.00" min="0"  value="<?php echo $vendor['price']; ?>" readonly/></td>
					<td><input type="text" name='final_price[]' id="final_price`+sno+`"  placeholder='Enter the final price' class="form-control final_price" autocomplete="OFF" step="0.00" min="0"  value="<?php echo $vendor['final_price']; ?>" /></td>
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

			// $('.online-pay').css('display','none'); 
			// $('#submit').prop("disabled", true);
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
                    var final_price = $(this).find('.final_price').val();
                    var total = quantity*final_price;
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
			var final_total = total;
			var discount = Number($("#discount").val()) ;
			if(discount) {
				discount = discount /100;
				var final_total = total - (total * discount);
			}
            $('#sub_total').val(total.toFixed(2));
            tax_sum=final_total/100*$('#tax').val();
            $('#tax_amount').val(tax_sum.toFixed(2));
            $('#total_amount').val((tax_sum+final_total).toFixed(2));

            $('#cash_amount').val((tax_sum+final_total).toFixed(2));
        }

        function calc_cash_amount() {
            var total_amount    = $('#total_amount').val();                
            var cash_amount     = $('#cash_amount').val();
            var crdit_amount    = (total_amount - cash_amount);
            
            $('#credit_amount').val(crdit_amount);
        }
        
        $('#cash').click(function() {
            if($('#cash').is(':checked')) 
            { 
                console.log('CASH');
                $('.online-pay').css('display','none'); 
                $('.bank-pay').css('display','none');
                $('.bank_list').attr('required', false); 
                $('.online_payment_list').attr('required', false);
                $('#submit').prop("disabled", false);
            }                                
        });

        $('#bank').click(function() {
            if($('#bank').is(':checked')) 
            { console.log('BANK');
                $('.online-pay').css('display','none'); 
                $('.bank-pay').css('display','block');
                $('.bank_list').attr('required', true);
                $('#submit').prop("disabled", false); 
            }                                
        });

        $('#online_pay').click(function() {
            if($('#online_pay').is(':checked')) 
            { console.log('Online pay');
                $('.online-pay').css('display','block');                
                $('.bank-pay').css('display','none');                
                $('.online_payment_list').attr('required', true);
                $('#submit').prop("disabled", false); 
            }                                 
        });
        

        $('.supplname').on('change', function() {
            var user_id = this.value;
            $.ajax(baseUrl+'/b2c/bill/getVendordetails?user_id='+user_id,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        $("#balance").val(data.cashdetails);
                        $("#address").text(data.vendor_details.address);
						$("#balance,#address").attr('readonly',  true);
                    }
                });
        });
		function selectProductName(typeId) {
			$("#price"+typeId).val('');
			$("#final_price"+typeId).val('');
			var getProductId = $("#product_name"+typeId).val();
			$.ajax(baseUrl+'/b2c/bill/getRicedetails/'+getProductId,   // request url
					{
						success: function (data, status, xhr) {// success callback function
							$("#price"+typeId).val(data);
							$("#price"+typeId).attr('readonly', true); 
							$("#final_price"+typeId).val(data);
						}
			});
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