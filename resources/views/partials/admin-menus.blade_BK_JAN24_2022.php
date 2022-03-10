<style>
  .sidebar li .submenu{
    list-style: none;
    margin: 0;
    padding: 0;
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .sidebar .nav-link {
    font-weight: 500;
    color: var(--bs-dark);
  }
  .sidebar .nav-link:hover {
    color: var(--bs-primary);
  }
  i{
    width: 17.34px;
  }
</style>
<script type="text/javascript">

  document.addEventListener("DOMContentLoaded", function(){

    document.querySelectorAll('.sidebar .nav-link').forEach(function(element){

      element.addEventListener('click', function (e) {

        let nextEl = element.nextElementSibling;
        let parentEl  = element.parentElement;

        if(nextEl) {
          e.preventDefault();
          let mycollapse = new bootstrap.Collapse(nextEl);

          if(nextEl.classList.contains('show')){
            mycollapse.hide();
          } else {
            mycollapse.show();
            // find other submenus with class=show
            var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
            // if it exists, then close all of them
            if(opened_submenu){
              new bootstrap.Collapse(opened_submenu);
            }

          }
        }

      });
    })

  });
  // DOMContentLoaded  end
</script>

<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{{ url('admin/dashboard') }}}"> <img alt="image" src="{{ asset('public/admin-assets/img/logo.png') }}" class="header-logo" /> <span
                class="logo-name">Sakthi Rice</span>
            </a>
          </div>
          <!-- User Details -->
          <?php
          $User = Auth::guard('admin')->user();
          $UserDetails = Auth::guard('admin')->user()->userDetails;
          $FullName = $UserDetails->first_name.' '.$UserDetails->last_name;
          $ProfileImage = asset('public/admin-assets/img/no-image.png');
          $ProfileRole = "";
          $user_type = "";
          
          if(isset($User->user_type)){
            $user_type = $User->user_type;
          }
          if($UserDetails->profile_image!="" && file_exists(public_path().$UserDetails->profile_image)){
            $ProfileImage = asset('public/'.$UserDetails->profile_image);
          }
          if($user_type==1){
            $ProfileRole = "Administrator";
          }
          if($user_type==3){
            $ProfileRole = "Shop Dealer";
          }
          ?>
          <div class="sidebar-user">
            <div class="sidebar-user-picture">
              <img alt="image" src="{{ $ProfileImage }}">
            </div>
            <div class="sidebar-user-details">
              <div class="user-name">{{ $FullName }}</div>
              <div class="user-role">{{ $ProfileRole }}</div>
            </div>
          </div>
          <nav class="sidebar py-2 mb-4">
            <ul class="nav flex-column" id="nav_accordion">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('admin/dashboard') }}"> <i data-feather="grid" style="width: 17px"></i><span> Dashboard</span> </a>
              </li>
              <?php if($user_type==1){ ?>
              <li class="nav-item">
                <a class="nav-link" href="#"> <i data-feather="grid" style="width: 17px"></i><span> Master</span> </a>
                <ul class="submenu collapse">
                  <li><a class="nav-link" href="{{ url('admin/accessories') }}"><i data-feather="package" style="width: 17px"></i><span> Accessories Types</span></a></li>
                  <li><a class="nav-link" href="{{ url('admin/bag') }}"><i data-feather="package" style="width: 17px"></i><span> Bag Types</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/bank') }}"><i data-feather="dollar-sign" style="width: 17px"></i><span> Bank</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/bank-payment') }}"><i data-feather="dollar-sign" style="width: 17px"></i><span> Bank Payment</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/update-invoiceno') }}"><i data-feather="package" style="width: 17px"></i> Invoice No. Settings</a> </li>
                    <li><a class="nav-link" href="{{ url('admin/online-payment') }}"><i data-feather="dollar-sign" style="width: 17px"></i><span> Online Payment</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/otherproducts') }}"><i data-feather="package" style="width: 17px"></i><span> Other Products</span></a></li> 
                    <li><a class="nav-link" href="{{ url('admin/paddy') }}"><i data-feather="package" style="width: 17px"></i><span> Paddy Types</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/rice') }}"><i data-feather="package" style="width: 17px"></i><span> Rice Types</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/vendortype') }}"><i data-feather="package" style="width: 17px"></i><span> Vendor Types</span></a></li>
                    <li><a class="nav-link" href="{{ url('admin/warehouses') }}"><i data-feather="truck" style="width: 17px"></i>Warehouses</a> </li>
                </ul>
              </li>
              
              <li class="nav-item">
                <a class="nav-link" href="{{ url('admin/vendors') }}"> <i data-feather="users" style="width: 17px"></i> Vendors</a>
              </li>
              <?php } ?>
              <li class="nav-item">
                <a class="nav-link" href="#"> <i data-feather="grid" style="width: 17px"></i><span> Billing</span> </a>
                <ul class="submenu collapse">
                  <li><a class="nav-link" href="{{ url('admin/b2b/bill') }}"><i data-feather="package" style="width: 17px"></i><span> Rice Mill B2B</span></a></li>
                  <li><a class="nav-link" href="{{ url('admin/b2c/bill') }}"><i data-feather="package" style="width: 17px"></i><span> Rice Mill B2C</span></a></li>
                  <li><a class="nav-link" href="{{ url('admin/bill') }}"><i data-feather="package" style="width: 17px"></i> Other bills</a> </li>
                </ul>
              </li>
              <?php if($user_type == 1 || $user_type == 3 ){ ?>
              <li class="nav-item has-submenu">
                <a class="nav-link" href="#"><i data-feather="shopping-bag" style="width: 17px"></i> Shop <i class="bi small bi-caret-down-fill"></i></a>
                <ul class="submenu collapse">
                  <li><a class="nav-link" href="{{ url('admin/dealers') }}"><i data-feather="shopping-bag" style="width: 17px"></i> Manage Shops</a></li>
                  <!-- <li><a class="nav-link" href="{{ url('admin/pincodes') }}"><i data-feather="map" style="width: 17px"></i> Manage Pincodes</a></li> -->
                </ul>
              </li>
              <?php } ?>
              <li class="nav-item has-submenu">
                <a class="nav-link" href="#"><i data-feather="grid" style="width: 17px"></i> Purchases  <i class="bi small bi-caret-down-fill"></i></a>
                <ul class="submenu collapse">
                   <li><a class="nav-link" href="{{ url('admin/accessories/purchase') }}"><i data-feather="grid" style="width: 17px"></i> Accessories</a></li>
				  <li><a class="nav-link" href="{{ url('admin/bag/purchase') }}"><i data-feather="grid" style="width: 17px"></i> Bag</a></li>
                  <li><a class="nav-link" href="{{ url('admin/purchase') }}"><i data-feather="grid" style="width: 17px"></i> Paddy</a></li>
                  <li><a class="nav-link" href="{{ url('admin/rice/purchase') }}"><i data-feather="grid" style="width: 17px"></i> Rice</a></li>
				  <li><a class="nav-link" href="{{ url('production/get/list') }}"><i data-feather="grid" style="width: 17px"></i> Paddy production own</a></li>
                </ul>
              </li>
              <li class="nav-item has-submenu">
                <a class="nav-link" href="#"><i data-feather="grid" style="width: 17px"></i> Stock  <i class="bi small bi-caret-down-fill"></i></a>
                <ul class="submenu collapse">				  
                  <li><a class="nav-link" href="{{ url('stock/ricestocklist') }}"><i data-feather="grid" style="width: 17px"></i> Rice </a></li>
                  <li><a class="nav-link" href="{{ url('stock/accessoriesstocklist') }}"><i data-feather="grid" style="width: 17px"></i> Accessories </a></li>
                 <li><a class="nav-link" href="{{ url('stock/bagstocklist') }}"><i data-feather="grid" style="width: 17px"></i> Bag</a></li>
                 <li><a class="nav-link" href="{{ url('stock/paddystocklist') }}"><i data-feather="grid" style="width: 17px"></i> Paddy </a></li>
                </ul>
              </li>
              
              <li class="nav-item has-submenu">
                <a class="nav-link" href="#"><i data-feather="truck" style="width: 17px"></i> Orders  </a>
                <ul class="submenu collapse">
                  <li><a class="nav-link" href="{{ url('admin/orders') }}"><i data-feather="truck" style="width: 17px"></i> Online Orders  </a></li>
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ url('admin/cashbook') }}"> <i data-feather="sunset" style="width: 17px"></i> Manage Cashbook </a>
              </li>
            </ul>
          </nav>
{{--          <ul class="sidebar-menu">--}}
{{--            <li><a class="nav-link" href="{{ url('admin/dashboard') }}"><i data-feather="grid"></i><span>Dashboard</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/bill') }}"><i data-feather="package"></i><span>Manage Bill</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/purchase') }}"><i data-feather="shopping-bag"></i><span>Manage Purchase</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/categories') }}"><i data-feather="package"></i><span>Manage Category</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/pincodes') }}"><i data-feather="map"></i><span>Manage Pincodes</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/products') }}"><i data-feather="shopping-bag"></i><span>Manage Products</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/vendors') }}"><i data-feather="award"></i><span>Manage Vendor</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/brokers') }}"><i data-feather="globe"></i><span>Manage Broker</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/paddy') }}"><i data-feather="shopping-bag"></i><span>Manage Paddy</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/warehouses') }}"><i data-feather="sunset"></i><span>Manage Warehouse</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/cashbook') }}"><i data-feather="sunset"></i><span>Manage Cashbook</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/dealers') }}"><i data-feather="users"></i><span>Manage Dealers</span></a></li>--}}
{{--            <li><a class="nav-link" href="{{ url('admin/orders') }}"><i data-feather="truck"></i><span>Orders</span></a></li>--}}
{{--          </ul>--}}
        </aside>
      </div>
