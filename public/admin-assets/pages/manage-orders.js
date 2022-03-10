// Orders List
$(document).ready(function(){
  var formData = "";
  var listUrl = $('#order-table').attr('data-url');
  var dataTable = $('#order-table').DataTable({
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
       { data: 'order_id' }, 
       { data: 'user' },
       { data: 'supplier' },
       { data: 'total_amount' },
       { data: 'payment_type' },
       { data: 'payment_status' },
       { data: 'status' },
       { data: 'date_time' },
       { data: 'action' }
    ]
});

$('#SearchForm').on('submit',function(){
  formData =   $(this).serialize();
  $('#order-table').DataTable().draw();
  return false;
});

});



// Status change
$(document).on('change','.orderstatus_change',function(){
  var id        = $(this).attr('data-id');
  var url       = $(this).attr('data-url');
  var status    = $(this).val();
  if(status!=""){
    swal({
      title: 'Are you sure?',
      text: 'Do you want to change the order status',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
          changeStatus(url,id,status);
        }
    });
  }
});


// paymentStatus change
$(document).on('change','.paymentstatus_change',function(){
  var id        = $(this).attr('data-id');
  var url       = $(this).attr('data-url');
  var status    = $(this).val();
  if(status!=""){
    swal({
      title: 'Are you sure?',
      text: 'Do you want to change the payment status',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
          changeStatus(url,id,status);
        }
    });
  }
});

// changeStatus Event
function changeStatus(url,id,status){
  $.ajax({
    type:'POST',
    url:url,        
    data: {orderId:id,status:status},
    success:function(data)
    {
      if(data.success){
          swal('Success !', data.success, 'success');
      }else if(data.error){
          swal('Error !', data.error, 'error');
      }
      $('#order-table').DataTable().ajax.reload();
    }
  });
}



// Create Category
$(document).on('click','#create',function(){
  var url      = $(this).attr('data-url');
  var title    = "Create Category";
  $.ajax({
    type:'GET',
    url:url,
    success:function(data)
    {
      $('#formModal .modal-title').html(title); 
      $('#formModal .modal-body').html(data);
      $('#formModal').modal('show');
    }
  });
});


// Update Category
$(document).on('click','.update-button',function(){
  var id       = $(this).attr('data-id');
  var url      = $(this).attr('data-url')+'/'+id;
  var title    = "Update Category";
  $.ajax({
    type:'GET',
    url:url,
    success:function(data)
    {
      $('#formModal .modal-title').html(title); 
      $('#formModal .modal-body').html(data);
      $('#formModal').modal('show');
    }
  });
});

// Submit create category form
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
      if($.isEmptyObject(data.error))
      {
          $('#formModal').modal('hide');
          $('#category-table').DataTable().ajax.reload();
          swal('Success !', data.success, 'success');
      }
      else
      {
          printErrorMsg(data.error);
      }
    }
  });
    return false;
});

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