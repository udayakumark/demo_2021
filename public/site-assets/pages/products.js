var loadingState  = false;
var pageNumber = 1;
var productListUrl = $('#ajax-url').val();
var isNextpage = true;
var sortBy = 1;
var productName = "";
var productCategory = "";


$(document).ready(function(){
  getProducts();

  $(window).scroll(function() {
    if ($(window).scrollTop() + $(window).height() >= $('.reach-loader').offset().top) {
      getProducts();
    }
  });

  $('#search').on('click',function(){
    $('.product-grids').html('');
    $('.product-lists').html('');
    isNextpage = true;
    sortBy = $('#order_filter').val();
    productName = $('#product_name').val();
    productCategory = $('#product_category').val();
    pageNumber = 1;
    getProducts();
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
      sortBy:sortBy,
      productName:productName,
      productCategory:productCategory
    };

    $.ajax({
      type:'POST',
      url:productListUrl,        
      data: details,
      success:function(response)
      {
        $('.product-grids').append(response.gridContent);
        $('.product-lists').append(response.listContent);
        $('#shop-pagination').html(response.pagination);
        isNextpage = response.isNextpage;
        loadingState = false;
        $('.reach-loader').hide();
      }
    });
  }
}