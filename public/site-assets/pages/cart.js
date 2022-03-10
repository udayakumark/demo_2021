// Submit register user form
$(document).on('submit','#cart-form',function(){
    var formData = new FormData($(this)[0]);
    $.ajax({
    type:'POST',
    url:$('#cart-form').attr('action'),
    contentType: false,
    processData: false,   
    cache: false,        
    data: formData,
    success:function(data)
    {
        if($.isEmptyObject(data.error) && $.isEmptyObject(data.success))
        {
          var redirectUrl = $('#invalidAuthUrl').val();
          window.location.replace(redirectUrl);
          $("#preloader").show();
        }
        else
        {
          if(data.success)
          {
            ShowAlerts('Success!',data.success,'success',true);
          }
          else
          {
            ShowAlerts('Error!',data.error,'error',false);
          }
        }
    }
  });
    return false;
});