@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Quản lý lớp</a></li>
          <li class="breadcrumb-item active">Sinh viên</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách sinh viên @if($maLop != '') {{$maLop}} @endif</h4>

        <button type="button" class="btn btn-danger waves-effect waves-light py-1 px-2 d-none" id="btnXoaNhieu" data-toggle="modal" data-target="#xoaNhieuModal">
          <i class="fas fa-trash-alt mr-1 font-12"></i>Xóa
        </button>
        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" id="btnFormThem" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm
        </button>
      </div>
      @if($gv != '')
        <div class="d-flex align-items-center mt-2 mb-3">
          <h5 class="header-title font-14 m-0">Cố vấn học tập: &nbsp</h5>  {{ $gv }} 
        </div>
      @endif

      <table id="selection-datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Lớp</th>
            <th class="text-center">MSSV</th>
            <th class="text-center">Họ tên</th>
            <th class="text-center">Giới</th>
            <th class="text-center">Ngày sinh</th>
            <!-- <th class="text-center">Địa chỉ</th> 
            <th class="text-center">SĐT</th>
            <th class="text-center">Email</th> -->
            <th class="text-center thao-tac-col">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($sv as $row)
            <tr id="row_{{ $stt }}" class="select-row">
              <td class="text-center" id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_lop }}</td>
              <td class="text-center">{{ $row->ma_sinh_vien }}</td>
              <td>{{ $row->ho_ten }}</td>
              <td class="text-center">{{ $row->gioi_tinh }}</td>
              <td class="text-center">{{ $row->ngay_sinh ? \Carbon\Carbon::createFromTimestamp($row->ngay_sinh)->format('d/m/Y') : '' }}</td>
              <!-- <td>{{ $row->dia_chi }}</td> -->
              <!-- <td class="text-center">{{ $row->so_dien_thoai }}</td> -->
              <!-- <td>{{ $row->email }}</td> -->
              <td class="thao-tac-col">
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" onclick="chiTiet('{{$row->ma_sinh_vien}}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px"
                  onclick="formSua(this, '{{$row->ma_sinh_vien}}', '{{$row->ho_ten}}', '{{$row->gioi_tinh}}', '{{$row->ngay_sinh}}', 
                      '{{$row->dia_chi}}', '{{$row->so_dien_thoai}}', '{{$row->email}}', '{{$row->ma_lop}}')">
                  <i class="fas fa-pen"></i>
                </a>
                @if($row->count_mon == 0 && $row->count_hk == 0)
                <a href="#" class="btn btn-danger py-1 px-2 btn-xoa" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_sinh_vien}}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2 disabled btn-xo a" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_sinh_vien}}')">
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

