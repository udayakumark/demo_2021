<!DOCTYPE html>
<html>

<head>
    <title>Bill</title>
    <meta name = "viewport" content = "width = device-width, initial-scale = 1.0">

    <!-- Bootstrap -->
    <link href = "https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel = "stylesheet">
    <link href = "https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js" rel = "stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <![endif]-->

</head>

<body>
<div class="container-fluid">
    <div id="ui-view" data-select2-id="ui-view">
        <div>
            <div class="card">
                <div class="card-header">
                    <strong>Bill of supply </strong>(Triplicate for suppliers)
                    <a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="#" onclick="javascript:window.print();" data-abc="true">
                        <i class="fa fa-print"></i> Print</a>
                    <a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#" data-abc="true">
                        <i class="fa fa-save"></i> Save</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-4">
                            <h6 class="mb-3">From:</h6>
                            <div>
                                <strong>SRI SAKTHI HI-TECH MODERN RICEMILL</strong>
                            </div>
                            <div>137/1b, pachamallai back side road,</div>
                            <div>karrukampalli, gobichettipalayam</div>
                            <div>Tamil Nadu 638476</div>
                            <div>GSTIN/UIN: 33ACFFS3032B1ZS</div>
                            <div>State: Tamil Nadu, Code: 33</div>
                            <div>Email: ricesakthi.c@gmail.com</div>

                        </div>
                        <div class="col-sm-4">
                            <h6 class="mb-3">To:</h6>
                            <div>
                                <strong><?php echo $vendorlist['first_name'].' '.$vendorlist['last_name'] ?></strong>
                            </div>
                            <div>Chennai</div>
                            <div>Email: admin@bbbootstrap.com</div>
                            <div>Phone: +48 123 456 789</div>
                        </div>
                        <div class="col-sm-4">
                            <h6 class="mb-3">Details:</h6>
                            <div>Invoice
                                <strong>#<?php echo $inv_details['invoice_no'] ?></strong>
                            </div>
                            <div> <strong>Date: <?php echo $inv_details['inv_date'] ?> </strong></div>
                            <div>Delivery Note: Mode/Term of payment</div>
                            <div>Supplier`s Ref: Other Reference</div>
                            <div>Buyer`s Order No: Dated</div>
                            <div>Dispatch Document No: Delivery Note date</div>
                            <div>Dispatch Through: Destination</div>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="center">#</th>
                                <th>Item</th>
                                <th class="center">Quantity</th>
                                <th class="right">Unit Cost</th>
                                <th class="right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 0;
                                foreach($salesitems as $salesitem){
                                    $i++;
                                    ?>
                                    <tr>
                                        <td class="center"><?php echo $i  ?></td>
                                        <td class="left"><?php echo $salesitem['product_id']  ?></td>
                                        <td class="center"><?php echo $salesitem['qty']  ?></td>
                                        <td class="right"><?php echo $salesitem['price']  ?></td>
                                        <td class="right"><?php echo $salesitem['total']  ?></td>
                                    </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-5">
                            Company PAN: <strong>ACFFS3032F</strong><br>
                            Bank details: <br>
                            Bank Name             : CANARA BANK,<br>
                            A/C No                : 1236271000158,<br>
                            Branch and IFSC Code  : GOBICHETTIPALAYAM, CNRB0001236
                        </div>
                        <div class="col-lg-4 col-sm-5 ml-auto">
                            <table class="table table-clear">
                                <tbody>
                                <tr>
                                    <td class="left">
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td class="right"><?php echo $inv_details['sub_total']  ?></td>
                                </tr>
                                <tr>
                                    <td class="left">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="right">
                                        <strong><?php echo $inv_details['sub_total']  ?></strong>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer style="text-align: center">
    <p>SUBJECT TO GOBICHETTIPALAYAM JURIDICTION</p>
</footer>
</body>
</html>