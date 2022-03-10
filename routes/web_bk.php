<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\PincodeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\OnlinePaymentController;
use App\Http\Controllers\Admin\BankPaymentController;
use App\Http\Controllers\Admin\PettyCashController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\BrokerController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\AccessoriesController;
use App\Http\Controllers\Admin\PaddyController;
use App\Http\Controllers\Admin\BagController;
use App\Http\Controllers\Admin\OtherProductsController;
use App\Http\Controllers\Admin\HSNCodeController;
use App\Http\Controllers\Admin\RiceController;
use App\Http\Controllers\Admin\CashbookController;
use App\Http\Controllers\Site\IndexController;
use App\Http\Controllers\Site\ProductsController;
use App\Http\Controllers\Site\MyaccountController;
use App\Http\Controllers\Dealer\DealerloginController;
use App\Http\Controllers\Dealer\DealerdashboardController;
use App\Http\Controllers\Dealer\DealerorderController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\Stock\PaddyStockController;
use App\Http\Controllers\Stock\BagStockController;
use App\Http\Controllers\Stock\AccessoriesStockController;
use App\Http\Controllers\Stock\RiceStockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
|--------------------------------------------------------------------------
| Site Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [IndexController::class,'Index']);
Route::match(['get','post'],'login', [IndexController::class,'Login']);
Route::match(['get','post'],'register', [IndexController::class,'Register']);
Route::match(['get','post'],'forgot-password', [IndexController::class,'Forgotpassword']);
Route::match(['get','post'],'otp-verification/{key}', [IndexController::class,'OtpVerification']);
Route::get('shop', [ProductsController::class,'Shop']);
Route::get('contact-us', [IndexController::class,'ContactUs']);
Route::get('rice-benefits', [IndexController::class,'RiceBenefits']);
Route::get('rice-details', [IndexController::class,'RiceDetails']);
Route::get('gallery', [IndexController::class,'Gallery']);
Route::post('contactus', [IndexController::class,'ContactusSave']);
Route::post('product-list', [ProductsController::class,'ProductsList']);
Route::post('getPriceDetails', [ProductsController::class,'PriceDetails']);
Route::get('product-detail/{id}', [ProductsController::class,'ProductDetail'])->where('id', '[0-9]+');
Route::post('userCartList', [ProductsController::class,'userCartList']);
Route::post('userCartMobileList', [ProductsController::class,'userCartMobileList']);



