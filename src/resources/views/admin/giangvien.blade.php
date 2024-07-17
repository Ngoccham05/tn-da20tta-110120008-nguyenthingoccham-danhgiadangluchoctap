@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Quản lý lớp</a></li>
          <li class="breadcrumb-item active">Danh sách giảng viên</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách giảng viên</h4>

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm
        </button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center" style="max-width:100px !important">STT</th>
            
            <th class="text-center" style="max-width:150px !important">Mã giảng viên</th>
            <th class="text-center">Họ tên</th>
            <th class="text-center">Giới</th>
            <th class="text-center">Ngày sinh</th>
            <!-- <th class="text-center">Địa chỉ</th>
            <th class="text-center">SĐT</th>
            <th class="text-center">Email</th> -->
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($gv as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              
              <td class="text-center">{{ $row->ma_giang_vien }}</td>
              <td>{{ $row->ho_ten }}</td>
              <td class="text-center">{{ $row->gioi_tinh }}</td>
              <td class="text-center">{{ $row->ngay_sinh ? \Carbon\Carbon::createFromTimestamp($row->ngay_sinh)->format('d/m/Y') : '' }}</td>
              <!-- <td>{{ $row->dia_chi }}</td> -->
              <!-- <td class="text-center">{{ $row->so_dien_thoai }}</td> -->
              <!-- <td>{{ $row->email }}</td> -->
              <td>
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px"
                  onclick="chiTiet('{{$row->ma_giang_vien}}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '{{$row->ma_giang_vien}}', '{{$row->ho_ten}}', '{{$row->gioi_tinh}}', '{{$row->ngay_sinh}}', 
                      '{{$row->dia_chi}}', '{{$row->so_dien_thoai}}', '{{$row->email}}')">
                  <i class="fas fa-pen"></i>
                </a>

                @if($row->count != 0)
                <a href="#" class="btn btn-danger py-1 px-2 disabled" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_giang_vien}}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_giang_vien}}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->

