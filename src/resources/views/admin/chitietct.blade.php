@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Chương trình đào tạo {{$chuong_trinh->ten_chuong_trinh}}</a></li>
          <li class="breadcrumb-item active">Danh sách</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách môn học</h4>

        <a href="/admin/nhommon/{{encrypt($chuong_trinh->ma_chuong_trinh)}}" class="btn btn-success waves-effect waves-light py-1 mr-2">
          <i class="fas fa-layer-group mr-1 font-12"></i>Nhóm môn</a>
        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target="#modalThem">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Học kỳ</th>
            <th class="text-center">Mã môn</th>
            <th class="text-center">Tên môn</th>
            <th class="text-center">STC</th>
            <th class="text-center">Loại học phần</th>
            <!-- <th class="text-center">Khối kiến thức</th>-->
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($mon_hoc_trong_ct as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->thu_tu_hoc_ky }}</td>
              <td class="text-center">{{ $row->ma_mon_hoc }}</td>
              <td>{{ $row->ten_mon_hoc }}</td>
              <td class="text-center">{{ $row->so_tin_chi }}</td>
              <td class="text-center">{{ $row->ten_loai_hoc_phan }}</td>
              <!-- <td>{{ $row->ten_khoi_kien_thuc }}</td>-->
              <td>
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" 
                  onclick="chiTiet('{{ $row->ma_mon_hoc }}', '{{ $row->ten_mon_hoc }}', '{{ $row->so_tin_chi }}',
                                  '{{ $row->ten_loai_hoc_phan }}', '{{ $row->ten_khoi_kien_thuc }}', '{{ $row->thu_tu_hoc_ky}}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_mon_hoc }}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->

