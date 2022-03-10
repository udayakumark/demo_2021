// Pincode List
$(document).ready(function(){
  var formData = "";
  var listUrl = $('#pincode-table').attr('data-url');
  var dataTable = $('#pincode-table').DataTable({
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
       { data: 'city_name' }, 
       { data: 'pincode' },
       { data: 'date_time' },
       { data: 'status' },
       { data: 'action' }
    ]
});

$('#SearchForm').on('submit',function(){
  formData =   $(this).serialize();
  $('#pincode-table').DataTable().draw();
  return false;
});

});



// Create Pincode
$(document).on('click','#create',function(){
  var url      = $(this).attr('data-url');
  var title    = "Create Pincode";
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


// Update Pincode
$(document).on('click','.update-button',function(){
  var id       = $(this).attr('data-id');
  var url      = $(this).attr('data-url')+'/'+id;
  var title    = "Update Pincode";
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

// Submit create pincode form
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
          $('#pincode-table').DataTable().ajax.reload();
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

// Delete Pincode
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
    data: {pincodeId:id},
    success:function(data)
    {
      if(data.success){
          swal('Success !', data.success, 'success');
      }else if(data.error){
          swal('Error !', data.error, 'error');
      }
      $('#pincode-table').DataTable().ajax.reload();
    }
  });
}

// Status Change
$(document).on('change','.pincode',function(){
  var id       = $(this).attr('data-id');
  var url      = $(this).attr('data-url');
  $.ajax({
    type:'POST',
    url:url,        
    data: {pincodeId:id},
    success:function(data)
    {
      if(data.success){
          swal('Success !', data.success, 'success');
      }else if(data.error){
          swal('Error !', data.error, 'error');
      }
      $('#pincode-table').DataTable().ajax.reload();
    }
  });
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