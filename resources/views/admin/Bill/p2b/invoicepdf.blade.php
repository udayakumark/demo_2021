<html>
   <head>
      <style>
        body {
			font-family: sans-serif;
         	font-size: 10pt;
        }
         p {	margin: 0pt; }
         table.items {
         border: 0.1mm solid #000000;
         }
		 table {
			 border-collapse: collapse;
			}

         td { vertical-align: top; }
         .items td {
         border-left: 0.1mm solid #000000;
         border-right: 0.1mm solid #000000;
         }
         table thead td { 
			 background-color: #EEEEEE;
         text-align: center;
         border: 0.1mm solid #000000;
         font-variant: small-caps;
		 border-spacing: 0;
         }
         .items td.blanktotal {
         background-color: #EEEEEE;
         border: 0.1mm solid #000000;
         background-color: #FFFFFF;
         border: 0mm none #000000;
         border-top: 0.1mm solid #000000;
         border-right: 0.1mm solid #000000;
         }
         .items td.totals {
         text-align: right;
         border: 0.1mm solid #000000;
         }
         .items td.cost {
         text-align: center;
         }
		table.des-sec td {
			border: 0.1mm solid #888888;
		}
		.company-name {
			text-align: right;
			font-weight: 700;
		} 
		.sign {
			text-align: right;			
		}
      </style>
   </head>
   <body>
      <!--mpdf
         <htmlpageheader name="myheader">
         <table width="100%">
         	<tr>
         <td width="50%" style="color:#0000BB; "><span style="font-weight: bold; font-size: 14pt;">
		</td>
         <td width="50%" style="text-align: right;">Invoice No:
		 <span style="font-weight: bold; font-size: 8pt;">{{ $inv_details['invoice_no'] }} </span>
		</td>
         </tr></table>
         </htmlpageheader>
         <htmlpagefooter name="myfooter">
         <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
         Page {PAGENO} of {nb}
         </div>
         </htmlpagefooter>
         <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
         <sethtmlpagefooter name="myfooter" value="on" />
         mpdf-->
      <div style="text-align: center; font-weight: bold; ">Bill of Supply</div>
	<table width="100%" style="font-family: serif;" cellpadding="10">
		<tr>
			<td width="50%" rowspan="3" style="border: 1px solid #888888; ">
			<br />
			<b> Sri Sakthi & Co </b>
			<br />5, Kalishwari Complex<br />Kamala rice mill st,<br />Gopichettipalayam<br />
			FSSAI No: 12418007000952 <br/>
			GSTIN/UIN: 33BERPS0485R1ZI <br/>
			State Name: Tamil nadu, Code :33
		</td>

			<td width="25%" style="border: 0.1mm solid #888888; ">Invoice No, {{ $inv_details['invoice_no'] }} <br/> e-Way Bill No.</td>
			<td width="25%" style="border: 0.1mm solid #888888; ">Dated <br/><b>{{ date("d/m/Y", strtotime($inv_details['inv_date'])) }}</b></td>
		</tr>
			<tr>
			<td width="25%" style="border: 0.1mm solid #888888; ">Delivery Note </td>
			<td width="25%" style="border: 0.1mm solid #888888; ">Mode/Terms of Payment</td>
		</tr>
		<tr>		
			<td width="25%" style="border: 0.1mm solid #888888; ">Supplier's Ref.</td>
			<td width="25%" style="border: 0.1mm solid #888888; ">Other Reference(s)</td>
		</tr>

		<tr>
			<td width="50%" rowspan="5" style="border: 1px solid #888888; ">
			<span style="font-size: 7pt; color: #555555; font-family: sans;">
			Buyer </span><br />
			<b>{{ $vendor_details['first_name'] }} {{ $vendor_details['last_name'] }} </b>
			<br />{{ $vendor_details['address'] }}<br />{{ $vendor_details['city'] }}<br />{{ $vendor_details['state']}} {{$vendor_details['pincode']}}</td>

			<td width="25%" style="border: 0.1mm solid #888888; ">Buyer Order No </td>
			<td width="25%" style="border: 0.1mm solid #888888; ">Dated </td>
		</tr>
			<tr>
			<td width="25%" style="border: 0.1mm solid #888888; ">Despatch Document No. </td>
			<td width="25%" style="border: 0.1mm solid #888888; ">Delivery Note Date </td>
		</tr>
		<tr>		
			<td width="25%" style="border: 0.1mm solid #888888; ">Despatched through</td>
			<td width="25%" style="border: 0.1mm solid #888888; ">Destination <br/> <b>{{ $vendor_details['city'] }}</b></td>
		</tr>
		<tr>		
			<td width="25%" style="border: 0.1mm solid #888888; ">Bill of Landing/LR-RR No.</td>
			<td width="25%" style="border: 0.1mm solid #888888; ">
			Motor Vihicle No(optinal) <br/> 
			<!-- <b> TN36AJ4989 </b> -->
		 </td>
		</tr>
		<tr>		
			<td colspan="2" style="border: 0.1mm solid #888888; ">Terms of Delivery</td>
		</tr>		
	</table>



	<table class="des-sec items" width="100%" style="font-family: serif;" cellpadding="10">
		<thead>
		<tr>
			<td width="5%">#</td>
			<td width="40%" style="text-align: center;">Description of Goods</td>
			<td style="text-align: center;">HSN/SAC</td>
			<td style="text-align: center;width:100px;">Quantity</td>
			<td style="text-align: center;">Rate</td>
			<td style="text-align: center;">per</td>
			<td style="text-align: center;">Amount</td>
		</tr>
		</thead>
		<?php $i=0; $bag_qty = 0;?>
			@foreach ($salesitems as $vendor)
			<?php $i++; $bag_qty = $bag_qty + $vendor['qty']; ?>
		<tr>
			<td><?php echo $i ?></td>
			<td>
			@foreach ($riceList as $rice)
				{{ $rice['id'] ==$vendor['paddy_id'] ? $rice['name'] : '' }}
			@endforeach

			</td>
			<td>{{ $inv_details['hsn'] }}</td>
			<td><b>{{ $vendor['qty'] }} Bag</b> <br/> (1.500 QTL)</td>
			<td>{{ number_format($vendor['final_price'], 2) }}</td>
			<td>bag</td>
			<td>{{ number_format($vendor['total'],2) }}</td>
		</tr>
			@endforeach
		<tr>
			<td></td>
			<td style="text-align: right;">	Total </td>
			<td></td>
			<td> <b>{{ $bag_qty }} bag</b></td>
			<td></td>
			<td></td>
			<td style="width: 110px;"> <b>₹ {{ number_format($inv_details['sub_total'],2) }}</b>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				Amount Chargeable (in words) <br/>
				<b>Indian Rupees <?php echo AmountInWords($inv_details['sub_total']); ?></b>
			</td>
		</tr>
		<tr>
			<td colspan="6"> HSN/SAC</td>
			<td> Taxable Value</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: right;font-weight: bold;"> Discount </td>
			<td> {{ $inv_details['discount']}}%</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: right;font-weight: bold;"> <b>Total</b></td>
			<td> <b>{{ number_format($inv_details['total'], 2) }} </b></td>
		</tr>

		
		</table>

		<table width="100%" style="font-family: serif; border:1px solid #888888" cellpadding="10" >
		<tr>
			<td colspan="2"> Tax Amount(in words) : <b>NIL</b> </td>			
		</tr>
		<tr>
			<td width="50%">  </td>
			<td colspan="2"> Company's Bank Details <br/>
				Bank Name : <b>K V B Bank </b><br/>
				A/c No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>1023654 5623 2251 2102 </b><br/>
				Branch & IFS Code: <b>Gopichettipalayam & KVBL0001131 </b><br/>
			</td>			
		</tr>
		<tr>
			<td width="50%"> <u>Declaration</u> <br/>
			We declare that this invoice shows the actual price of the goods descriped and that all particulars are ture and correct.
			</td>
			<td style="border: 1px solid #888888; text-align: right;" colspan="2" >
				<span class="company-name"> <b>For Sri Sakthi & Co</b></span>
				<br/><br/><br/><br/>
				<span class="sign"  style="margin-top:200px;"> Authorised Signature</span>
			</td>
		</tr>
	</table>
	       
      <!-- <div style="text-align: center; font-style: italic;">Payment terms: payment due in 30 days</div> -->
      <div style="text-align: center; font-style: italic;">This is a computer generated invoice </div>
   </body>
</html>
<?php

function AmountInWords(float $amount)
{
   $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
   // Check if there is any number after decimal
   $amt_hundred = null;
   $count_length = strlen($num);
   $x = 0;
   $string = array();
   $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
     3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
     7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
     10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
     13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
     16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
     19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
     40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
     70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $x < $count_length ) {
      $get_divider = ($x == 2) ? 10 : 100;
      $amount = floor($num % $get_divider);
      $num = floor($num / $get_divider);
      $x += $get_divider == 10 ? 1 : 2;
      if ($amount) {
       $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
       $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
       $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
       '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
       '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
        }
   else $string[] = null;
   }
   $implode_to_Rupees = implode('', array_reverse($string));
   $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
   " . $change_words[$amount_after_decimal % 10]) . ' Paise' : '';
   return ($implode_to_Rupees ? $implode_to_Rupees . 'Rupees ' : '') . $get_paise;
}


?>