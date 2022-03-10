@extends('layouts.admin-layout')
@section('title', 'Paddy Production own')
@section('content')
    <style>
        table.GeneratedTable {
            width: 100%;
            background-color: #ffffff;
            border-collapse: collapse;
            border-width: 2px;
            border-color: #fffefe;
            border-style: solid;
            color: #fffcfc;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        /*table.GeneratedTable td, table.GeneratedTable th {*/
        /*    border-width: 2px;*/
        /*    border-color: #ffffff;*/
        /*    border-style: solid;*/
        /*}*/

        table.GeneratedTable thead {
            background-color: #255eb2;
        }
        table.GeneratedTable tfoot {
            background-color: #255eb2;
        }
    </style>
    <div class="main-content">
        <section class="section" id="grid-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
{{--                                    <div class="col-lg-6 mb-4">--}}
{{--                                        <h4 class="card-title">Paddy Production own</h4>--}}
{{--                                    </div>--}}
                                    <div class="col-lg-12 mb-4" style="text-align: right">
                                        <!-- CSS Code: Place this code in the document's head (between the 'head' tags) -->


                                        <!-- HTML Code: Place this code in the document's body (between the 'body' tags) where the table should appear -->
                                        <table class="GeneratedTable">
                                            <thead style="text-align: center;border-width: 2px;border-color: #ffffff;border-style: solid;">
                                            <tr>
                                                <th width="50%" colspan="4">Source <button type="button" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus"></i></button></th>
                                                <th width="50%" colspan="4">Destination <button type="button" data-toggle="modal" data-target="#exampleModal2"><i class="fa fa-plus"></i></button></th>
                                            </tr>

                                            </thead>
                                            <thead style="text-align: center;">
                                            <tr>
                                                <th width="15%">Name of Item</th>
                                                <th width="35%" colspan="3">Godown</th>
                                                <th width="15%">Name of Item</th>
                                                <th width="35%" colspan="3">Godown</th>
                                            </tr>
                                            </thead>
                                            <thead style="text-align: center">
                                            <tr>
                                                <th width="15%"></th>
                                                <th width="11%" style="text-align: right">Qty</th>
                                                <th width="12%" style="text-align: right">Rate</th>
                                                <th width="12%" style="text-align: right">Amount</th>
                                                <th width="15%"></th>
                                                <th width="11% " style="text-align: right">Qty</th>
                                                <th width="12%" style="text-align: right">Rate</th>
                                                <th width="12%" style="text-align: right">Amount</th>
                                            </tr>

                                            </thead>
                                            <tbody id="tbodys" style="color: #000000;">
                                            <tr>
                                                <th width="50%" colspan="4" style="vertical-align:top">
                                                    <table>
                                                        <tbody id="tbody" style="color: #000000;border-width: 2px;border-color: #000000;border-style: solid;">


                                                        </tbody>
                                                    </table>
                                                </th>
                                                <th width="50%" colspan="4" style="vertical-align:top">
                                                    <table>
                                                        <tbody id="tbody2" style="border-width: 2px;border-color: #000000;border-style: solid;">


                                                        </tbody>
                                                    </table>
                                                </th>

                                            </tr>
                                            </tbody>
                                            <tr style="background-color: #255eb2">
                                                <th width="15%"></th>
                                                <th width="11%" id="totsrcqty" style="text-align: right"></th>
                                                <th width="12%" style="text-align: right"></th>
                                                <th width="12%" id="totsrcamt" style="text-align: right"></th>
                                                <th width="15%"></th>
                                                <th width="11% " style="text-align: right"></th>
                                                <th width="12%" style="text-align: right"></th>
                                                <th width="12%" id="totdesamt" style="text-align: right"></th>
                                            </tr>
                                        </table>
                                        <!-- Codes by Quackit.com -->
                                    </div>
                                    <div class="col-lg-12 mb-4" style="text-align: center">
                                        <button class="btn btn-primary" onclick="formsubmit()">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel"
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
                    <div class="card-header">
                        <div class="col-8"><h4>Source</h4><br>
                        <div id="errortag"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-12">
                            <input type="hidden" value="<?php echo $next_id ?>" id="next_id">
                            <label>Type</label>
                            <select class="form-control col-12" name="srcVendorType" id="srcVendorType">
                                <option value="">Select Type</option>
                                @foreach ($VendorType as $vendor)
                                    <option value="{{ $vendor['id'] }}" >{{ $vendor['name'] }}</option>
                                @endforeach
                            </select>
                            <label>Item</label>
                            <select class="form-control col-12" name="srcitemlist" id="srcitemlist">
                                <option value="">Select Item</option>
                            </select>
                            <label>Warehouse</label>
                            <select class="form-control col-12" name="srcwarehouseid" id="srcwarehouseid">
                                @foreach ($warehouseList as $vendor)
                                    <option value="{{ $vendor['id'] }}" >{{ $vendor['name'] }}</option>
                                @endforeach
                            </select>
                            <label>Qty</label>
                            <input class="form-control  col-12" type="number" name="srcqty" id="srcqty" />
                            <label>Rate</label>
                            <input class="form-control  col-12" type="number" name="srcrate" id="srcrate" />
                            <button class="btn btn-primary" onclick="submitadd()">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal2" role="dialog" aria-labelledby="exampleModalLabel2"
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
                    <div class="card-header">
                        <div class="col-8"><h4>Destination</h4><br>
                            <div id="errortag"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-12">
                            <input type="hidden" value="<?php echo $next_id ?>" id="next_id2">
                            <label>Type</label>
                            <select class="form-control col-12" name="srcVendorType" id="srcVendorType2">
                                <option value="">Select Type</option>
                                @foreach ($VendorType as $vendor)
                                    <option value="{{ $vendor['id'] }}" >{{ $vendor['name'] }}</option>
                                @endforeach
                            </select>
                            <label>Item</label>
                            <select class="form-control col-12" name="srcitemlist" id="srcitemlist2">
                                <option value="">Select Item</option>
                            </select>
                            <label>Warehouse</label>
                            <select class="form-control col-12" name="srcwarehouseid" id="srcwarehouseid2">
                                @foreach ($warehouseList as $vendor)
                                    <option value="{{ $vendor['id'] }}" >{{ $vendor['name'] }}</option>
                                @endforeach
                            </select>
                            <label>Qty</label>
                            <input class="form-control  col-12" type="number" name="srcqty" id="srcqty2" />
                            <label>Rate</label>
                            <input class="form-control  col-12" type="number" name="srcrate" id="srcrate2" />
                            <button class="btn btn-primary" onclick="submitadd2()">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var data = <?php echo $SourceItems; ?>;
        var data2 = <?php echo $DestinationItems; ?>;
        var dessum = <?php echo $dessum; ?>;
        var srcqty = <?php echo $srcqty; ?>;
        var srcsum = <?php echo $srcsum; ?>;
        $(document).ready(function(){

            $("#tbody").html(' ');
            var html = '';
            for(var i = 0; i<data.length; i++){
                html += '<tr> ' +
                    '<th width="15%">'+data[i].item_id+'</th> ' +
                    '<th width="35%" colspan="3" style="text-align: center">'+data[i].warehouse_id+'</th> ' +
                    '</tr>' +
                    '<tr> <td width="15%" style="text-align: left"><a href="#" onclick="deletesrcitem('+data[i].id+')"><i style="color:red;" class="fa fa-trash" aria-hidden="true"></i></a></td> ' +
                    '<td width="11%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changesrcqty(this.value,'+data[i].id+')" value="'+data[i].qty+'" /> Bag</td> ' +
                    '<td width="12%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changesrcrate(this.value,'+data[i].id+')" value="'+data[i].rate+'" /> /Bag</td> ' +
                    '<td width="12%" style="text-align: right">'+rupeeformat(data[i].amount)+'</td> ' +
                    '</tr>';
            }

            $("#tbody").html(html);

            $("#totsrcqty").text(srcqty+' Bags');
            $("#totsrcamt").text(rupeeformat(srcsum));


            $("#tbody2").html(' ');
            var html2 = '';
            for(var i = 0; i<data2.length; i++){
                html2 += '<tr> ' +
                    '<th width="15%">'+data2[i].item_id+'</th> ' +
                    '<th width="35%" colspan="3" style="text-align: center">'+data2[i].warehouse_id+'</th> ' +
                    '</tr>' +
                    '<tr> <td width="15%" style="text-align: left"><a href="#" onclick="deletedesitem('+data2[i].id+')"><i style="color:red;" class="fa fa-trash" aria-hidden="true"></i></a></td> ' +
                    '<td width="11%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changedesqty(this.value,'+data2[i].id+')" value="'+data2[i].qty+'" /> Bag</td> ' +
                    '<td width="12%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changedesrate(this.value,'+data2[i].id+')" value="'+data2[i].rate+'" /> /Bag</td> ' +
                    '<td width="12%" style="text-align: right">'+rupeeformat(data2[i].amount)+'</td> ' +
                    '</tr>';
            }

            $("#tbody2").html(html2);

            $("#totdesamt").text(rupeeformat(dessum));
        });
        function submitadd(){

            var srcVendorType = $("#srcVendorType").val();
            var srcitemlist = $("#srcitemlist").val();
            var srcwarehouseid = $("#srcwarehouseid").val();
            var srcqty = $("#srcqty").val();
            var srcrate = $("#srcrate").val();
            var next_id = $("#next_id").val();

            $.ajax('/production/addsource?srcVendorType='+srcVendorType+'&srcitemlist='+srcitemlist+'&srcwarehouseid='+srcwarehouseid+'&srcqty='+srcqty+'&srcrate='+srcrate+'&next_id='+next_id,   // request url
                {
                    success: function (data, status, xhr) {// success callback function

                        var datas = data.SourceItems;
                        srcdiv(datas);
                        var srcqty = data.srcqty+" Bags";
                        var srcsum = data.srcsum;
                        $("#totsrcqty").text(srcqty);
                        $("#totsrcamt").text(rupeeformat(srcsum));
                        $("#exampleModal").modal('toggle');
                    }
                });

        }

        function srcdiv(data){
            $("#tbody").html(' ');
            var html = '';
            for(var i = 0; i<data.length; i++){
                html += '<tr> ' +
                    '<th width="15%">'+data[i].item_id+'</th> ' +
                    '<th width="35%" colspan="3" style="text-align: center">'+data[i].warehouse_id+'</th> ' +
                    '</tr>' +
                    '<tr> <td width="15%" style="text-align: left"><a href="#" onclick="deletesrcitem('+data[i].id+')"><i style="color:red;" class="fa fa-trash" aria-hidden="true"></i></a></td> ' +
                    '<td width="11%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changesrcqty(this.value,'+data[i].id+')" value="'+data[i].qty+'" /> Bag</td> ' +
                    '<td width="12%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changesrcrate(this.value,'+data[i].id+')" value="'+data[i].rate+'" /> /Bag</td> ' +
                    '<td width="12%" style="text-align: right">'+rupeeformat(data[i].amount)+'</td> ' +
                    '</tr>';
            }

            $("#tbody").html(html);

        }

        function desdiv(data){

            $("#tbody2").html(' ');
            var html = '';
            for(var i = 0; i<data.length; i++){
                html += '<tr> ' +
                    '<th width="15%">'+data[i].item_id+'</th> ' +
                    '<th width="35%" colspan="3" style="text-align: center">'+data[i].warehouse_id+'</th> ' +
                    '</tr>' +
                    '<tr> <td width="15%" style="text-align: left"><a href="#" onclick="deletedesitem('+data[i].id+')"><i style="color:red;" class="fa fa-trash" aria-hidden="true"></i></a></td> ' +
                    '<td width="11%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changedesqty(this.value,'+data[i].id+')" value="'+data[i].qty+'" /> Bag</td> ' +
                    '<td width="12%" style="text-align: right"><input type="number" style="width: 58px;" onblur="changedesrate(this.value,'+data[i].id+')" value="'+data[i].rate+'" /> /Bag</td> ' +
                    '<td width="12%" style="text-align: right">'+rupeeformat(data[i].amount)+'</td> ' +
                    '</tr>';
            }

            $("#tbody2").html(html);


        }

        function submitadd2(){

            var srcVendorType = $("#srcVendorType2").val();
            var srcitemlist = $("#srcitemlist2").val();
            var srcwarehouseid = $("#srcwarehouseid2").val();
            var srcqty = $("#srcqty2").val();
            var srcrate = $("#srcrate2").val();
            var next_id = $("#next_id2").val();

            $.ajax('/production/adddestination?srcVendorType='+srcVendorType+'&srcitemlist='+srcitemlist+'&srcwarehouseid='+srcwarehouseid+'&srcqty='+srcqty+'&srcrate='+srcrate+'&next_id='+next_id,   // request url
                {
                    success: function (data, status, xhr) {// success callback function

                        var datas = data.DestinationItems;
                        desdiv(datas);
                        var dessum = data.dessum;
                        $("#totdesamt").text(rupeeformat(dessum));
                        $("#exampleModal2").modal('toggle');

                    }
                });

        }

        $('#srcVendorType').on('change', function() {
            var type = this.value;
            $.ajax('/production/getitemlist?type='+type,   // request url
                {
                    success: function (data, status, xhr) {// success callback function

                        $("#srcitemlist").html(' ');
                        data = data.vendor_details;
                        var html = '';
                        for(var i = 0; i<data.length; i++){
                            html += '<option value="'+data[i].id+'" >'+data[i].name+'</option>';
                        }

                        $("#srcitemlist").html(html);
                    }
                });
        });
        $('#srcVendorType2').on('change', function() {
            var type = this.value;
            $.ajax('/production/getitemlist?type='+type,   // request url
                {
                    success: function (data, status, xhr) {// success callback function

                        $("#srcitemlist2").html(' ');
                        data = data.vendor_details;
                        var html = '';
                        for(var i = 0; i<data.length; i++){
                            html += '<option value="'+data[i].id+'" >'+data[i].name+'</option>';
                        }

                        $("#srcitemlist2").html(html);
                    }
                });
        });
        function rupeeformat(result){
            var x=result;
            x=x.toString();
            var afterPoint = '';
            if(x.indexOf('.') > 0)
                afterPoint = x.substring(x.indexOf('.'),x.length);
            x = Math.floor(x);
            x=x.toString();
            var lastThree = x.substring(x.length-3);
            var otherNumbers = x.substring(0,x.length-3);
            if(otherNumbers != '')
                lastThree = ',' + lastThree;
            var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;
            return res
        }

        function changesrcqty(res1,res2){
            $.ajax('/production/changesrcqty?qty='+res1+'&id='+res2,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        var datas = data.SourceItems;
                        srcdiv(datas);
                        var srcqty = data.srcqty+" Bags";
                        var srcsum = data.srcsum;

                        $("#totsrcqty").text(srcqty);
                        $("#totsrcamt").text(rupeeformat(srcsum));
                    }
                });
        }

        function changesrcrate(res1,res2){
            $.ajax('/production/changesrcrate?rate='+res1+'&id='+res2,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        var datas = data.SourceItems;
                        srcdiv(datas);
                        var srcqty = data.srcqty+" Bags";
                        var srcsum = data.srcsum;

                        $("#totsrcqty").text(srcqty);
                        $("#totsrcamt").text(rupeeformat(srcsum));
                    }
                });
        }

        function deletesrcitem(res1) {
            $.ajax('/production/deletesrcitem?id='+res1,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        var datas = data.SourceItems;
                        srcdiv(datas);
                        var srcqty = data.srcqty+" Bags";
                        var srcsum = data.srcsum;

                        $("#totsrcqty").text(srcqty);
                        $("#totsrcamt").text(rupeeformat(srcsum));
                    }
                });
        }

        function changedesqty(res1,res2){
            $.ajax('/production/changedesqty?qty='+res1+'&id='+res2,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        var datas = data.DestinationItems;
                        desdiv(datas);
                        var dessum = data.dessum;
                        $("#totdesamt").text(rupeeformat(dessum));
                    }
                });
        }

        function changedesrate(res1,res2){
            $.ajax('/production/changedesrate?rate='+res1+'&id='+res2,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        var datas = data.DestinationItems;
                        desdiv(datas);
                        var dessum = data.dessum;
                        $("#totdesamt").text(rupeeformat(dessum));
                    }
                });
        }

        function deletedesitem(res1) {
            $.ajax('/production/deletedesitem?id='+res1,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        var datas = data.DestinationItems;
                        desdiv(datas);
                        var dessum = data.dessum;
                        $("#totdesamt").text(rupeeformat(dessum));
                    }
                });
        }

        function formsubmit() {
           var next_id = $("#next_id").val();
            $.ajax('/production/formsubmit?next_id='+next_id,   // request url
                {
                    success: function (data, status, xhr) {// success callback function
                        
                    }
                });
        }
    </script>
@endsection


