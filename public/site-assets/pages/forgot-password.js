// Ajax request response loader
$(document).bind("ajaxSend", function(){
  $("#preloader").show();
}).bind("ajaxComplete", function(){
  $("#preloader").hide();
});

// Submit register user form
$(document).on('submit','#forgotpassword-form',function(){
    var formData = new FormData($(this)[0]);
    $.ajax({
    type:'POST',
    url:$('#forgotpassword-form').attr('action'),
    contentType: false,
    processData: false,   
    cache: false,        
    data: formData,
    success:function(data)
    {
      if(data.success)
      {
          clearErrorMsg();
          ShowAlertsmsg('Success!',data.success,'success',true,data.redirectUrl);
      }
      else
      {
        errorHandle(data);
      }
    }
  });
    return false;
});

// Alert message display function
function ShowAlertsmsg(title,text,icon,isReload,redirectUrl)
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
      if(redirectUrl!==null){
        window.location.replace(redirectUrl);
      }else{
        window.location.reload();
      }
    }
  });
}

// Error Handle
function errorHandle(data)
{
  if(data.error)
  {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
    $(".print-error-msg").find("ul").append('<li>'+data.error+'</li>');
  }
  else
  {
      printErrorMsg(data.errors);
  }
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

    // scrollto position
    $('html, body').animate({
        scrollTop: $(".print-error-msg").offset().top
    }, 2000);
}

/*Clear Errors*/
function clearErrorMsg () 
{
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','none');
}