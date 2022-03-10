// Billing city pincode get
$(document).on('change','#billing_city',function(){
  var billing_city = $(this).val();
  $.ajax({
    type:'POST',
    url:$('#getPincodeUrl').val(),    
    data: {city_id:billing_city},
    success:function(data)
    {
        $('#billing_pincode').html(data);
    }
  });
});

// Shipping city pincode get
$(document).on('change','#shipping_city',function(){
  var shipping_city = $(this).val();
  $.ajax({
    type:'POST',
    url:$('#getPincodeUrl').val(),    
    data: {city_id:shipping_city},
    success:function(data)
    {
        $('#shipping_pincode').html(data);
    }
  });
});

// Submit oredr checkout form
$(document).on('submit','#checkout-form',function(){
  if(confirm("Are you sure to place this order?"))
  {
    var formData = new FormData($(this)[0]);
    $.ajax({
    type:'POST',
    url:$('#checkout-form').attr('action'),
    contentType: false,
    processData: false,   
    cache: false,        
    data: formData,
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
          if(data.success && !data.order_id)
          {
            ShowAlerts('Success!',data.success,'success',true);
          }
          else if(data.success && data.order_id)
          {
            clearErrorMsg();
            loadRazorpay(data.order_id,data.amount);
          }
          else
          {
            errorHandle(data);
          }
        }
    }
    });
  }
  return false;
});

// Razorpay payment
function loadRazorpay(order_id,amount)
{
  var total_amount = amount * 100;
  var razorpayKey  = $('#keyCode').val();
  var responseUrl  = $('#responseUrl').val();
  var options      = {
    "key": razorpayKey, // Enter the Key ID generated from the Dashboard
    "amount": total_amount, // Amount is in currency subunits. Default currency is INR. Hence, 10 refers to 1000 paise
    "currency": "INR",
    "name": "Sakthi Rice",
    "description": "Order Payment",
    "image": "https://sakthirice.com/assets/image/s.png",
    "order_id": "", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
    "handler": function (response){
      $.ajax({
        type:'POST',
        url:responseUrl,
        data:{razorpay_payment_id:response.razorpay_payment_id,amount:amount,order_id:order_id},
        success:function(data){
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
    },
    "theme": {
      "color": "#1c2454"
    }
  };
  var rzp1 = new Razorpay(options);
  rzp1.open();
}