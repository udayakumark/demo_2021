// Submit contact us form
$(document).on('submit','#contactus-form',function(){
    var formData = new FormData($(this)[0]);
    $.ajax({
    type:'POST',
    url:$('#contactus-form').attr('action'),
    contentType: false,
    processData: false,   
    cache: false,        
    data: formData,
    success:function(data)
    {
      if($.isEmptyObject(data.error) && $.isEmptyObject(data.errors) && $.isEmptyObject(data.success))
      {
          clearErrorMsgs();
          window.location.replace(data.redirectUrl);
      }
      else
      {
          if(data.success)
          {
            clearErrorMsgs();
            ShowAlert('Success!',data.success,'success',true);
          }
          else
          {
            errorHandles(data);
          }
      }
    }
  });
    return false;
});



/*************************** Error handle functions start *******************************/

// Alert message display function
function ShowAlert(title,text,icon,isReload)
{
  Swal.fire({
  title: title,
  text: text,
  icon: icon,
  confirmButtonColor: '#3085d6',
  confirmButtonText: 'Ok',
  allowOutsideClick: false
  }).then((result) => {
    if (isReload) {
      $("#preloader").show();
      window.location.reload();
    }
  });
}



// Error Handle
function errorHandles(data)
{
  if(data.error)
  {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
    $(".print-error-msg").find("ul").append('<li>'+data.error+'</li>');
  }
  else
  {
      printErrorMsgs(data.errors);
  }
}


/*Error Display*/
function printErrorMsgs(msg) 
{
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
    $.each( msg, function( key, value ) 
    {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
    });

    // scrollto position
    $('html, body').animate({
        scrollTop: $(".print-error-msg").offset().top
    }, 2000);
}

/*Clear Errors*/
function clearErrorMsgs() 
{
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','none');
}

/*************************** Error handle functions end *******************************/