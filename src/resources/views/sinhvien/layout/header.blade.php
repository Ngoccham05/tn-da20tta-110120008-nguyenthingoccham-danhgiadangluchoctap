@php
  $currentPath = Request::path();

@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>TVU</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta content="Responsive bootstrap 4 admin template" name="description">
  <meta content="Coderthemes" name="author">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- App favicon -->
  <link rel="shortcut icon" href="/images/tvu-logo.webp">
  <!-- Table datatable css -->
  <link href="/assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <link href="/assets/libs/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <link href="/assets/libs/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <link href="/assets/libs/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css">
  <!-- Notification css (Toastr) -->
  <link href="/assets/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css">
  <!-- select -->
  <link href="/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css">
  <!-- Plugins css -->
  <link href="/assets/libs/dropify/dropify.min.css" rel="stylesheet" type="text/css">
  <!-- App css -->
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet">
  <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css">
  <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet">

  <style>
    #customtable thead tr th,
    #customtable tbody tr td{
      padding: 6px 10px !important;
    }

    #tttable tr th,
    #tttable tr td{
      border: none;
      padding: 6px 10px !important;
    }

    #datatable thead tr th,
    #datatable tbody tr td,
    #selection-datatable thead tr th,
    #selection-datatable tbody tr td{
      padding: 10px !important;
    }

    #datatable tbody tr td:first-child,
    #datatable tbody tr td:last-child,
    #selection-datatable tbody tr td:first-child,
    #selection-datatable tbody tr td:last-child {
      text-align: center;
    } 

    #tableChiTiet tr th{
      padding: 6px 25px 6px 20px !important;
    }

    #select2-slCoVan-container,
    #select2-slBoMon-container{
      color: #495057 !important
    }
  </style>

</head>
<body>
  <!-- Begin page -->
  <div id="wrapper">
    <!-- Topbar Start -->
    <div class="navbar-custom">
      <ul class="list-unstyled topnav-menu float-right mb-0 mr-2">
        <li class="dropdown notification-list">
          <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <span class="d-none d-sm-inline-block ml-1 font-weight-medium">
              @if(Auth::guard('sv')->check())
                Sinh viên | {{$nganh->ten_nganh}} | {{ Auth::guard('sv')->user()->ten_dang_nhap }} 
              @endif
            </span>
            <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
          </a>

          <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
            <!-- item-->
            <a href="/sv/ttcanhan" class="dropdown-item notify-item">
              <i class="mdi mdi-account-outline"></i>
              <span>Thông tin</span>
            </a>

            <!-- item-->
            <a href="/dangxuat" class="dropdown-item notify-item">
              <i class="mdi mdi-logout-variant"></i>
              <span>Đăng xuất</span>
            </a>
          </div>
        </li>
      </ul>

      <!-- LOGO -->
      <div class="logo-box">
        <a href="#" class="logo text-center logo-dark">
          <span class="logo-lg py-2">
            <img src="/images/tvu-logo.webp" alt="" height="70">
          </span>
        </a>
      </div>

      <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
          <button class="button-menu-mobile waves-effect waves-light">
            <i class="mdi mdi-menu"></i>
          </button>
        </li>
    
        <!-- Thanh tìm kiếm -->
        <li class="d-none d-sm-blocke">
          <form class="app-search">
            <div class="app-search-box">
              <div class="input-group">
                <input type="text" class="form-control d-none" placeholder="Tìm kiếm">

                <div class="input-group-append">
                  <button class="btn" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </li>
      </ul>
    </div><!-- end Topbar -->

    <div class="left-side-menu pt-4">
      <div class="">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
          <ul class="metismenu" id="side-menu">
            <!-- <li class="menu-title"></li> -->

            <li>
              <a href="/sv/trangchu">
                <i class="mdi mdi-view-dashboard"></i>
                <span> Trang chủ </span>
              </a>
            </li>

            <li>
              <a href="/sv/ctdaotao">
                <i class="fas fa-book-reader"></i>
                <span> CT đào tạo </span>
              </a>
            </li>            

            <li>
              <a href="/sv/xemdiem">
                <i class="fas fa-chart-bar"></i>
                <span> Xem điểm </span>
              </a>
            </li>

            <li>
              <a href="#">
                <i class="fas fa-chart-line"></i>
                <span> Phân tích </span>
                <span class="menu-arrow"></span>
              </a>
              <ul class="nav-second-level" aria-expanded="false">
                <li><a href="/sv/manhyeu">Năng lực</a></li>
                <li><a href="/sv/goiycaithien">Gợi ý cải thiện</a></li>
              </ul>
            </li>

            <li>
              <a href="/quydinh">
                <i class="fas fa-check-double"></i>
                <span> Các quy định khác </span>
              </a>
            </li>

          </ul>
        </div> <!-- End Sidebar -->

        <div class="clearfix"></div>
      </div><!-- Sidebar -left -->
    </div><!-- Left Sidebar End -->

    <div class="content-page pt-0 px-2">
      <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">