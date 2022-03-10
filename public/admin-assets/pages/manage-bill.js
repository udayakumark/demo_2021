$(document).ready(function(){



    $("#farm_bill").submit(function (e) {

        e.preventDefault();
        console.log("calling ajax!");

        var formData = new FormData($("#farm_bill")[0]);

        console.log(formData);
        $.ajax({
            url: "ajax_purchasebill.php",
            type: "post",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                //return;
                var status = data.split("|")[0];
                var message = data.split("|")[1];

                if (status.trim() == "SUCCESS")
                {
                    alert(message);
                    window.location.href = "new_purchase.php";
                    return true;
                }
                if (status.trim() == "ERROR") {
                    alert(message);
                }
            }

        });
        return false;
    });


    $("#add_row").click(function(){

        var i = $("#serialno").val();
        var sno = parseInt(i);
        sno = sno + 1;
        // $('#addr'+i).html($('#addr'+b).html()).find('td:first-child').html(i+1);
        $('#addr').append(`<tr id='addr`+i+`'><td>`+sno+`</td>

            <td >
            <input list="browsers" name='product[]' value="" autocomplete="off" class="form-control" id="browser" required>
            </td>
             <td><input type="text" name='qty[]' value="" placeholder='Enter Qty' class="form-control qty" step="0" min="0" required/><input type="hidden" name="purchaseID[]" value=""></td>
             <td><input type="text" name='price[]' placeholder='Enter Unit purchase price' class="form-control price" step="0.00" min="0" value="" required/></td>
             <td><input type="text" name='salesprice[]'  placeholder='Enter Unit salesprice' class="form-control salesprice" step="0.00" min="0" /></td>
             <td><input type="text" name='gst[]' value="0" placeholder='Enter Unit gst' onblur="onblurred()" class="form-control gst" step="0.00" min="0" required/></td>
             <td><input type="text" name='total[]' placeholder='0.00' class="form-control total" readonly/></td>
              <td><a data-id='`+i+`' id="`+i+`" style="color: white" onclick="deletethisrow(this.id)" class="deletethisrow pull-right btn btn-danger">Delete Row</a></td>
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

function onblurred() {
    console.log("blurred");

    var i = $("#serialno").val();
    var sno = parseInt(i);
    sno = sno + 1;
    // $('#addr'+i).html($('#addr'+b).html()).find('td:first-child').html(i+1);
    $('#addr').append(`<tr id='addr`+i+`'><td>`+sno+`</td>

            <td >
            <input list="browsers" name='product[]' value="" autocomplete="off" class="form-control" id="browser" required>
            </td>
             <td><input type="text" name='qty[]' value="" placeholder='Enter Qty' class="form-control qty" step="0" min="0" required/><input type="hidden" name="purchaseID[]" value=""></td>
             <td><input type="text" name='price[]' placeholder='Enter Unit purchase price' class="form-control price" step="0.00" min="0" value="" required/></td>
             <td><input type="text" name='salesprice[]'  placeholder='Enter Unit salesprice' class="form-control salesprice" step="0.00" min="0" /></td>
             <td><input type="text" name='gst[]' value="0" placeholder='Enter Unit gst' onblur="onblurred()" class="form-control gst" step="0.00" min="0" required/></td>
             <td><input type="text" name='total[]' placeholder='0.00' class="form-control total" readonly/></td>
              <td><a data-id='`+i+`' id="`+i+`" style="color: white" onclick="deletethisrow(this.id)" class="deletethisrow pull-right btn btn-danger">Delete Row</a></td>
             </tr>`);

    i++;

    $("#serialno").val(i)
    calc();
}
function calc()
{
    $('#tab_logic tbody tr').each(function(i, element) {
        var html = $(this).html();
        if(html!='')
        {
            var qty = $(this).find('.qty').val();
            var price = $(this).find('.price').val();
            var gst = $(this).find('.gst').val();

            var withouttax = qty*price;

            var withtaxs = withouttax * (gst/100);

            var withtax = parseInt(withouttax)+parseInt(withtaxs);

            $(this).find('.total').val(withtax);

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