<div class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg modal-dialog-centered" id="model-resize">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCenterModalLabel">Thêm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body">  
        <div id="xoaForm" class="form-group row m-0">
          Xóa không thể khôi phục. Bạn có chắc muốn xóa không?
        </div>  
      
        <div class="form-group d-none row m-0">
          <div class="col-12 px-0">
            <input type="text" class="form-control" id="txtStt" name="txtStt" readonly>
          </div>
        </div>

        <div id="themSuaForm">
          <div class="custom-tab">
            <nav id="nav-bar" class="pb-2">
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link px-3 active" id="nav-default-tab" style="font-size:14px"
                  data-toggle="tab" href="#nav-default" role="tab" aria-controls="nav-default" aria-selected="true">Mặc định</a>
                <a class="nav-item nav-link px-3" id="nav-file-tab" style="font-size:14px"
                  data-toggle="tab" href="#nav-file" role="tab" aria-controls="nav-file" aria-selected="false">Thêm bằng tệp</a>
              </div>
            </nav>

            <div class="tab-content pt-0" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-default" role="tabpanel" aria-labelledby="nav-default-tab">
                <div class="d-flex">
                  <div class="w-50 mr-4">
                    <label for="txtMaGV" class="col-form-label p-1">Mã giảng viên: <span class="text-danger">*</span></label>
                    <div class="w-100">
                      <input type="text" class="form-control bg-white" id="txtMaGV" name="txtMaGV" autocomplete="off">
                    </div>
                  </div>
                  <div class="w-50 ml-4"></div>
                </div>

                <div class="d-flex mt-2">
                  <div class="w-50 mr-4">
                    <label for="txtTen" class="col-form-label p-1">Họ tên: <span class="text-danger">*</span></label>
                    <div class="w-100">
                      <input type="text" class="form-control" id="txtTen" name="txtTen" autocomplete="off">
                    </div>
                  </div>
                  <div class="w-50 ml-4">
                    <label for="" class="col-form-label p-1">Giới tính: </label>
                    <div class="d-flex w-100 py-2">
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
                </div>

                <div class="d-flex mt-2">
                  <div class="w-50 mr-4">
                    <label for="txtNgaySinh" class="col-form-label p-1">Ngày sinh: </label>
                    <div class="w-100">
                      <input type="date" class="form-control" id="txtNgaySinh" name="txtNgaySinh" max="<?php echo date('Y-m-d'); ?>">
                    </div>
                  </div>
                  <div class="w-50 ml-4">
                    <label for="txtDiaChi" class="col-form-label p-1">Địa chỉ:</label>
                    <div class="w-100">
                      <input type="text" class="form-control bg-white" id="txtDiaChi" name="txtDiaChi" autocomplete="off">
                    </div>
                  </div>
                </div>

                <div class="d-flex mt-2">
                  <div class="w-50 mr-4">
                    <label for="txtSDT" class="col-form-label p-1">Số điện thoại:</label>
                    <div class="w-100">
                      <input type="text" class="form-control" id="txtSDT" name="txtSDT" autocomplete="off">
                    </div>
                  </div>

                  <div class="w-50 ml-4">
                    <label for="txtEmail" class="col-form-label p-1">Email:</label>
                    <div class="w-100">
                      <input type="text" class="form-control bg-white" id="txtEmail" name="txtEmail" autocomplete="off">
                    </div>
                  </div>
                </div>
              </div> <!-- tab thêm 1-->

              <div class="tab-pane fade show" id="nav-file" role="tabpanel" aria-labelledby="nav-file-tab">
                <div class="d-flex mt-2">
                  <label for="fileUpload" class="col-form-label p-1 pt-2 w-25">
                    Chọn tệp: <span class="text-danger">*</span></br>
                    <a href="/excelmau/Danh_sach_giang_vien.xlsx" class="font-weight-light" download><i>Mẫu tại đây </i></a>
                  </label>
                  <div class="w-100">
                    <input type="file" class="dropify" data-height="100" id="fileUpload" name="fileUpload">
                  </div>
                </div>
              </div><!-- tab thêm bằng file-->
            </div> <!--tab-content-->
          </div><!--custom tab-->
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themGiangVien()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThemFile" onclick="themBangFile()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaGiangVien()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaGiangVien()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modalChiTiet" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCenterModalLabel">Thông tin giảng viên</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body py-2 px-3">        
        <table id="tableChiTiet" class="w-100">
          <tr><th>Mã giảng viên:</th><td id="mgv"></td></tr>
          <tr><th>Họ tên:</th><td id="ten"></td></tr>
          <tr><th>Giới tính:</th><td id="gioi"></td></tr>
          <tr><th>Ngày sinh:</th><td id="ngaySinh"></td></tr>
          <tr><th>Địa chỉ:</th><td id="diaChi"></td></tr>
          <tr><th>Số điện thoại:</th><td id="sdt"></td></tr>
          <tr><th>Email:</th><td id="email"></td></tr>
          <tr class="border-top">
            <th>Tài khoản: </th>
            <td class="pt-3 pb-2">
              <button type="button" class="btn rounded-pill waves-effect waves-light py-1 px-2 mr-2" id="btnTrangThai" onclick="suaTrangThaiTK()"></button>
              <button type="button" class="btn btn-danger rounded-pill waves-effect waves-light py-1 px-2" id="btnReset" onclick="doiMatKhau()">Đặt lại mật khẩu</button>
            </td>
          </tr>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#txtMaGV, #txtTen').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });

    // kiểm tra email
    const validateEmail = (email) => {
      return email.match(
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
      );
    };

    const validate = () => {
      const email = $('#txtEmail').val();

      if(validateEmail(email) || email == ''){
        $('#txtEmail').removeClass('border-danger');
        $('#txtEmail').siblings('.error-text').remove();
      } else{
        $('#txtEmail').addClass('border-danger');
        $('#txtEmail').siblings('.error-text').remove();
        $('#txtEmail').after(`<div class="error-text text-danger" style="font-size: 12px;">
          <i class='fas fa-exclamation-circle mr-1'></i>Email không hợp lệ</div>`);
      }
      return false;
    }
    $('#txtEmail').on('change', validate);
    
  });

  function suaTrangThaiTK(){
    var ma = $('#mgv').text();
    
    $.ajax({
      url: '/admin/cntrangthaitk',
      method: 'POST',
      data:{
        ma: ma,
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
        if(data == 0){
          $('#btnTrangThai').removeClass('btn-danger');
          $('#btnTrangThai').addClass('btn-success');
          $('#btnTrangThai').html('Hoạt động');
        } else{
          $('#btnTrangThai').removeClass('btn-success');
          $('#btnTrangThai').addClass('btn-danger');
          $('#btnTrangThai').html('Vô hiệu hóa');
        }  
        customThongBao();
        toastr.success("", "Cập nhật trạng thái thành công");  
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  function doiMatKhau(){
    var ma = $('#mgv').text();

    $.ajax({
      url: '/admin/doimatkhau',
      method: 'POST',
      data:{
        ma: ma,
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
        customThongBao();
        toastr.success("Mật khẩu đã được đặt về mặc định", "Cập nhật thành công");
      },
      error: function(xhr) {
        customThongBao();
        toastr.success("", "Cập nhật không thành công");
      }
    });
  }

  function chiTiet(ma){
    $.ajax({
      url: '/admin/ttgv',
      method: 'GET',
      data:{
        ma: ma,
      },
      success: function(data) {
        // console.log(data);
        if(data.ngay_sinh != null){
          var ngaySinh = formatDateFromTimestamp(data.ngay_sinh);
          ngaySinh = ngaySinh[0] + '/' + ngaySinh[1] + '/' + ngaySinh[2]
        } else{
          var ngaySinh = '';
        }        

        $('#mgv').html(data.ma_giang_vien);
        $('#ten').html(data.ho_ten);
        $('#gioi').html(data.gioi_tinh);
        $('#ngaySinh').html();
        $('#diaChi').html(data.dia_chi);
        $('#sdt').html(data.so_dien_thoai);
        $('#email').html(data.email);

        if(data.trang_thai == 0){
          $('#btnTrangThai').addClass('btn-success');
          $('#btnTrangThai').html('Hoạt động');
        } else{
          $('#btnTrangThai').addClass('btn-danger');
          $('#btnTrangThai').html('Vô hiệu hóa');
        }    
        
        $('#modalChiTiet').modal('show');
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  function themGiangVien(){
    var dataGiangVien = {
      mgv: $('#txtMaGV').val(),
      ten: $('#txtTen').val(),
      gioi: $('input[name="rdGioi"]:checked').val(),
      ngaySinh: $('#txtNgaySinh').val(),
      diaChi: $('#txtDiaChi').val(),
      sdt: $('#txtSDT').val(),
      email: $('#txtEmail').val()
    };

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themgiangvien",
        type: "POST",
        data: dataGiangVien,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          // console.log(data);
          if(data == "Đã tồn tại"){
            customThongBao();
            toastr.error("Dữ liệu đã tồn tại", "Thêm không thành công");
          }else if(data == "Lỗi khi thêm dữ liệu"){
            customThongBao();
            toastr.error("Lỗi khi thêm dữ liệu", "Thêm không thành công");
          } else{
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Thêm thành công");

            if(data.gv.ngay_sinh != null){
              var ngaySinh = formatDateFromString(data.gv.ngay_sinh);
            } else{
              var ngaySinh = '';
            } 

            var table = $('#datatable').DataTable();
            var newRow = table.row.add([
              `${data.num_row}`,
              `${data.gv.ma_giang_vien}`,
              `${data.gv.ho_ten}`,
              `${data.gv.gioi_tinh}`,
              `${ngaySinh}`,
              `<a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${data.gv.ma_giang_vien}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target="#modalChiTiet"
                  onclick="chiTiet('${data.gv.ma_giang_vien}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${dataGiangVien.mgv}', '${dataGiangVien.ten}', '${dataGiangVien.gioi}', '${(data.gv.ngay_sinh)}', 
                    '${dataGiangVien.diaChi}', '${dataGiangVien.sdt}', '${dataGiangVien.email}')">
                <i class="fas fa-pen"></i></a>`
            ]).draw(false).node();

            $(newRow).find('td:first-child').attr('id', 'stt');
            $(newRow).find('td:eq(1)').addClass('text-center');
            $(newRow).find('td:eq(3)').addClass('text-center');
            $(newRow).find('td:eq(4)').addClass('text-center');
            $(newRow).attr('id', 'row_' + data.num_row);
          } 
        },
        error: function(xhr, status, error){
          customThongBao();
          toastr.error("", "Thêm không thành công");                 
        }
      });
    }
  }

  function suaGiangVien(){
    var dataGiangVien = {
      mgv: $('#txtMaGV').val(),
      ten: $('#txtTen').val(),
      gioi: $('input[name="rdGioi"]:checked').val(),
      ngaySinh: $('#txtNgaySinh').val(),
      diaChi: $('#txtDiaChi').val(),
      sdt: $('#txtSDT').val(),
      email: $('#txtEmail').val()
    };
    var tt = $('#txtStt').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/suagiangvien",
        type: "POST",
        data: dataGiangVien,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          // console.log(data);
          $('.modal-center').modal('hide');
          customThongBao();
          toastr.success("", "Cập nhật thành công");

          if(data.gv.ngay_sinh != null){
            var ngaySinh = formatDateFromString(data.gv.ngay_sinh);
          } else{
            var ngaySinh = '';
          }

          if(data.count == 0){
            var btn_xoa = `<a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${dataGiangVien.mgv}')">
                  <i class="fas fa-trash-alt"></i>
                </a>`;
          } else{
            var btn_xoa = `<a href="#" class="btn btn-danger py-1 px-2 disabled" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${dataGiangVien.mgv}')">
                  <i class="fas fa-trash-alt"></i>
                </a>`;
          }

          $("#row_" + tt).html(`
            <td id="stt">${tt}</td>
            <td class="text-center">${dataGiangVien.mgv}</td>
            <td>${dataGiangVien.ten}</td>
            <td class="text-center">${dataGiangVien.gioi}</td>
            <td class="text-center">${ngaySinh}</td>
            <td>
              <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target="#modalChiTiet"
                  onclick="chiTiet('${dataGiangVien.mgv}')">
                <i class="fas fa-eye"></i>
              </a>
              <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${dataGiangVien.mgv}', '${dataGiangVien.ten}', '${dataGiangVien.gioi}', '${data.gv.ngay_sinh}', 
                    '${dataGiangVien.diaChi}', '${dataGiangVien.sdt}', '${dataGiangVien.email}')">
                <i class="fas fa-pen"></i>
              </a>
              ${btn_xoa}
            </td>`); 
          
        },
        error: function(xhr, status, error){
          customThongBao();
          toastr.error("", "Cập nhật không thành công");                 
        }
      });
    }

  }

  function xoaGiangVien(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaGV').val();

    $.ajax({
      url: "/admin/xoagiangvien",
      type: "POST",
      data: {
        ma: ma,
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data){  
        if(data != 0){
          customThongBao();
          toastr.success("", "Xóa thành công");
          $('.modal-center').modal('hide');
          capNhatStt(tt);
          
        } else{
          customThongBao();
          toastr.error("", "Xóa không thành công");
        }
      },
      error: function(xhr, status, error){
        customThongBao();
        toastr.error("", "Xóa không thành công");                
      }
    });
  }

  function kiemTraRong() {
    var mangID = ["txtMaGV", "txtTen"];
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
    return result;
  }

  function formThem(){
    customFormThem();
    var mangID = ["txtMaGV", "txtTen", "txtNgaySinh", "txtSDT", "txtEmail", "txtDiaChi"];

    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    $('#nav-bar').removeClass('d-none');
    $('#txtMaGV').prop('readonly', false);
    $('#rdNam').prop('checked', true);
    $('#btnThem').removeClass('d-none');
    $('#btnThemFile').addClass('d-none');
    $('#model-resize').addClass('modal-lg');
  }

  function formSua(ele, ma, ten, gioi, ngaySinh, diaChi, sdt, email){
    customFormSua();
    $('#model-resize').addClass('modal-lg');
    var mangID = ["txtMaGV", "txtTen", "txtNgaySinh", "txtSDT", "txtEmail", "txtDiaChi"];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });

    $('#nav-bar').addClass('d-none');
    $('#txtMaGV').prop('readonly', true);
    $('#btnThem').addClass('d-none');
    $('#btnThemFile').addClass('d-none');

    var stt = $(ele).closest('tr').find('td:first').text();
    $('#txtStt').val(stt);
    $('#txtMaGV').val(ma);
    $('#txtTen').val(ten);
    
    if(ngaySinh != null){
      var ngaySinh = formatDateFromTimestamp(ngaySinh);
      ngaySinh[2] + '-' + ngaySinh[1] + '-' + ngaySinh[0]
    } else{
      var ngaySinh = '';
    }      

    $('#txtNgaySinh').val(ngaySinh);
    $('#txtDiaChi').val(diaChi);
    $('#txtSDT').val(sdt);
    $('#txtEmail').val(email);

    if(gioi == "Nam"){
      $('#rdNam').prop('checked', true);
    } else{
      $('#rdNu').prop('checked', true);
    }
  }

  function formXoa(ele, ma){
    customFormXoa();
    $('#btnThemFile').addClass('d-none');
    $('#model-resize').removeClass('modal-lg');
    var stt = $(ele).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaGV').val(ma);
  }
 
</script>

@include('admin.layout.footer')