Route::middleware(['user'])->group(function(){
    // Myaccount routes
    Route::get('myaccount', [MyaccountController::class,'Index']);
    Route::post('myorder-list', [MyaccountController::class,'MyorderList']);
    Route::post('changepassword', [MyaccountController::class,'Changepassword']);
    Route::get('logout', [MyaccountController::class,'Logout']);

    // Products routes
    Route::post('addtoCart', [ProductsController::class,'AddtoCart']);
    Route::post('removeFromCart', [ProductsController::class,'RemoveFormCart']);
    Route::get('cart', [ProductsController::class,'CartList']);
    Route::post('updateCart', [ProductsController::class,'UpdateCart']);
    Route::get('checkout', [ProductsController::class,'Checkout']);
    Route::post('placeOrder', [ProductsController::class,'PlaceOrder']);
    Route::post('getPincode', [ProductsController::class,'GetPincode']);
    Route::post('razorpayResponse', [ProductsController::class, 'razorpayResponse']);
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::match(['get','post'],'admin', [LoginController::class,'index']);
Route::match(['get','post'],'admin/forgot-pwd', [LoginController::class,'Forgotpassword']);
// Route::match(['get','post'],'otp-verification/{key}', [IndexController::class,'OtpVerification']);


Route::middleware(['admin'])->group(function(){

    Route::get('admin/dashboard', [DashboardController::class,'index']);
    Route::match(['get','post'],'admin/changepassword', [DashboardController::class,'changepassword']);
    Route::get('admin/logout', [LoginController::class,'logout']);

    // Manage Categories
    Route::get('admin/categories', [CategoryController::class,'Index']);
    Route::post('admin/category-list', [CategoryController::class,'List']);
    Route::match(['get','post'],'admin/create-category', [CategoryController::class,'Create']);
    Route::match(['get','post'],'admin/update-category/{id}', [CategoryController::class,'Update']);
    Route::post('admin/delete-category', [CategoryController::class,'Delete']);

    // Manage Products
    Route::get('admin/products', [ProductController::class,'Index']);
    Route::post('admin/product-list', [ProductController::class,'List']);
    Route::match(['get','post'],'admin/create-product', [ProductController::class,'Create']);
    Route::match(['get','post'],'admin/update-product/{id}', [ProductController::class,'Update']);
    Route::post('admin/delete-product', [ProductController::class,'Delete']);

    // Manage Dealers
    Route::get('admin/dealers', [DealerController::class,'Index']);
    Route::post('admin/dealer-list', [DealerController::class,'List']);
    Route::match(['get','post'],'admin/create-dealer', [DealerController::class,'Create']);
    Route::match(['get','post'],'admin/update-dealer/{id}', [DealerController::class,'Update']);
    Route::post('admin/delete-dealer', [DealerController::class,'Delete']);

    // Manage Pincodes
    Route::get('admin/pincodes', [PincodeController::class,'Index']);
    Route::post('admin/pincode-list', [PincodeController::class,'List']);
    Route::match(['get','post'],'admin/create-pincode', [PincodeController::class,'Create']);
    Route::match(['get','post'],'admin/update-pincode/{id}', [PincodeController::class,'Update']);
    Route::post('admin/delete-pincode', [PincodeController::class,'Delete']);
    Route::post('admin/changestatus-pincode', [PincodeController::class,'ChangeStatus']);

    // Orders List
    Route::get('admin/orders', [OrderController::class,'Index']);
    Route::post('admin/order-list', [OrderController::class,'List']);
    Route::post('admin/changestatus-order', [OrderController::class,'ChangeStatus']);
    Route::post('dealer/changepaymentstatus-order', [OrderController::class,'ChangepaymentStatus']);
    Route::get('admin/view-order', [OrderController::class,'ViewOrder']);

    //Bill List
    Route::get('admin/bill', [BillController::class,'Index']);
    Route::post('bill/add',  [BillController::class,'bill_add']);
    Route::get('bill/print',  [BillController::class,'bill_print']);
	
	//Purchase Accessories
    Route::get('admin/accessories/purchase', [PurchaseController::class,'AccessoriesIndex']);
	Route::post('accessories/purchase/list',  [PurchaseController::class,'AccessoriesList']);
	Route::match(['get','post'],'accessories/purchase/add/', [PurchaseController::class,'AccessoriesAdd']);
	Route::match(['get','post'],'admin/accessories/purchase-view/{purchase_id}', [PurchaseController::class,'AccessoriesView']);
	Route::match(['get','post'],'admin/accessories/purchase-edit/{purchase_id}', [PurchaseController::class,'AccessoriesEdit']);
	Route::post('admin/accessories-delete', [PurchaseController::class,'AccessoriesDelete']);
    Route::get('accessories/purchase/getVendordetails',  [PurchaseController::class,'getVendordetailsaccessories']);
	
	 //Purchase Bag
    Route::get('admin/bag/purchase', [PurchaseController::class,'BagIndex']);
	Route::post('bag/purchase/list',  [PurchaseController::class,'BagList']);
	Route::match(['get','post'],'bag/purchase/add/', [PurchaseController::class,'BagAdd']);
	Route::match(['get','post'],'admin/bag/purchase-view/{purchase_id}', [PurchaseController::class,'BagView']);
	Route::match(['get','post'],'admin/bag/purchase-edit/{purchase_id}', [PurchaseController::class,'BagEdit']);
	Route::post('admin/bag-delete', [PurchaseController::class,'BagDelete']);
    Route::get('bag/purchase/getVendordetails',  [PurchaseController::class,'getVendordetailsbag']);
    Route::get('bag/purchase/getRicedetails',  [PurchaseController::class,'getRicedetailsbag']);
	 
    //Purchase  Paddy
    Route::get('admin/purchase', [PurchaseController::class,'Index']);
	Route::post('purchase/list',  [PurchaseController::class,'PaddyList']);
	Route::match(['get','post'],'purchase/add', [PurchaseController::class,'PaddyAdd']);
	Route::match(['get','post'],'admin/purchase-view/{purchase_id}', [PurchaseController::class,'PaddyView']);
	Route::match(['get','post'],'admin/purchase-edit/{purchase_id}', [PurchaseController::class,'PaddyEdit']);
	Route::post('admin/purchase-delete', [PurchaseController::class,'PaddyDelete']);
	Route::get('purchase/print',  [PurchaseController::class,'purchase_print']);
    Route::get('purchase/getVendordetails',  [PurchaseController::class,'getVendordetails']);

    //Purchase Rice
    Route::get('admin/rice/purchase', [PurchaseController::class,'RiceIndex']);
	Route::post('rice/purchase/list',  [PurchaseController::class,'RiceList']);
	Route::match(['get','post'],'rice/purchase/add/', [PurchaseController::class,'RiceAdd']);
	Route::match(['get','post'],'admin/rice/purchase-view/{purchase_id}', [PurchaseController::class,'RiceView']);
	Route::match(['get','post'],'admin/rice/purchase-edit/{purchase_id}', [PurchaseController::class,'RiceEdit']);
	Route::post('admin/rice-delete', [PurchaseController::class,'RiceDelete']);
    Route::get('rice/purchase/getVendordetails',  [PurchaseController::class,'getVendordetailsrice']);
	
	Route::get('admin/production', [PurchaseController::class,'production']);
    Route::get('production/getitemlist',  [PurchaseController::class,'getitemlist']);
    Route::get('production/addsource',  [PurchaseController::class,'addsrcitems']);
    Route::get('production/adddestination',  [PurchaseController::class,'adddesitems']);
    Route::get('production/changesrcqty',  [PurchaseController::class,'changesrcqty']);
    Route::get('production/deletesrcitem',  [PurchaseController::class,'deletesrcitem']);
    Route::get('production/changedesqty',  [PurchaseController::class,'changedesqty']);
    Route::get('production/deletedesitem',  [PurchaseController::class,'deletedesitem']);
    Route::get('production/changesrcrate',  [PurchaseController::class,'changesrcrate']);
    Route::get('production/changedesrate',  [PurchaseController::class,'changedesrate']);
    Route::get('production/formsubmit',  [PurchaseController::class,'formsubmit']);
    Route::post('production/list',  [PurchaseController::class,'productionList']);
    Route::get('production/get/list',  [PurchaseController::class,'productiongetList']);
    Route::match(['get','post'],'admin/production-view/{next_id}', [PurchaseController::class,'productionview']);
	
	//Manage Accessories
    Route::get('admin/accessories', [AccessoriesController::class,'Index']);
    Route::post('admin/accessories-list', [AccessoriesController::class,'List']);
    Route::match(['get','post'],'admin/create-accessories', [AccessoriesController::class,'Create']);
    Route::match(['get','post'],'admin/update-accessories/{id}', [AccessoriesController::class,'Update']);
    Route::post('admin/delete-accessories', [AccessoriesController::class,'Delete']);

    //Manage OtherProducts
    Route::get('admin/otherproducts', [OtherProductsController::class,'Index']);
    Route::post('admin/otherproducts-list', [OtherProductsController::class,'List']);
    Route::match(['get','post'],'admin/create-otherproducts', [OtherProductsController::class,'Create']);
    Route::match(['get','post'],'admin/update-otherproducts/{id}', [OtherProductsController::class,'Update']);
    Route::post('admin/delete-otherproducts', [OtherProductsController::class,'Delete']);
	
	//Manage HSNCode
    Route::get('admin/hsncode', [HSNCodeController::class,'Index']);
    Route::post('admin/hsncode-list', [HSNCodeController::class,'List']);
    Route::match(['get','post'],'admin/create-hsncode', [HSNCodeController::class,'Create']);
    Route::match(['get','post'],'admin/update-hsncode/{id}', [HSNCodeController::class,'Update']);
    Route::post('admin/delete-hsncode', [HSNCodeController::class,'Delete']);
	
	//Bill B2b
    Route::get('admin/b2b/bill', [BillController::class,'B2bIndex']);
	Route::post('b2b/bill/list',  [BillController::class,'B2bList']);
	Route::match(['get','post'],'b2b/bill/add/', [BillController::class,'B2bAdd']);
	Route::match(['get','post'],'admin/b2b/bill-view/{bill_id}', [BillController::class,'B2bView']);
	Route::match(['get','post'],'admin/b2b/bill-edit/{bill_id}', [BillController::class,'B2bEdit']);
	Route::post('admin/b2b-delete', [BillController::class,'B2bDelete']);
    Route::get('admin/b2b/bill-print/{bill_id}',  [BillController::class,'B2bPrint']);
	Route::get('b2b/bill/getVendordetails',  [BillController::class,'getVendordetailsb2b']);
    Route::get('b2b/bill/getRicedetails/{rice_id}',  [BillController::class,'getRicedetailsb2b']);
	
	//Bill B2bd
    Route::get('admin/b2bd/bill', [BillController::class,'B2bdIndex']);
	Route::post('b2bd/bill/list',  [BillController::class,'B2bdList']);
	Route::match(['get','post'],'b2bd/bill/add/', [BillController::class,'B2bdAdd']);
	Route::match(['get','post'],'admin/b2bd/bill-view/{bill_id}', [BillController::class,'B2bdView']);
	Route::match(['get','post'],'admin/b2bd/bill-edit/{bill_id}', [BillController::class,'B2bdEdit']);
	Route::post('admin/b2bd-delete', [BillController::class,'B2bdDelete']);
	Route::get('admin/b2bd/bill-print/{bill_id}',  [BillController::class,'B2bdPrint']);
	
	//Bill B2c
    Route::get('admin/b2c/bill', [BillController::class,'B2cIndex']);
	Route::post('b2c/bill/list',  [BillController::class,'B2cList']);
	Route::match(['get','post'],'b2c/bill/add/', [BillController::class,'B2cAdd']);
	Route::match(['get','post'],'admin/b2c/bill-view/{bill_id}', [BillController::class,'B2cView']);
	Route::match(['get','post'],'admin/b2c/bill-edit/{bill_id}', [BillController::class,'B2cEdit']);
	Route::post('admin/b2c-delete', [BillController::class,'B2cDelete']);
	Route::get('admin/b2c/bill-print/{bill_id}',  [BillController::class,'B2cPrint']);
    Route::get('b2c/bill/getVendordetails',  [BillController::class,'getVendordetailsb2c']);
    Route::get('b2c/bill/getRicedetails/{rice_id}',  [BillController::class,'getRicedetailsb2c']);
	
	//Bill B2cd
    Route::get('admin/b2cd/bill', [BillController::class,'B2cdIndex']);
	Route::post('b2cd/bill/list',  [BillController::class,'B2cdList']);
	Route::match(['get','post'],'b2cd/bill/add/', [BillController::class,'B2cdAdd']);
	Route::match(['get','post'],'admin/b2cd/bill-view/{bill_id}', [BillController::class,'B2cdView']);
	Route::match(['get','post'],'admin/b2cd/bill-edit/{bill_id}', [BillController::class,'B2cdEdit']);
	Route::post('admin/b2cd-delete', [BillController::class,'B2cdDelete']);
	Route::get('admin/b2cd/bill-print/{bill_id}',  [BillController::class,'B2cdPrint']);
	
	//Bill O2b
    Route::get('admin/o2b/bill', [BillController::class,'O2bIndex']);
	Route::post('o2b/bill/list',  [BillController::class,'O2bList']);
	Route::match(['get','post'],'o2b/bill/add/', [BillController::class,'O2bAdd']);
	Route::match(['get','post'],'admin/o2b/bill-view/{bill_id}', [BillController::class,'O2bView']);
	Route::match(['get','post'],'admin/o2b/bill-edit/{bill_id}', [BillController::class,'O2bEdit']);
	Route::post('admin/o2b-delete', [BillController::class,'O2bDelete']);
    Route::get('admin/o2b/bill-print/{bill_id}',  [BillController::class,'O2bPrint']);
	Route::get('o2b/bill/getVendordetails',  [BillController::class,'getVendordetailso2b']);
    Route::get('o2b/bill/getRicedetails/{rice_id}',  [BillController::class,'getRicedetailso2b']);
	
	//Bill O2bd
    Route::get('admin/o2bd/bill', [BillController::class,'O2bdIndex']);
	Route::post('o2bd/bill/list',  [BillController::class,'O2bdList']);
	Route::match(['get','post'],'o2bd/bill/add/', [BillController::class,'O2bdAdd']);
	Route::match(['get','post'],'admin/o2bd/bill-view/{bill_id}', [BillController::class,'O2bdView']);
	Route::match(['get','post'],'admin/o2bd/bill-edit/{bill_id}', [BillController::class,'O2bdEdit']);
	Route::post('admin/o2bd-delete', [BillController::class,'O2bdDelete']);
    Route::get('admin/o2bd/bill-print/{bill_id}',  [BillController::class,'O2bdPrint']);
	
    //Manage Vendors
    Route::get('admin/vendors', [VendorController::class,'Index']);
    Route::post('admin/vendor-list', [VendorController::class,'List']);
    Route::match(['get','post'],'admin/create-vendor', [VendorController::class,'Create']);
    Route::match(['get','post'],'admin/update-vendor/{id}', [VendorController::class,'Update']);
    Route::match(['get','post'],'admin/view-vendor/{id}', [VendorController::class,'View']);
    Route::post('admin/delete-vendor', [VendorController::class,'Delete']);
    Route::get('admin/vendor/getPincodedetails',  [VendorController::class,'getPincodedetails']);
    
     //Manage Broker
    Route::get('admin/brokers', [BrokerController::class,'Index']);
    Route::post('admin/broker-list', [BrokerController::class,'List']);
    Route::match(['get','post'],'admin/create-broker', [BrokerController::class,'Create']);
    Route::match(['get','post'],'admin/update-broker/{id}', [BrokerController::class,'Update']);
    Route::post('admin/delete-broker', [BrokerController::class,'Delete']);
    
     //Manage Warehouse
    Route::get('admin/warehouses', [WarehouseController::class,'Index']);
    Route::post('admin/warehouse-list', [WarehouseController::class,'List']);
    Route::match(['get','post'],'admin/create-warehouse', [WarehouseController::class,'Create']);
    Route::match(['get','post'],'admin/update-warehouse/{id}', [WarehouseController::class,'Update']);
    Route::post('admin/delete-warehouse', [WarehouseController::class,'Delete']);
    
    // Paddy Stock
    Route::match(['get','post'],'stock/paddystocklist', [PaddyStockController::class,'PaddyFilter']);
    Route::match(['get','post'],'stock/bagstocklist', [BagStockController::class,'BagFilter']);
    Route::match(['get','post'],'stock/accessoriesstocklist', [AccessoriesStockController::class,'AccessoriesFilter']);
    Route::match(['get','post'],'stock/ricestocklist', [RiceStockController::class,'RiceFilter']);

    //Manage paddy
    Route::get('admin/paddy', [PaddyController::class,'Index']);
    Route::post('admin/paddy-list', [PaddyController::class,'List']);
    Route::match(['get','post'],'admin/create-paddy', [PaddyController::class,'Create']);
    Route::match(['get','post'],'admin/update-paddy/{id}', [PaddyController::class,'Update']);
    Route::post('admin/delete-paddy', [PaddyController::class,'Delete']);

    //Manage vendortype
    Route::get('admin/vendortype', [MasterController::class,'Index']);
    Route::post('admin/vendortype-list', [MasterController::class,'List']);
    Route::match(['get','post'],'admin/create-vendortype', [MasterController::class,'Create']);
    Route::match(['get','post'],'admin/update-vendortype/{id}', [MasterController::class,'Update']);
    Route::post('admin/delete-vendortype', [MasterController::class,'Delete']);
	
	Route::match(['get','post'],'admin/update-invoiceno/', [MasterController::class,'UpdateInvoice']);
	
	//Manage bag
    Route::get('admin/bag', [BagController::class,'Index']);
    Route::post('admin/bag-list', [BagController::class,'List']);
    Route::match(['get','post'],'admin/create-bag', [BagController::class,'Create']);
    Route::match(['get','post'],'admin/update-bag/{id}', [BagController::class,'Update']);
    Route::post('admin/delete-bag', [BagController::class,'Delete']);

	//Manage rice
    Route::get('admin/rice', [RiceController::class,'Index']);
    Route::post('admin/rice-list', [RiceController::class,'List']);
    Route::match(['get','post'],'admin/create-rice', [RiceController::class,'Create']);
    Route::match(['get','post'],'admin/update-rice/{id}', [RiceController::class,'Update']);
    Route::match(['get','post'],'admin/view-rice/{id}', [RiceController::class,'View']);
    Route::post('admin/delete-rice', [RiceController::class,'Delete']);
	
	//Manage Bank    
    Route::get('admin/bank', [BankController::class,'Index']);
    Route::post('admin/bank-list', [BankController::class,'List']);
    Route::get('admin/bank-view/{id}', [BankController::class,'View']);    
    Route::match(['get','post'],'admin/create-bank', [BankController::class,'Create']);
    Route::match(['get','post'],'admin/update-bank/{id}', [BankController::class,'Update']);
    Route::post('admin/delete-bank', [BankController::class,'Delete']);

    //Manage Online Payment   
    Route::get('admin/online-payment', [OnlinePaymentController::class,'Index']);
    Route::post('admin/online-payment-list', [OnlinePaymentController::class,'List']);
    // Route::get('admin/bank-view/{id}', [OnlinePaymentController::class,'View']);    
    Route::match(['get','post'],'admin/create-online-payment', [OnlinePaymentController::class,'Create']);
    Route::match(['get','post'],'admin/update-online-payment/{id}', [OnlinePaymentController::class,'Update']);
    Route::post('admin/delete-online-payment', [OnlinePaymentController::class,'Delete']);

    //Manage Bank Payment   
    Route::get('admin/bank-payment', [BankPaymentController::class,'Index']);
    Route::post('admin/bank-payment-list', [BankPaymentController::class,'List']);
    // Route::get('admin/bank-payment-view/{id}', [BankPaymentController::class,'View']);    
    Route::match(['get','post'],'admin/create-bank-payment', [BankPaymentController::class,'Create']);
    Route::match(['get','post'],'admin/update-bank-payment/{id}', [BankPaymentController::class,'Update']);
    Route::post('admin/delete-bank-payment', [BankPaymentController::class,'Delete']);
    
    //Manage Petty Cash   
    Route::get('admin/petty-cash', [PettyCashController::class,'Index']);
    Route::post('admin/petty-cash-list', [PettyCashController::class,'List']);   
    Route::match(['get','post'],'admin/create-petty-cash', [PettyCashController::class,'Create']);
    Route::match(['get','post'],'admin/update-petty-cash/{id}', [PettyCashController::class,'Update']);
    Route::post('admin/delete-petty-cash', [PettyCashController::class,'Delete']);

	
    //Manage cashbook
    Route::get('admin/cashbook', [CashbookController::class,'Index']);
    Route::post('admin/cashbook-lists', [CashbookController::class,'List']);
    Route::match(['get','post'],'admin/create-cashbook', [CashbookController::class,'Create']);
    Route::match(['get','post'],'admin/update-cashbook/{id}', [CashbookController::class,'Update']);
    Route::post('admin/delete-cashbook', [CashbookController::class,'Delete']);


});



/*
|--------------------------------------------------------------------------
| Dealer Routes
|--------------------------------------------------------------------------
*/

Route::match(['get','post'],'dealer', [DealerloginController::class,'index']);
Route::middleware(['dealer'])->group(function(){

    Route::get('dealer/dashboard', [DealerdashboardController::class,'index']);
    Route::match(['get','post'],'dealer/changepassword', [DealerdashboardController::class,'changepassword']);
    Route::get('dealer/logout', [DealerloginController::class,'logout']);

    // Orders List
    Route::get('dealer/orders', [DealerorderController::class,'Index']);
    Route::post('dealer/order-list', [DealerorderController::class,'List']);
    Route::post('dealer/changestatus-order', [DealerorderController::class,'ChangeStatus']);
    Route::post('dealer/changepaymentstatus-order', [DealerorderController::class,'ChangepaymentStatus']);
    Route::get('dealer/view-order', [DealerorderController::class,'ViewOrder']);
});


/*
|--------------------------------------------------------------------------
| POS Routes
|--------------------------------------------------------------------------
*/
Route::get('pos/dashboard', 'App\Http\Controllers\Pos\PosController@index');
