<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Đăng nhập</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Responsive bootstrap 4 admin template" name="description">
  <meta content="Coderthemes" name="author">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- App faviconc  -->
  <link rel="shortcut icon" href="/images/tvu-logo.webp">

  <!-- App css -->
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet">
  <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css">
  <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet">
</head>

<body class="m-0 p-0">
  <div class="account-pages d-flex align-items-center" style="height:100vh;background-color: #e9ecef !important">
    <div class="container">
      <div class="row justify-content-center align-items-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <div class="card mb-0 rounded-lg">
            <div class="card-body p-5 ">

              <div class="text-center">
                <div class="">
                  <span><img src="/images/tvu-logo.webp" alt="" height="90"></span>
                </div>
              </div>

              <div class="text-center mt-3 mb-4 font-20 font-weight-bold" style="line-height: 1.5 !important">
                HỆ THỐNG ĐÁNH GIÁ <br>NĂNG LỰC HỌC TẬP CỦA SINH VIÊN
              </div>
              
              @if(session('error'))
                <div class="input-group mb-1 text-danger">
                  <b>{{ session('error') }}</b>
                </div>
              @endif

              <form method="POST" action="/dangnhap">
                @csrf
                <div class="form-group mb-3">
                  <input class="form-control" type="text" id="txtTenDN" name="txtTenDN" placeholder="Tên đăng nhập" autocomplete="off">
                </div>
      
                <div class="form-group mb-3">
                  <input class="form-control" type="password" id="txtMatKhau" name="txtMatKhau" placeholder="Mật khẩu" autocomplete="off">
                </div>

                <div class="form-group text-center mb-0">
                  <button class="btn btn-success btn-block waves-effect waves-light" type="submit" id="btnDN" name="btnDN"> Đăng nhập </button>
                </div>
              </form> 
            </div> <!-- end card-body -->
          </div><!-- end card -->
        </div> <!-- end col -->
      </div><!-- end row -->
    </div><!-- end container -->
  </div><!-- end page -->

  <!-- Vendor js -->
  <script src="/assets/js/vendor.min.js"></script>
  <!-- App js -->
  <script src="/assets/js/app.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
    function kiemTraRong(mangID) {
      var result = true;

      mangID.forEach(function(id) {
        var value = $('#' + id).val().trim();

        if (!value) {
          $('#' + id).addClass('border-danger');
          $('#' + id).siblings('.error-text').remove();
          $('#' + id).after(`<div class="error-text text-danger" style="font-size: 12px">
              <i class='fas fa-exclamation-circle mr-1'></i>Vui lòng điền trường này
            </div>`);
          result = false;
        } else {
          $('#' + id).removeClass('border-danger');
          $('#' + id).siblings('.error-text').remove();
        }
      });

      return result;
    }
  </script>
</body>

</html>