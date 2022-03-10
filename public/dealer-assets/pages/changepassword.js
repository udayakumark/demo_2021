// Submit change password form
$(document).on('submit','#changepassword-form',function(){
    var formData = new FormData($(this)[0]);
    $.ajax({
    type:'POST',
    url:$('#changepassword-form').attr('action'),
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
    window.location.replace($('#changepassword-form').attr('redirect-url'));
    initiatePage  = false;
  }
});