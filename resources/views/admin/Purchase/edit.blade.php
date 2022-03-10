@extends('layouts.admin-layout')
@section('title', $purchase_action.' '.$purchase_type.' Purchase')
@section('script-src', asset('public/admin-assets/pages/manage-purchase.js'))
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
                                <form id="farm_bill" action="{{ url('purchase/add') }}" redirect-url="{{ url($redirectUrl) }}" method="post">
                                    {{ csrf_field() }}
									<div class="row">
                                        <div class="col-lg-12 mb-4">
                                            <div class="form-group">
                                                <label for="username">Purchase Source</label>
                                                <input class="form-control" type="radio" name="purchase_source" id="purchase_source_own" value="1" required="" required>Own
												<input class="form-control" type="radio" name="purchase_source" id="purchase_source_vendor" value="2" required="" required>Third Party
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Invoice No.</label>
                                                <input class="form-control" type="text" name="invoice_no" id="invoice_no"  autocomplete="OFF"
                                                       required="" required>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="username">Purchase Date</label>
                                                <input class="form-control" type="date" name="billdate" id="billdate" value="<?php echo date('Y-m-d') ?>" autocomplete="OFF"
                                                       required="" required>
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
                                                        <option value="{{ $vendor['id'] }}" >{{ $vendor['first_name']." ". $vendor['last_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Current balance</label>
                                                <input class="form-control" type="text" name="balance" id="balance"  autocomplete="OFF" readonly="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Address</label>
                                                <textarea class="form-control" type="text" name="address" id="address"  autocomplete="OFF" readonly="true"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mb-4 purchase_source_vendor">
                                            <div class="form-group">
                                                <label for="username">Broker</label>
                                                <select class="form-control select2" name="broker">
                                                    <option value="">Select broker</option>
                                                    @foreach ($brokerlist as $vendor)
                                                        <option value="{{ $vendor['id'] }}" >{{ $vendor['first_name']." ". $vendor['last_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Warehouse</label>
                                                <select class="form-control select2" name="warehouse">
                                                    <option value="">Select warehouse</option>
                                                    @foreach ($warehouseList as $vendor)
                                                        <option value="{{ $vendor['id'] }}" >{{ $vendor['name']." ". $vendor['mobile_number'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-4">
                                            <div class="form-group">
                                                <label for="username">Vehicle No:</label>
                                                <input class="form-control" type="text" name="veh_no" id="veh_no"  autocomplete="OFF" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover  table-responsive" id="tab_logic">
                                                <thead>
                                                <tr>
                                                    <th class="text-center"> # </th>
                                                    <th class="text-center"> Item name</th>
                                                    <th class="text-center"> Weight(Kgs) </th>
                                                    <th class="text-center"> No.of Bags(Qty) </th>
													<th class="text-center"> Total Weight(Kgs)</th>
                                                    <th class="text-center purchase_source_vendor"> Rate</th>
                                                    <th class="text-center purchase_source_vendor"> Amount</th>
                                                    <th class="text-center"> Action</th>
                                                </tr>
                                                </thead>
                                                <tbody id='addr'>
                                                <tr id='addr1' >
                                                    <td>1</td>
                                                    <td>
                                                        <select class="form-control select2" name="product_id[]">
                                                            <option value="">Select Paddy</option>
                                                            @foreach ($productList as $category)
                                                                <option value="{{ $category->id }}" >{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name='wgt[]' placeholder='Enter the weight' class="form-control weight" step="0" min="0" autocomplete="OFF" required/></td>
                                                    <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required/></td>
                                                    <td><input type="text" name='totalwgt[]' placeholder='' class="form-control totalweight" value="" autocomplete="OFF" readonly/></td>
													<td class="purchase_source_vendor_items"><input type="text" name='rate[]'  placeholder='Enter the rate' class="form-control rate" autocomplete="OFF" step="0.00" min="0" /></td>
                                                    <td class="purchase_source_vendor_items"><input type="text" name='total[]' placeholder='0.00' class="form-control total" autocomplete="OFF" readonly/></td>
                                                    <td><a data-id='1' id="1" onclick="deletethisrow(this.id)" style="color: white" class="deletethisrow pull-right btn btn-danger">Delete Row</a></td>
                                                </tr>
                                                <input type="hidden" value="1" id="serialno">
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
                                            <div class="form-group">
                                                <label for="username">Narration:</label>
                                                <textarea class="form-control" type="text" name="narration" id="narration"  autocomplete="OFF"
                                                ></textarea>
                                            </div>
                                        </div>
                                        <div class="pull-right col-md-4">
                                            <div class="form-group">
                                                <label for="username">Weigh 1st:</label>
                                                <input class="form-control" type="text" name="weigh1" id="weigh1"  autocomplete="OFF" >

                                            </div>
                                            <div class="form-group">
                                                <label for="username">Weigh 2nd:</label>
                                                <input class="form-control" type="text" name="weigh2" id="weigh2"  autocomplete="OFF" >

                                            </div>
                                            <div class="form-group">
                                                <label for="username">Weigh 3rd:</label>
                                                <input class="form-control" type="text" name="weigh3" id="weigh3"  autocomplete="OFF" >

                                            </div>
                                        </div>
                                        <div class="pull-right col-md-4">
                                            <table class="table table-bordered table-hover" id="tab_logic_total">
                                                <tbody>
                                                <tr class="purchase_source_vendor">
                                                    <th class="text-center">Broker Commission <input type="text" name='bro_comm' placeholder='0.00' class="form-control" id="bro_comm" onkeyup="return calc_total();"></th>
                                                    <td class="text-center"><input type="number" name='bro_comm_total' placeholder='0.00' class="form-control" id="bro_comm_total" readonly></td>
                                                </tr>
												<tr class="purchase_source_vendor">
                                                    <th class="text-center">Sub Total</th>
                                                    <td class="text-center"><input type="number" name='sub_total' placeholder='0.00' class="form-control" id="sub_total" readonly/></td>
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
                                                <tr class="purchase_source_vendor">
                                                    <th class="text-center">Grand Total</th>
                                                    <td class="text-center"><input type="text" name='total_amount' id="total_amount" placeholder='0.00' class="form-control"/></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row clearfix" style="margin-top:20px">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" id="submit" class="btn btn-primary pull-center">Submit</button>
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
				var purchase_source = $('input[name="purchase_source"]:checked').val();
				var purchase_source_vendor_items = (purchase_source==2) ? 'style="display:none"' : '';
                $('#addr').append(`<tr id='addr`+i+`' >
                                                    <td>`+sno+`</td>
                                                    <td>
                                                        <select class="form-control select2" name="product_id[]">
                                                            <option value="">Select Paddy</option>
                                                            @foreach ($productList as $category)
                <option value="{{ $category->id }}" >{{ $category->name }}</option>
                                                            @endforeach
                </select>
                    </td>
                    <td><input type="text" name='wgt[]' placeholder='Enter the weight' class="form-control weight" step="0" min="0" autocomplete="OFF" required/></td>
                    <td><input type="text" name='qty[]' placeholder='Enter the quantity' class="form-control quantity" step="0" min="0" autocomplete="OFF" required/></td>
                    <td><input type="text" name='totalwgt[]' placeholder='' class="form-control totalweight" autocomplete="OFF" readonly /></td>
                    <td class="purchase_source_vendor_items" `+purchase_source_vendor_items+`><input type="text" name='rate[]' placeholder='Enter the rate' class="form-control rate" value="" autocomplete="OFF" step="0.00" min="0" /></td>
                    <td class="purchase_source_vendor_items" `+purchase_source_vendor_items+`><input type="text" name='total[]' placeholder='0.00' class="form-control total" autocomplete="OFF" readonly/></td>
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
			
			$('#purchase_source_own').click(function(){
				$("#invoice_no").val('');
				$("#invoice_no").attr('readonly', false);
				$('#tab_logic tbody tr').each(function(i, element) {
					$('#addr'+i).remove();
					calc();
				});
				$(".purchase_source_vendor_items").show();
				$(".purchase_source_vendor").show(); 
			});
			$('#purchase_source_vendor').click(function(){
			   var invoice_no_auto = $("#invoice_no_auto").val();
			   $("#invoice_no").val(invoice_no_auto);
			   $("#invoice_no").attr('readonly', true);
			   $('#tab_logic tbody tr').each(function(i, element) {
					$('#addr'+i).remove();
					calc();
				});
				$(".purchase_source_vendor_items").hide();
				$(".purchase_source_vendor").hide();
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
                    var weight = $(this).find('.weight').val();
                    var quantity = $(this).find('.quantity').val();
                    var rate = $(this).find('.rate').val();
                    var totalweight = weight*quantity;
                    var totalamount = totalweight*rate;
                    $(this).find('.totalweight').val(totalweight);
                    $(this).find('.total').val(totalamount);
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
			totalweight=0;
            $('.totalweight').each(function() {
                totalweight += parseInt($(this).val());
            });
			bro_comm_total=0;
			var bro_comm = $("#bro_comm").val();
			if(bro_comm) {
				bro_comm_total = totalweight * bro_comm;
				$('#bro_comm_total').val(bro_comm_total.toFixed(2));
			}
			var final_total = bro_comm_total + total;
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
    </script>
@endsection