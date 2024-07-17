@include('sinhvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Sinh viên</a></li>
          <li class="breadcrumb-item active">Thông tin cá nhân</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="row">
        <div class="col-6">
          <div class="d-flex align-items-center mb-3">
            <h4 class="header-title font-18 m-0 mr-auto">Thông tin cá nhân</h4>
          </div>
          <table id="tttable" class="table" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <tr><th>Mã sinh viên: </th><td>{{$tt->ma_sinh_vien}}</td></tr>
            <tr><th>Họ tên: </th><td>{{$tt->ho_ten}}</td></tr>
            <tr><th>Ngày sinh: </th><td id="ctngaySinh">{{ $tt->ngay_sinh ? \Carbon\Carbon::createFromTimestamp($tt->ngay_sinh)->format('d/m/Y') : '' }}</td></tr>
            <tr><th>Giới tính: </th><td id="ctgioi">{{$tt->gioi_tinh}}</td></tr>
            <tr><th>Số điện thoại: </th><td id="ctsdt">{{$tt->so_dien_thoai}}</td></tr>
            <tr><th>Email: </th><td id="ctemail">{{$tt->email}}</td></tr>
            <tr><th>Địa chỉ: </th><td id="ctdiaChi">{{$tt->dia_chi}}</td></tr>
          </table> 
        </div>

        <div class="col-6">
          <div class="d-flex align-items-center mb-3">
            <h4 class="header-title font-18 m-0 mr-auto">Thông tin khóa học</h4>
          </div>
          <table id="tttable" class="table" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <tr><th>Mã lớp: </th><td>{{$tt->ma_lop}}</td></tr>
            <tr><th>Tên lớp: </th><td>{{$tt->ten_lop}}</td></tr>
            <tr><th>Ngành: </th><td>{{$tt->ten_nganh}}</td></tr>
            <tr><th>Số học kỳ: </th><td>{{$so_hk}}</td></tr>
          </table>
        </div>
      </div>
      <div class="row mt-5">
        <div class="col-12 text-right" id="btnGroup">
          <a href="#" class="btn btn-primary py-1 px-2 mr-2" style="font-size: 14px" id="btnSuaTTCaNhan"
            onclick="formSua('{{$tt->gioi_tinh}}', '{{$tt->ngay_sinh}}', '{{$tt->dia_chi}}', '{{$tt->so_dien_thoai}}', '{{$tt->email}}')">
            <i class="fas fa-pen mr-1"></i> Cập nhật thông tin
          </a>
          <a href="#" class="btn btn-primary py-1 px-2" style="font-size: 14px" data-toggle="modal" data-target="#modalDoiMK">
            <i class="fas fa-lock mr-1"></i> Đổi mật khẩu
          </a>
        </div>
      </div>
      
    </div>
  </div>
</div> <!-- end row -->

<div id="modalSua" class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Cập nhật thông tin cá nhân</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mx-2">
        <div class="row">
          <label for="txtMaSV" class="col-form-label col-3 pt-2">Giới tính:</label>
          <div class="d-flex w-100 col-9 py-2">
            <div class="custom-control custom-radio ml-3 mr-5">
              <input type="radio" id="rdNam" name="rdGioi" value="Nam" class="custom-control-input" checked>
              <label class="custom-control-label" for="rdNam">Nam</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="rdNu" name="rdGioi" value="Nữ" class="custom-control-input">
              <label class="custom-control-label" for="rdNu">Nữ</label>
            </div>
          </div>   
          <span id="span_loi"></span>
        </div>

        <div class="row mt-3">
          <label for="txtNgaySinh" class="col-form-label col-3 pt-2">Ngày sinh:</label>
          <div class="w-100 col-9">
            <input type="date" class="form-control bg-white" id="txtNgaySinh" name="txtNgaySinh" autocomplete="off" max="<?php echo date('Y-m-d'); ?>">
          </div>
        </div>

        <div class="row mt-3">
          <label for="txtDiaChi" class="col-form-label col-3 pt-2">Địa chỉ:</label>
          <div class="w-100 col-9">
            <input type="text" class="form-control bg-white" id="txtDiaChi" name="txtDiaChi" autocomplete="off">
          </div>
        </div>

        <div class="row mt-3">
          <label for="txtSDT" class="col-form-label col-3 pt-2">Số điện thoại:</label>
          <div class="w-100 col-9">
            <input type="text" class="form-control bg-white" id="txtSDT" name="txtSDT" autocomplete="off">
          </div>
        </div>

        <div class="row mt-3">
          <label for="txtEmail" class="col-form-label col-3 pt-2">Email:</label>
          <div class="w-100 col-9">
            <input type="text" class="form-control bg-white" id="txtEmail" name="txtEmail" autocomplete="off">
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaTT()">Lưu</button>
      </div>
    </div>
  </div>
</div>

<div id="modalDoiMK" class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Đổi mật khẩu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mx-2">
        <div class="row">
          <label for="txtMKCu" class="col-form-label col-4 pt-2">Mật khẩu hiện tại:</label>
          <div class="w-100 col-8">
            <input type="text" class="form-control bg-white" id="txtMKCu" name="txtMKCu" autocomplete="off">
          </div>
        </div>

        <div class="row mt-3">
          <label for="txtMKMoi" class="col-form-label col-4 pt-2">Mật khẩu mới:</label>
          <div class="w-100 col-8">
            <input type="text" class="form-control bg-white" id="txtMKMoi" name="txtMKMoi" autocomplete="off">
          </div>
        </div>

        <div class="row mt-3">
          <label for="txtXacNhan" class="col-form-label col-4 pt-2">Xác nhận mật khẩu:</label>
          <div class="w-100 col-8">
            <input type="text" class="form-control bg-white" id="txtXacNhan" name="txtXacNhan" autocomplete="off">
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="doiMK()">Lưu</button>
      </div>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> 
  $(document).ready(function() {
    $('#txtMKCu, #txtMKMoi, #txtXacNhan').on('keyup', function() {
      $(this).removeClass('border-danger');
      $(this).siblings('.error-text').remove();

      var mk = $('#txtMKMoi').val();
      var xn = $('#txtXacNhan').val();
      if (mk != '' && xn != '') {
        if (mk !== xn) {
          $('#txtMKMoi').addClass('border-danger');
          $('#txtXacNhan').addClass('border-danger');
          $('#txtXacNhan').siblings('.error-text').remove();
          $('#txtXacNhan').after(`<div class="error-text text-danger" style="font-size: 12px;">
            <i class='fas fa-exclamation-circle mr-1'></i>Mật khẩu không trùng khớp</div>`);
        } else {
          $('#txtMKMoi').removeClass('border-danger');
          $('#txtXacNhan').removeClass('border-danger');
          $('#txtXacNhan').siblings('.error-text').remove();
        }
      }
    });
    
  });

  function suaTT(){
    var dataSinhVien = {
      gioi: $('input[name="rdGioi"]:checked').val(),
      ngaySinh: $('#txtNgaySinh').val(),
      diaChi: $('#txtDiaChi').val(),
      sdt: $('#txtSDT').val(),
      email: $('#txtEmail').val()
    };
    
    $.ajax({
      url: "/sv/suatt",
      type: "POST",
      data: dataSinhVien,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data){ 
        if(dataSinhVien.ngaySinh != ""){
          var ngaySinh = formatDateFromString(dataSinhVien.ngaySinh);
        } else{
          var ngaySinh = '';
        } 

        $('#ctgioi').html(dataSinhVien.gioi);
        $('#ctngaySinh').html(ngaySinh);
        $('#ctdiaChi').html(dataSinhVien.diaChi);
        $('#ctsdt').html(dataSinhVien.sdt);
        $('#ctemail').html(dataSinhVien.email);

        $('#btnGroup').html(`
          <a href="#" class="btn btn-primary py-1 px-2 mr-2" style="font-size: 14px" id="btnSuaTTCaNhan"
            onclick="formSua('${dataSinhVien.gioi}', '${data.ngay_sinh}', '${dataSinhVien.diaChi}', '${dataSinhVien.sdt}', '${dataSinhVien.email}')">
            <i class="fas fa-pen mr-1"></i> Cập nhật thông tin
          </a>
          <a href="#" class="btn btn-primary py-1 px-2" style="font-size: 14px" data-toggle="modal" data-target="#modalDoiMK">
            <i class="fas fa-lock mr-1"></i> Đổi mật khẩu
          </a>`);

        $('#modalSua').modal('hide');
        customThongBao();
        toastr.success("", "Cập nhật thành công");
        
      },
      error: function(xhr, status, error){
        customThongBao();
        toastr.error("", "Cập nhật không thành công");             
      }
    });


  }

  function formSua(gioi, ns, dc, sdt, email){
    if(ns != ""){
      var ns = formatDateFromTimestamp(ns);
      ns = ns[2] + '-' + ns[1] + '-' + ns[0];
    } else{
      var ns = '';
    } 

    $('#txtNgaySinh').val(ns);
    $('#txtDiaChi').val(dc);
    $('#txtSDT').val(sdt);
    $('#txtEmail').val(email);

    if(gioi != "" && gioi == "Nam"){
      $('#rdNam').prop('checked', true);
    } else if(gioi != ""){
      $('#rdNu').prop('checked', true);
    }

    $('#modalSua').modal('show');
  }

  function kiemTraRong() {
    var mangID = ["txtMKCu", "txtMKMoi", "txtXacNhan"];
    var result = true;

    mangID.forEach(function(id) {
      var value = $('#' + id).val();

      if (!value) {
        $('#' + id).addClass('border-danger');
        $('#' + id).siblings('.error-text').remove();
        $('#' + id).after(`<div class="error-text text-danger" style="font-size: 12px;">
          <i class='fas fa-exclamation-circle mr-1'></i>Vui lòng điền trường này</div>`);
        result = false;
      } else {
        $('#' + id).removeClass('border-danger');
        $('#' + id).siblings('.error-text').remove();
      }
    });

    var mk = $('#txtMKMoi').val();
    var xn = $('#txtXacNhan').val();
    if (mk != '' && xn != '') {
      if (mk !== xn) {
        $('#txtMKMoi').addClass('border-danger');
        $('#txtXacNhan').addClass('border-danger');
        $('#txtXacNhan').siblings('.error-text').remove();
        $('#txtXacNhan').after(`<div class="error-text text-danger" style="font-size: 12px;">
          <i class='fas fa-exclamation-circle mr-1'></i>Mật khẩu không trùng khớp</div>`);
      }
    }

    return result;
  }

  function doiMK(){
    if(kiemTraRong() == true){
      if(!$('#txtMKMoi').hasClass('border-danger')){
        var mkCu = $('#txtMKCu').val();
        var mkMoi = $('#txtMKMoi').val();
        
        $.ajax({
          url: "/sv/doimk",
          type: "POST",
          data: {
            mkCu: mkCu,
            mkMoi: mkMoi,
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(data){ 
            if(data != 0){
              $('#modalDoiMK').modal('hide');
              customThongBao();
              toastr.success("", "Cập nhật thành công");
            } else{
              customThongBao();
              toastr.error("Mật khẩu hiện tại không đúng", "Cập nhật không thành công");  
            }
            
          },
          error: function(xhr, status, error){
            customThongBao();
            toastr.error("", "Cập nhật không thành công");             
          }
        });
      }
    }
    
  }
</script>

@include('sinhvien.layout.footer')