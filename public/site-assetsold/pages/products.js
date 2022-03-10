var loadingState  = false;
var pageNumber = 1;
var productListUrl = $('#ajax-url').val();
var isNextpage = true;


$(document).ready(function(){
  getProducts();

  $(window).scroll(function() {
    if ($(window).scrollTop() + $(window).height() >= $('.reach-loader').offset().top) {
      getProducts();
    }
  });
});

// Products list
function getProducts()
{
  if(loadingState==false && isNextpage==true)
  {
    $('.reach-loader').show();
    loadingState = true;
    let details = {
      pageNumber:pageNumber,
    };

    $.ajax({
      type:'POST',
      url:productListUrl,        
      data: details,
      success:function(response)
      {
        $('#product-details').append(response.content);
        isNextpage = response.isNextpage;
        loadingState = false;
        $('.reach-loader').hide();
      }
    });
  }
}