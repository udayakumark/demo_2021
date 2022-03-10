var initiatePage  = false;

// Category List
$(document).ready(function(){
    var formData = "";
    var listUrl = $('#dealer-table').attr('data-url');
    var dataTable = $('#dealer-table').DataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': false, // Remove default Search Control
        'lengthChange':false,
        'pageLength':4,
        'ajax': {
            'url':listUrl,
            'data': function(data){
                data.formData = formData;
            }
        },
        'columns': [
            { data: 'snumber' },
            { data: 'id' },
            { data: 'source_qty' },
            { data: 'source_total' },
            { data: 'destination_total' },
            { data: 'created_at' },
            { data: 'action' }
        ]
    });

    $('#SearchForm').on('submit',function(){
        formData =   $(this).serialize();
        $('#dealer-table').DataTable().draw();
        return false;
    });

});



// Submit create & upadte product form
$(document).on('submit','#form',function(){
    var formData = new FormData($(this)[0]);
    $.ajax({
        type:'POST',
        url:$('#form').attr('action'),
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        success:function(data)
        {
            if($.isEmptyObject(data.error) && $.isEmptyObject(data.singleerror))
            {
                clearErrorMsg();
                swal('Success !', data.success, 'success');
                initiatePage  = true;
            }
            else
            {
                if($.isEmptyObject(data.error))
                {
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display','block');
                    $(".print-error-msg").find("ul").append('<li>'+data.singleerror+'</li>');
                }
                else
                {
                    printErrorMsg(data.error);
                }
            }
        }
    });
    return false;
});



// Delete
$(document).on('click','.delete-button',function(){
    var id       = $(this).attr('data-id');
    var url      = $(this).attr('data-url');
    swal({
        title: 'Are you sure?',
        text: 'Once deleted, you will not be able to recover this data',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            deleteCategory(url,id);
        }
    });
});

// Delete Event
function deleteCategory(url,id){
    $.ajax({
        type:'POST',
        url:url,
        data: {dealerId:id},
        success:function(data)
        {
            if(data.success){
                swal('Success !', data.success, 'success');
            }else if(data.error){
                swal('Error !', data.error, 'error');
            }
            $('#dealer-table').DataTable().ajax.reload();
        }
    });
}

/*Error Display*/
function printErrorMsg (msg)
{
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
    $.each( msg, function( key, value )
    {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
    });
}

/*Clear Errors*/
function clearErrorMsg ()
{
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
}

// After alert click redirect to main page
$(window).click(function(e) {
    if(initiatePage){
        $('.loader').show();
        window.location.replace($('#form').attr('redirect-url'));
        initiatePage  = false;
    }
});
















// Product prices manage scripts

// add new product price rows
$(document).on('click','.priceadd-button',function(){
    addContent();
    serialNumbersUpdate();
});

// remove product price rows
$(document).on('click','.priceremove-button',function(){
    $(this).parent().parent().remove();
    serialNumbersUpdate();
});

// add new row content
function addContent(){
    var snumber = 0;
    $('.product-prices').each(function(){
        snumber++;
    });

    var data = '<tr class="text-center product-prices">';
    data += '<td width="4%" class="snumber">8</td>';
    data += '<td width="8%">KG</td>';
    data += '<td width="20%">';
    data += '<input type="number" name="quantity[]" min="1" step=".01" placeholder="Enter Quantity" class="form-control">';
    data += '</td>';
    data += '<td width="20%">';
    data += '<input type="number" name="originalprice[]" min="1" step=".01" placeholder="Enter Original Price" class="form-control">';
    data += '</td>';
    data += '<td width="20%">';
    data += '<input type="number" name="sellingprice[]" min="1" step=".01" placeholder="Enter Selling Price" class="form-control">';
    data += '</td>';
    data += '<td width="20%">';
    data += '<label class="custom-switch mt-2">';
    data += '<input type="checkbox" name="status'+snumber+'" class="custom-switch-input" value="1" checked>';
    data += '<span class="custom-switch-indicator"></span>';
    data += '</label>';
    data += '</td>';
    data += '<td width="8%">';
    data += '<button type="button" class="btn btn-icon btn-danger priceremove-button">';
    data += '<i class="fas fa-times"></i>';
    data += '</button>';
    data += '</td>';
    data += '</tr>';
    $('#price-data').append(data);
    serialNumbersUpdate();
}

// Serial number data update
function serialNumbersUpdate(){
    var count = 1;
    $('.product-prices').each(function(){
        $(this).find('.snumber').html(count);
        count++;
    });
}