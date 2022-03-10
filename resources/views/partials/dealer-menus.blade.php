<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="{{{ url('dealer/dashboard') }}}"> <img alt="image" src="{{ asset('public/dealer-assets/img/logo.png') }}" class="header-logo" /> <span
                class="logo-name">Sakthi Rice</span>
            </a>
          </div>
          <!-- User Details -->
          <?php 
          $User = Auth::guard('dealer')->user();
          $UserDetails = Auth::guard('dealer')->user()->userDetails;
          $FullName = $UserDetails->first_name.' '.$UserDetails->last_name;
          $ProfileImage = asset('public/dealer-assets/img/no-image.png');
          $ProfileRole = "";
          if($UserDetails->profile_image!="" && file_exists(public_path().$UserDetails->profile_image)){
            $ProfileImage = asset('public/'.$UserDetails->profile_image);
          }
          if($User->user_type==1){
            $ProfileRole = "Administrator";
          }else{
            $ProfileRole = "Dealer";
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
          <ul class="sidebar-menu">
            <li><a class="nav-link" href="{{ url('dealer/dashboard') }}"><i data-feather="grid"></i><span>Dashboard</span></a></li>
            <li><a class="nav-link" href="{{ url('dealer/orders') }}"><i data-feather="truck"></i><span>Orders</span></a></li>
          </ul>
        </aside>
      </div>