<div id="modalThem" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
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
            <nav id="nav-bar" class="pb-3">
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link px-3 active" id="nav-default-tab" style="font-size:14px"
                  data-toggle="tab" href="#nav-default" role="tab" aria-controls="nav-default" aria-selected="true">Mặc định</a>
                <a class="nav-item nav-link px-3" id="nav-file-tab" style="font-size:14px"
                  data-toggle="tab" href="#nav-file" role="tab" aria-controls="nav-file" aria-selected="false">Thêm bằng tệp</a>
              </div>
            </nav>

            <div class="tab-content pt-0" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-default" role="tabpanel" aria-labelledby="nav-default-tab">
                <div class="form-group row m-0">
                  <label for="txtMaMon" class="col-md-4 col-form-label px-0">Mã môn học: <span class="text-danger">*</span></label>
                  <div class="col-md-8 px-0">
                    <!-- <input type="text" class="form-control bg-white" id="txtMaMon" name="txtMaMon" autocomplete="off"> -->
                    <input id="txtMaMon" class="dropdown-toggle form-control" data-toggle="dropdown" aria-expanded="false" autocomplete="off">
                    <ul id="dd_monHoc" class="dropdown-menu rounded-0 p-0 m-0" aria-labelledby="monHoc" style="width:450px; max-height: 200px; overflow-y:scroll; position:relative;">
                      <li class="p-2" style="position:sticky; top:0; background:white; z-index:1;">
                        <input type="text" class="form-control" id="inputSearch" autocomplete="off">
                      </li>
                      @foreach ($mon_hoc as $row)
                        <li>
                          <a class="dropdown-item" href="#" onclick="chonMonHoc(this, '{{ $row->ma_mon_hoc }}', '{{ $row->ten_mon_hoc }}', '{{ $row->so_tin_chi }}')">
                            {{ $row->ma_mon_hoc }}: {{ $row->ten_mon_hoc }}
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>

                <div class="form-group row m-0 mt-3">
                  <label for="txtTenMon" class="col-md-4 col-form-label px-0">Tên môn học: <span class="text-danger">*</span></label>
                  <div class="col-md-8 px-0">
                    <input type="text" class="form-control" id="txtTenMon" name="txtTenMon" autocomplete="off">
                  </div>
                </div>

                <div class="form-group row m-0 mt-3">
                  <label for="txtSoTC" class="col-md-4 col-form-label px-0">Số tín chỉ: <span class="text-danger">*</span></label>
                  <div class="col-md-8 px-0">
                    <input type="number" class="form-control" id="txtSoTC" name="txtSoTC" autocomplete="off">
                  </div>
                </div>

                <div class="form-group row m-0 mt-3">
                  <label for="slKhoiKienThuc" class="col-md-4 col-form-label px-0">Khối kiến thức: <span class="text-danger">*</span></label>
                  <div class="col-md-8 px-0">
                    <select class="form-control" id="slKhoiKienThuc" name="slKhoiKienThuc" style="width: 100% !important">
                      @foreach($khoi_kien_thuc as $kkt)
                        <option value="{{ $kkt->ma_khoi_kien_thuc}}">{{ $kkt->ten_khoi_kien_thuc }}</option>
                       @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row m-0 mt-3">
                  <label for="slLoaiHocPhan" class="col-md-4 col-form-label px-0">Loại học phần: <span class="text-danger">*</span></label>
                  <div class="col-md-8 px-0">
                    <select class="form-control" id="slLoaiHocPhan" name="slLoaiHocPhan" style="width: 100% !important">
                      @foreach($loai_hoc_phan as $lhp)
                        <option value="{{ $lhp->ma_loai_hoc_phan}}">{{ $lhp->ten_loai_hoc_phan }}</option>
                       @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row m-0 mt-3">
                  <label for="txtHocKy" class="col-md-4 col-form-label px-0">Học kỳ: <span class="text-danger">*</span></label>
                  <div class="col-md-8 px-0">
                    <input type="number" class="form-control" id="txtHocKy" name="txtHocKy" autocomplete="off">
                  </div>
                </div>
              </div>

              <!-- thêm bằng file -->
              <div class="tab-pane fade show" id="nav-file" role="tabpanel" aria-labelledby="nav-file-tab">
                <div class="form-group row m-0 mt-2">
                  <label for="slChuongTrinh2" class="col-md-3 col-form-label px-0">CT đào tạo:</label>
                  <div class="col-md-9 px-0">
                    <select class="form-control" id="slChuongTrinh2" name="slChuongTrinh2" style="width: 100% !important">
                      <option value="{{ $chuong_trinh->ma_chuong_trinh}}">{{ $chuong_trinh->ma_chuong_trinh }} - {{ $chuong_trinh->ten_chuong_trinh }}</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row m-0 mt-2">
                  <label for="fileUpload" class="col-md-3 col-form-label px-0">
                    Chọn tệp: <span class="text-danger">*</span></br>
                    <!-- <a href="/excelmau/Danh_sach_sinh_vien.xlsx" class="font-weight-light" download><i>Mẫu tại đây </i></a> -->
                  </label>
                  <div class="col-md-9 px-0">
                    <input type="file" class="dropify" data-height="100" id="fileUpload" name="fileUpload">
                  </div>
                </div>
              </div>
            </div>  
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themMonTrongChuongTrinh()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThemFile" onclick="themBangFile()">thêm file</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaMonHocTrongChuongTrinh()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Chi tiết -->
<div id="modalChiTiet" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCenterModalLabel">Thông tin môn học</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body py-2 px-3">        
        <table id="tableChiTiet" class="w-100">
          <tr><th>Học kỳ:</th><td id="ctHK"></td></tr>
          <tr><th style="width: 35%">Mã môn:</th><td id="ctMa"></td></tr>
          <tr><th>Tên môn:</th><td id="ctTen"></td></tr>
          <tr><th>Số tín chỉ:</th><td id="ctSTC"></td></tr>
          <tr><th>Loại học phần:</th><td id="ctLoai"></td></tr>
          <tr><th>Khối kiến thức:</th><td id="ctKhoi"></td></tr>
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
    $('#txtMaMon, #txtTenMon, #txtSoTC, #txtHocKy').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });

    $("#inputSearch").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#dd_monHoc li a").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

  });

  function kiemTraRong() {
    var mangID = ['txtMaMon', 'txtTenMon', 'txtSoTC', 'txtHocKy'];
    var result = true;

    mangID.forEach(function(id) {
      var value = $('#' + id).val().trim();
      var errorText = `Vui lòng điền trường này`;

      if (!value) {
        $('#' + id).addClass('border-danger');
        $('#' + id).siblings('.error-text').remove();
        $('#' + id).after(`<div class="error-text text-danger" style="font-size: 12px;"><i class='fas fa-exclamation-circle mr-1'></i>${errorText}</div>`);
        result = false;
      } else {
        $('#' + id).removeClass('border-danger');
        $('#' + id).siblings('.error-text').remove();
      }
    });

    return result;
  }

  function xoaMonHocTrongChuongTrinh(){
    var ctdt = $('#slChuongTrinh2').val();
    var ma_mon = $('#txtMaMon').val();
    var tt = $('#txtStt').val();
    
    $.ajax({
      url: "/admin/xoamonhoctrongct",
      type: "POST",
      data: {
        maCT: ctdt,
        maMon: ma_mon,
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
        console.error("Status: " + status);
        console.error("Error: " + error);
        console.error("Response: " + xhr.responseText);           
      }
    });

  }

  function themMonTrongChuongTrinh(){
    var dataMonHoc = {
      ma: $('#txtMaMon').val(),
      ten: $('#txtTenMon').val(),
      stc: $('#txtSoTC').val(),
      ctdt: $('#slChuongTrinh2').val(),
      kkt: $('#slKhoiKienThuc').val(),
      lhp: $('#slLoaiHocPhan').val(),
      hk: $('#txtHocKy').val(),
    };

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themmonhocvaoctdt",
        type: "POST",
        data: dataMonHoc,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          customThongBao();
          toastr.success("", "Thêm thành công");
          setTimeout(() => {
            location.reload();
          }, "1800");
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
    var chuongTrinh = $('#slChuongTrinh2').val();

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
    formData.append('chuongTrinh', chuongTrinh);

    $.ajax({
      url: '/admin/importmonhoc',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (data) {
        if(data != "Lỗi thêm dữ liệu"){
          $('.modal-center').modal('hide');
          customThongBao();
          toastr.success("", "Thêm thành công");
          setTimeout(() => {
            location.reload();
          }, "1800");
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

  function chonMonHoc(ele, ma, ten, stc){
    $('#txtMaMon').val(ma);
    $('#txtTenMon').val(ten);
    $('#txtSoTC').val(stc);

    var mangID = ['txtMaMon', 'txtTenMon', 'txtSoTC'];
    mangID.forEach(function(id) {
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
  }

  function formThem(){
    customFormThem();

    var mangID = ['txtMaMon',  'txtTenMon', 'txtSoTC'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    $('.dropify-wrapper').siblings('.error-text').remove();
    $('#txtMaMon').prop('readonly', false);

    $('#btnThem').removeClass('d-none');
    $('#btnThemFile').addClass('d-none');
  }

  function chiTiet(ma, ten, stc, loai, khoi, hk){
    $('#ctMa').html(ma);
    $('#ctTen').html(ten);
    $('#ctSTC').html(stc);
    $('#ctLoai').html(loai);
    $('#ctKhoi').html(khoi);
    $('#ctHK').html(hk);

    $('#modalChiTiet').modal('show');
  }

  function formXoa(ele, ma){
    customFormXoa();
    $('#btnThemFile').addClass('d-none');
    var stt = $(ele).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaMon').val(ma);
  }
  
</script>


@include('admin.layout.footer')