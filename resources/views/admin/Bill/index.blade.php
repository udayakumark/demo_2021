@extends('layouts.admin-layout')
@section('title', 'Manage Bill')
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
                                                        <h4 class="card-title">Bill Generate</h4>
                                                    </div>
                                                    <div class="col-lg-6 mb-4" style="text-align: right">

                                                    </div>
                                                </div>
                                                <form id="farm_bill" action="{{ url('bill/add') }}" method="post">
                                                    {{ csrf_field() }}
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-4">
                                                            <div class="form-group">
                                                                <label for="username">Vendor Name</label>
                                                                <select class="form-control select2" name="supplname">
                                                                    <option value="">Select Vendor</option>
                                                                    @foreach ($vendorlist as $vendor)
                                                                        <option value="{{ $vendor['id'] }}" >{{ $vendor['first_name']." ". $vendor['last_name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 mb-4">
                                                            <div class="form-group">
                                                                <label for="username">Bill Date</label>
                                                                <input class="form-control" type="date" name="billdate" id="billdate"
                                                                       required="" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row clearfix">
                                                        <div class="col-md-12">
                                                            <table class="table table-bordered table-hover  table-responsive" id="tab_logic">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center"> # </th>
                                                                    <th class="text-center"> Product </th>
                                                                    <th class="text-center"> Qtl. Qty </th>
                                                                    <th class="text-center"> Qty </th>
                                                                    <th class="text-center"> Sales price (Rs.)</th>
                                                                    <th class="text-center"> Total (Rs.)</th>
                                                                    <th class="text-center">Action</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id='addr'>
                                                                <tr id='addr1' >
                                                                    <td>1</td>
                                                                    <td>
                                                                        <select class="form-control select2" name="product_id[]">
                                                                            <option value="">Select Product</option>
                                                                            @foreach ($productList as $category)
                                                                                <option value="{{ $category->id }}" >{{ $category->product_name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="text" name='qtlqty[]' placeholder='Enter Qtl' class="form-control Qtl" step="0" min="0" required/></td>
                                                                    <td><input type="text" name='qty[]' placeholder='Enter Qty' class="form-control qty" step="0" min="0" required/></td>
                                                                    <td><input type="text" name='salesprice[]'  placeholder='Enter Unit salesprice' class="form-control salesprice" step="0.00" min="0" /></td>
                                                                    <td><input type="text" name='total[]' placeholder='0.00' class="form-control total" readonly/></td>
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
                                                        <div class="pull-right col-md-4"></div>
                                                        <div class="pull-right col-md-4"></div>
                                                        <div class="pull-right col-md-4">
                                                            <table class="table table-bordered table-hover" id="tab_logic_total">
                                                                <tbody>
                                                                <tr>
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
                                                                <tr>
                                                                    <th class="text-center">Grand Total</th>
                                                                    <td class="text-center"><input type="number" name='total_amount' id="total_amount" placeholder='0.00' class="form-control" readonly/></td>
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
        $(document).ready(function(){


            $("#add_row").click(function(){

                var i = $("#serialno").val();
                var sno = parseInt(i);
                sno = sno + 1;
                console.log(sno);
                // $('#addr'+i).html($('#addr'+b).html()).find('td:first-child').html(i+1);
                $('#addr').append(`<tr id='addr`+i+`'><td>`+sno+`</td>
                <td>
                <select class="form-control select2" name="product_id[]">
                <option value="">Select Product</option>
                 @foreach ($productList as $category)
                                <option value="{{ $category->id }}" >{{ $category->product_name }}</option>
                @endforeach
                </select>
                </td>
                 <td><input type="text" name='qtlqty[]' placeholder='Enter Qtl' class="form-control Qtl" step="0" min="0" required/></td>
                 <td><input type="text" name='qty[]' value="" placeholder='Enter Qty' class="form-control qty" step="0" min="0" required/><input type="hidden" name="purchaseID[]" value=""></td>
                 <td><input type="text" name='salesprice[]'  placeholder='Enter Unit salesprice' class="form-control salesprice" step="0.00" min="0" /></td>
                 <td><input type="text" name='total[]' placeholder='0.00' class="form-control total" readonly/></td>
                  <td><a data-id='`+i+`' id="`+i+`" style="color: white" onclick="deletethisrow(this.id)" class="deletethisrow pull-right btn btn-danger">Delete Row</a></td>
             </tr>`);

                i++;

                $("#serialno").val(i)
                calc();
            });

            // $("#farm_bill").submit(function (e) {
            //
            //     e.preventDefault();
            //     console.log("calling ajax!");
            //
            //     var formData = new FormData($("#farm_bill")[0]);
            //
            //     console.log(formData);
            //     $.ajax({
            //         url: "ajax_purchasebill.php",
            //         type: "post",
            //         data: formData,
            //         contentType: false,
            //         processData: false,
            //         success: function (data) {
            //             console.log(data);
            //             //return;
            //             var status = data.split("|")[0];
            //             var message = data.split("|")[1];
            //
            //             if (status.trim() == "SUCCESS")
            //             {
            //                 alert(message);
            //                 window.location.href = "new_purchase.php";
            //                 return true;
            //             }
            //             if (status.trim() == "ERROR") {
            //                 alert(message);
            //             }
            //         }
            //
            //     });
            //     return false;
            // });


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
                    var qty = $(this).find('.qty').val();
                    var price = $(this).find('.salesprice').val();
                    var withouttax = qty*price;

                    $(this).find('.total').val(withouttax);

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
            $('#sub_total').val(total.toFixed(2));
            tax_sum=total/100*$('#tax').val();
            $('#tax_amount').val(tax_sum.toFixed(2));
            $('#total_amount').val((tax_sum+total).toFixed(2));
        }

    </script>
@endsection