<!-- modal thêm sửa xóa  -->
<div id="modalThemSua" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true">
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
                    <label for="txtMaSV" class="col-form-label p-1">MSSV: <span class="text-danger">*</span></label>
                    <div class="w-100">
                      <input type="text" class="form-control bg-white" id="txtMaSV" name="txtMaSV" autocomplete="off">
                    </div>
                  </div>
                  <div class="w-50 ml-4">
                    <label for="slLop" class="col-form-label p-1">Lớp: <span class="text-danger">*</span></label>
                    <div class="w-100">
                      <select class="form-control" id="slLop" name="slLop" style="width: 100% !important">
                        @if($maLop != '')
                          <option value="{{ $maLop }}">{{ $maLop }}</option>
                        @else
                          @foreach($lop as $row)
                            <option value="{{ $row->ma_lop }}">{{ $row->ma_lop }}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>

                <div class="d-flex mt-2">
                  <div class="w-50 mr-4">
                    <label for="txtTen" class="col-form-label p-1">Họ tên: <span class="text-danger">*</span></label>
                    <div class="w-100">
                      <input type="text" class="form-control" id="txtTen" name="txtTen" autocomplete="off">
                    </div>
                  </div>
                  <div class="w-50 ml-4">
                    <label for="txtMaSV" class="col-form-label p-1">Giới tính:</label>
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
                    <label for="txtNgaySinh" class="col-form-label p-1">Ngày sinh:</label>
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
                    <a href="/excelmau/Danh_sach_sinh_vien.xlsx" class="font-weight-light" download><i>Mẫu tại đây </i></a>
                  </label>
                  <div class="w-100">
                    <input type="file" class="dropify" data-height="150" id="fileUpload" name="fileUpload">
                  </div>
                </div>
              </div><!-- tab thêm bằng file-->
            </div> <!--tab-content-->
          </div><!--custom tab-->
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themSinhVien()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThemFile" onclick="themBangFile()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaSinhVien()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaSinhVien()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- modal chi tiết sinh viên -->
<div id="modalChiTietSV" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel_tt" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-0">
        <h5 class="">Thông tin sinh viên</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body py-2 px-3">        
        <table id="tableChiTiet" class="w-100">
          <tr><th>Mã số sinh viên:</th><td id="mssv"></td></tr>
          <tr><th>Họ tên:</th><td id="ten"></td></tr>
          <tr><th>Giới tính:</th><td id="gioi"></td></tr>
          <tr><th>Ngày sinh:</th><td id="ngaySinh"></td></tr>
          <tr><th>Địa chỉ:</th><td id="diaChi"></td></tr>
          <tr><th>Số điện thoại:</th><td id="sdt"></td></tr>
          <tr><th>Email:</th><td id="email"></td></tr>
          <tr><th>Lớp:</th><td id="lop"></td></tr>
          <tr>
            <th>Trạng thái:</th>
            <td class="pb-2">
              <button type="button" class="btn rounded-pill waves-effect waves-light py-1 px-2 mr-2" id="btnTrangThai" onclick="suaTrangThai()"></button>
            </td>
          </tr>
          <tr class="border-top">
            <th>Tài khoản: </th>
            <td class="pt-3 pb-2">
              <button type="button" class="btn rounded-pill waves-effect waves-light py-1 px-2 mr-2" id="btnTrangThaiTK" onclick="suaTrangThaiTK()"></button>
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

