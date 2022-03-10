// Add cart products function
$(document).on('click','.addcart-button',function(){
  var pack_id = $(this).parent().parent().find('#packs').val();
  var quantity = $(this).parent().parent().find('#quantity').val();
  if(pack_id=="" || quantity=="")
  {
    ShowAlerts('Error!','Something went wrong.try again.','error',false);
  }
  else
  {
    var addtoCartUrl = $('#cartProductAddUrl').val();
    $.ajax({
      type:'POST',
      url:addtoCartUrl,        
      data: {pack:pack_id,quantity:quantity},
      success:function(data)
      {
        if($.isEmptyObject(data.error) && $.isEmptyObject(data.errors) && $.isEmptyObject(data.success))
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
            errorHandle(data);
          }
        }
      }
    });
  }
}); 

// Get product price details
$(document).on('change','#packs',function(){
  var pack_id = $(this).val();
  var priceDetailsUrl = $('#priceDetails').val();
    $.ajax({
      type:'POST',
      url:priceDetailsUrl,        
      data: {pack_id:pack_id},
      success:function(data)
      {
        $('#product-title').html(data.product_name);
        $('#selling_price').html(data.selling_price);
        $('#original_price').html(data.original_price);
        $('#discount_percentage').html(data.discount_percentage);
        $('#cart_button').html(data.cart_status);
      }
    });
});