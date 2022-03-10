// Ajax request response loader
$(document).bind("ajaxSend", function(){
  $("#preloader").show();
}).bind("ajaxComplete", function(){
  $("#preloader").hide();
});


// Cart header load cart details
$(document).ready(function(){
  getCartProducts();
  getMobileCartProducts();
});

function getCartProducts()
{
  var CartUrl = $('#cartProductsUrl').val();
    $.ajax({
      type:'POST',
      url:CartUrl,        
      data: [],
      success:function(response)
      {
        $('#headerCartDiv').html(response);
      }
    });
}

function getMobileCartProducts()
{
  var CartUrl = $('#cartMobileProductsUrl').val();
    $.ajax({
      type:'POST',
      url:CartUrl,        
      data: [],
      success:function(response)
      {
        $('#headerCartMobileDiv').html(response);
      }
    });
}

// Add cart products function
$(document).on('click','.addcart-btn',function(){
  var pack_id = $(this).parent().parent().find('.packs').val();
  var quantity = $(this).parent().parent().find('.quantity').val();
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

// Remove cart products function
$(document).on('click','.removecart-btn',function(){
  var cart_id = $(this).attr('cart-id');
  if(cart_id=="")
  {
    ShowAlerts('Error!','Something went wrong.try again.','error',false);
  }
  else
  {
    var removeFromCartUrl = $('#cartProductRemoveUrl').val();
    $.ajax({
      type:'POST',
      url:removeFromCartUrl,        
      data: {cart_id:cart_id},
      success:function(data)
      {
        if($.isEmptyObject(data.error) && $.isEmptyObject(data.errors) && $.isEmptyObject(data.success))
        {
          $("#preloader").show();
          var redirectUrl = $('#invalidAuthUrl').val();
          window.location.replace(redirectUrl);
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
$(document).on('change','.packs',function(){
  var pack_id = $(this).val();
  var priceDetailsUrl = $('#priceDetails').val();
    $.ajax({
      type:'POST',
      url:priceDetailsUrl,        
      data: {pack_id:pack_id},
      success:function(data)
      {
        $('.product_'+data.product_id).find('.product-title a').html(data.product_name);
        $('.product_'+data.product_id).find('.price-sale').html(data.selling_price);
        $('.product_'+data.product_id).find('.regular-price').html(data.original_price);
        $('.product_'+data.product_id).find('.addcart-btn').html(data.cart_status);
      }
    });
});


// Alert message display function
function ShowAlerts(title,text,icon,isReload)
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