<!-- modal xóa nhiều sv -->
<div class="modal fade modal-center" tabindex="-1" role="dialog" id="xoaNhieuModal" aria-labelledby="xoaNhieuModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="xoaNhieuModalLabel">Thêm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body"> 
        <div id="" class="form-group row m-0">
          Xóa không thể khôi phục. Bạn có chắc muốn xóa không?
        </div>     
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaNhieuSinhVien()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#txtMaSV, #slLop, #txtTen, #txtNgaySinh').on('input', function() {
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
    
    // chọn nhiều dòng trong bảng
    $('#selection-datatable tbody').on('click', 'tr', function(event) {
      var $td = $(event.target).closest('td');
      var $tr = $(this);

      if ($td.is(':last-child')) {
        return false;
      } else if($tr.find('.btn-xoa').hasClass('disabled')){
        customThongBao();
        toastr.warning("", "Không thể chọn dòng này")
        return false;
      } else {
        var table = $('#selection-datatable').DataTable();
        $tr.toggleClass('selected');
        var count = table.rows('.selected').count();
        $('#btnXoaNhieu').toggleClass('d-none', count == 0);
        $('#btnFormThem').toggleClass('d-none', count != 0);

        $('.thao-tac-col').toggleClass('d-none', count != 0);
        table.rows().every(function(idx) {
          var $row = $(this.node());
          $row.find('td:last-child').toggleClass('d-none', count != 0);
        });
      }
    });

  });

  function xoaNhieuSinhVien(){
    var table = $('#selection-datatable').DataTable();
    var dataXoa = [];

    table.rows().every(function() {
      var $row = $(this.node());
      if ($row.hasClass('selected')) {
        var value = $row.find('td:eq(2)').text();
        dataXoa.push(value);
      }
    });
    
    $.ajax({
      url: '/admin/xoanhieusv',
      method: 'POST',
      data: {
        dataXoa: dataXoa
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
        customThongBao();
        toastr.success("", "Đã xóa thành công");
        setTimeout(() => {
          location.reload();
        }, "1800");
      },
      error: function(xhr) {
        customThongBao();
        toastr.success("", "Xóa không thành công");
      }
    });

  }

  function doiMatKhau(){
    var ma = $('#mssv').text();

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

  function suaTrangThaiTK(){
    var ma = $('#mssv').text();
    
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
          $('#btnTrangThaiTK').removeClass('btn-danger');
          $('#btnTrangThaiTK').addClass('btn-success');
          $('#btnTrangThaiTK').html('Hoạt động');
        } else{
          $('#btnTrangThaiTK').removeClass('btn-success');
          $('#btnTrangThaiTK').addClass('btn-danger');
          $('#btnTrangThaiTK').html('Vô hiệu hóa');
        }  
        customThongBao();
        toastr.success("", "Cập nhật trạng thái thành công");  
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  function suaTrangThai(){
    var ma = $('#mssv').text();
    var trang_thai = $('#btnTrangThai').text();

    $.ajax({
      url: '/admin/cntrangthai',
      method: 'POST',
      data:{
        ma: ma,
        trang_thai: trang_thai
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
        if(data == "Đang học"){
          $('#btnTrangThai').removeClass('btn-danger');
          $('#btnTrangThai').addClass('btn-success');
          $('#btnTrangThai').html(data);

          $('#btnTrangThaiTK').removeClass('btn-danger');
          $('#btnTrangThaiTK').addClass('btn-success');
          $('#btnTrangThaiTK').html('Hoạt động');
        } else{
          $('#btnTrangThai').removeClass('btn-success');
          $('#btnTrangThai').addClass('btn-danger');
          $('#btnTrangThai').html(data);

          $('#btnTrangThaiTK').removeClass('btn-success');
          $('#btnTrangThaiTK').addClass('btn-danger');
          $('#btnTrangThaiTK').html('Vô hiệu hóa');
        }  
        customThongBao();
        toastr.success("", "Cập nhật trạng thái thành công");  
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  function chiTiet(ma){
    $.ajax({
      url: '/admin/ttsv',
      method: 'GET',
      data:{
        ma: ma,
      },
      success: function(data) {
        if(data.ngay_sinh != null){
          var ngaySinh = formatDateFromTimestamp(data.ngay_sinh);
          ngaySinh = ngaySinh[0] + '/' + ngaySinh[1] + '/' + ngaySinh[2]
        } else{
          var ngaySinh = '';
        } 

        $('#mssv').html(data.ma_sinh_vien);
        $('#ten').html(data.ho_ten);
        $('#gioi').html(data.gioi_tinh);
        $('#ngaySinh').html(ngaySinh);
        $('#diaChi').html(data.dia_chi);
        $('#sdt').html(data.so_dien_thoai);
        $('#email').html(data.email);
        $('#lop').html(data.ten_lop);
        
        if(data.trang_thai_hoc == "Đang học"){
          $('#btnTrangThai').addClass('btn-success');
          $('#btnTrangThai').html('Đang học');
        } else{
          $('#btnTrangThai').addClass('btn-danger');
          $('#btnTrangThai').html('Đã thôi học');
        }  

        if(data.trang_thai == 0){
          $('#btnTrangThaiTK').addClass('btn-success');
          $('#btnTrangThaiTK').html('Hoạt động');
        } else{
          $('#btnTrangThaiTK').addClass('btn-danger');
          $('#btnTrangThaiTK').html('Vô hiệu hóa');
        }   
        
        $('#modalChiTietSV').modal('show');
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  function themSinhVien(){
    var dataSinhVien = {
      mssv: $('#txtMaSV').val(),
      lop: $('#slLop').val(),
      ten: $('#txtTen').val(),
      gioi: $('input[name="rdGioi"]:checked').val(),
      ngaySinh: $('#txtNgaySinh').val(),
      diaChi: $('#txtDiaChi').val(),
      sdt: $('#txtSDT').val(),
      email: $('#txtEmail').val()
    };

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themsinhvien",
        type: "POST",
        data: dataSinhVien,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
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

            var table = $('#datatable').DataTable();
            var newRow = table.row.add([
              `${data.num_row}`,
              `${data.sv.ma_lop}`,
              `${data.sv.ma_sinh_vien}`,
              `${data.sv.ho_ten}`,
              `${data.sv.gioi_tinh}`,
              `${formatDateFromString(dataSinhVien.ngaySinh)}`,
              `<a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target="#modalChiTietSV"
                  onclick="chiTiet('${data.sv.ma_sinh_vien}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${data.sv.ma_sinh_vien}','${data.sv.ten_sinh_vien}', '${data.sv.gioi_tinh}', 
                    '${data.sv.ngay_sinh}', '${data.sv.dia_chi}', '${data.sv.so_dien_thoai}', '${data.sv.email}', '${data.sv.ma_lop}' )">
                <i class="fas fa-pen"></i></a>
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${data.sv.ma_sinh_vien}')">
                  <i class="fas fa-trash-alt"></i>
                </a>`
            ]).draw(false).node();

            $(newRow).find('td:first-child').attr('id', 'stt');
            $(newRow).find('td:eq(1)').addClass('text-center');
            $(newRow).find('td:eq(2)').addClass('text-center');
            $(newRow).find('td:eq(4)').addClass('text-center');
            $(newRow).find('td:eq(5)').addClass('text-center');
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

  function themBangFile(){
    var formData = new FormData();
    var fileInput = document.getElementById('fileUpload');

    if (fileInput.files.length === 0) {
      $('.dropify-wrapper').siblings('.error-text').remove();
      $('.dropify-wrapper').after(`<div class="error-text text-danger pt-1" style="font-size: 14px;">
        <i class='fas fa-exclamation-circle mr-1'></i>Vui lòng chọn một tệp dữ liệu (.xls hoặc .xlsx)</div>`);
      return;
    }

    var fileName = fileInput.files[0].name;
    var isExcelFile = /\.(xlsx|xls)$/i.test(fileName);

    if (isExcelFile == false) {
      $('.dropify-wrapper').siblings('.error-text').remove();
      $('.dropify-wrapper').after(`<div class="error-text text-danger pt-1" style="font-size: 14px;">
        <i class='fas fa-exclamation-circle mr-1'></i>Định dạng tệp không phù hợp. Vui lòng sử dụng tệp .xls hoặc .xlsx</div>`);
      return;
    }

    formData.append('file', fileInput.files[0]);

    customThongBaoCho();
    var toast = toastr.info('Vui lòng chờ cho đến khi quá trình hoàn tất', 'Đang lưu');
    $.ajax({
      url: '/admin/importsinhvien',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (data) {
        toastr.clear(toast);

        if(data != "Lỗi thêm dữ liệu"){
          $('.modal-center').modal('hide');
          customThongBao();
          toastr.success("", "Thêm thành công");

          setTimeout(() => {location.reload();}, "1800");

        } else{
          customThongBao();
          toastr.error("", "Thêm không thành công");

          $('.dropify-wrapper').siblings('.error-text').remove();
          $('.dropify-wrapper').after(`<div class="error-text text-danger pt-1" style="font-size: 14px;">
            <i class='fas fa-exclamation-circle mr-1'></i>Lỗi định dạng dữ liệu</div>`);
        }
        
      },
      error: function (xhr) {
        console.log(xhr.responseJSON.error);
      }
    });
  }

  function suaSinhVien(){
    var dataSinhVien = {
      mssv: $('#txtMaSV').val(),
      lop: $('#slLop').val(),
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
        url: "/admin/suasinhvien",
        type: "POST",
        data: dataSinhVien,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          $('.modal-center').modal('hide');
          customThongBao();
          toastr.success("", "Cập nhật thành công");

          if(dataSinhVien.ngaySinh != ''){
            var ngaySinh = formatDateFromString(dataSinhVien.ngaySinh);
          } else{
            var ngaySinh = '';
          } 

          $("#row_" + tt).html(`
            <td id="stt">${tt}</td>
            <td class="text-center">${dataSinhVien.lop}</td>
            <td class="text-center">${dataSinhVien.mssv}</td>
            <td>${dataSinhVien.ten}</td>
            <td class="text-center">${dataSinhVien.gioi}</td>
            <td class="text-center">${ngaySinh}</td>
            <td>
              <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target="#modalChiTietSV"
                  onclick="chiTiet('${dataSinhVien.mssv}')">
                <i class="fas fa-eye"></i>
              </a>
              <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${dataSinhVien.mssv}', '${dataSinhVien.ten}', '${dataSinhVien.gioi}', '${data.ngay_sinh}', 
                    '${dataSinhVien.diaChi}', '${dataSinhVien.sdt}', '${dataSinhVien.email}', '${dataSinhVien.lop}')">
                <i class="fas fa-pen"></i>
              </a>
              <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${dataSinhVien.mssv}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
            </td>`); 
          
        },
        error: function(xhr, status, error){
          customThongBao();
          toastr.error("", "Cập nhật không thành công");                 
        }
      });
    }

  }

  function xoaSinhVien(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaSV').val();

    $.ajax({
      url: "/admin/xoasinhvien",
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
          capNhatSttSelectionTable(tt);
          
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
    var mangID = ["txtMaSV", "slLop", "txtTen"];
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
    var mangID = ["txtMaSV", "slLop", "txtTen", "txtNgaySinh", "txtSDT", "txtEmail", "txtDiaChi"];

    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    $('#nav-bar').removeClass('d-none');
    $('#txtMaSV').prop('readonly', false);
    $('#rdNam').prop('checked', true);
    $('#btnThem').removeClass('d-none');
    $('#btnThemFile').addClass('d-none');
  }

  function formSua(ele, ma, ten, gioi, ngaySinh, diaChi, sdt, email, lop){
    customFormSua();    
    var mangID = ["txtMaSV", "slLop", "txtTen", "txtNgaySinh", "txtSDT", "txtEmail", "txtDiaChi"];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });

    $('#nav-bar').addClass('d-none');  
    $('#nav-file').removeClass('active show');  
    $('#nav-default').addClass('active show');   
    $('#txtMaSV').prop('readonly', true);
    $('#btnThemFile').addClass('d-none');
    $('#btnThem').addClass('d-none');
    $('#modalThemSua').modal('show');

    var stt = $(ele).closest('tr').find('td:eq(0)').text();
    $('#txtStt').val(stt);
    $('#txtMaSV').val(ma);
    $('#txtTen').val(ten);

    if(ngaySinh != ""){
      var ngaySinh = formatDateFromTimestamp(ngaySinh);
      ngaySinh = ngaySinh[2] + '-' + ngaySinh[1] + '-' + ngaySinh[0];
    } else{
      var ngaySinh = '';
    } 

    $('#txtNgaySinh').val(ngaySinh);
    $('#txtDiaChi').val(diaChi);
    $('#txtSDT').val(sdt);
    $('#txtEmail').val(email);
    $('#slLop').val(lop);

    if(gioi == "Nam"){
      $('#rdNam').prop('checked', true);
    } else if(gioi != ""){
      $('#rdNu').prop('checked', true);
    }
  }
 
  function formXoa(ele, ma){
    customFormXoa();
    $('#modalThemSua').modal('show');
    $('#btnThemFile').addClass('d-none');
    $('#model-resize').removeClass('modal-lg');
    var stt = $(ele).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaSV').val(ma);
  }
</script>

@include('admin.layout.footer')