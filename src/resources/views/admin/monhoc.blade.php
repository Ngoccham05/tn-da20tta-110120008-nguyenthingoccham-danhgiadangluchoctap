@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Chương trình đào tạo</a></li>
          <li class="breadcrumb-item active">Môn học</li>
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

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center" style="min-width: 75px">STT</th>
            <th class="text-center" style="min-width: 120px">Mã môn</th>
            <th class="text-center">Tên môn</th>
            <th class="text-center">Số tín chỉ</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($mon_hoc as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_mon_hoc }}</td>
              <td>{{ $row->ten_mon_hoc }}</td>
              <td class="text-center">{{ $row->so_tin_chi }}</td>
              <td>
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" 
                  onclick="chiTiet('{{$row->ma_mon_hoc}}', '{{$row->ten_mon_hoc}}', '{{$row->so_tin_chi}}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '{{ $row->ma_mon_hoc}}', '{{ $row->ten_mon_hoc }}', '{{ $row->so_tin_chi }}')">
                  <i class="fas fa-pen"></i>
                </a>
                @if($row->count_ct == 0 && $row->count_nhom == 0 && $row->count_bdiem == 0)
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_mon_hoc}}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2 disabled" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_mon_hoc}}')">
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
                    <input type="text" class="form-control bg-white" id="txtMaMon" name="txtMaMon" autocomplete="off">
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
              </div>

              <!-- thêm bằng file -->
              <div class="tab-pane fade show" id="nav-file" role="tabpanel" aria-labelledby="nav-file-tab">
                <div class="form-group row m-0 mt-2">
                  <label for="slChuongTrinh2" class="col-md-3 col-form-label px-0">CT đào tạo:</label>
                  <div class="col-md-9 px-0">
                    <select class="form-control" id="slChuongTrinh2" name="slChuongTrinh2" style="width: 100% !important">
                      @foreach($chuong_trinh as $ct)
                        <option value="{{ $ct->ma_chuong_trinh}}">{{ $ct->ma_chuong_trinh }} - {{ $ct->ten_chuong_trinh }}</option>
                       @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row m-0 mt-2">
                  <label for="fileUpload" class="col-md-3 col-form-label px-0">
                    Chọn tệp: <span class="text-danger">*</span></br>
                    <!-- <a href="/file/Danh_sach_sinh_vien.xlsx" class="font-weight-light" download><i>Mẫu tại đây </i></a> -->
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
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themMonHoc()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThemFile" onclick="themBangFile()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaMonHoc()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaMonHoc()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modalChiTiet" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCenterModalLabel">Thông tin môn học</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body px-4"> 
        <div class="mb-3">
          <div class="font-weight-bold" id="ctMaMon"></div>
          <div class="font-weight-bold mt-2" id="ctTenMon"></div>
          <div class="font-weight-bold mt-2" id="ctStc"></div>  
          <div class="font-weight-bold mt-2" id="tieuDe"></div>  
        </div>          
        
        <table id="tableChiTiet" class="table-bordered w-100">
          <thead>
            <th class="text-center">STT</th>
            <th class="text-center">CT đào tạo</th>
            <th class="text-center">Loại học phần</th>
            <th class="text-center">Khối kiến thức</th>
            <th class="text-center">Học kỳ</th>
          </thead>
          <tbody>

          </tbody>
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
    $('#txtMaMon, #txtTenMon, #txtSoTC').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });

    // kiểm tra số tín chỉ nhập vào
    $('#txtSoTC').on('input', function(){
      let value = $(this).val();
      if(!Number.isInteger(Number(value)) || value <= 0){
        $(this).addClass('border-danger');
        $(this).next('.text-danger').remove();
        $(this).after(`<div class="error-text text-danger" style="font-size: 12px;"><i class='fas fa-exclamation-circle mr-1'></i>Vui lòng nhập số nguyên lớn hơn 0</div>`);
        
        $('#btnThem').prop('disabled', true);
        $('#btnSua').prop('disabled', true);
      } else{
        $(this).next('.text-danger').remove();
        $('#btnThem').prop('disabled', false);
        $('#btnSua').prop('disabled', false);
      }
    })    
  });

  function kiemTraRong() {
    var mangID = ['txtMaMon', 'txtTenMon', 'txtSoTC'];
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

  function chiTiet(ma_mon, ten_mon, stc){
    $.ajax({
      url: '/admin/ttmon',
      method: 'GET',
      data:{
        ma_mon: ma_mon,
      },
      success: function(data) {
        $('#ctMaMon').html('Mã môn: ' + ma_mon);
        $('#ctTenMon').html('Tên môn: ' + ten_mon);
        $('#ctStc').html('Số tín chỉ: ' + stc);

        if(data == ''){
          $('#tieuDe').html('Chi tiết: (Trống)');
          $('#tableChiTiet').addClass('d-none');          
        } else {
          $('#tieuDe').html('Chi tiết:');
          $('#tableChiTiet').removeClass('d-none');   
          $('#tableChiTiet tbody').empty();
            
          data.forEach(function(item, index) {
            var newRow = `
              <tr>
                <td class="text-center py-2">${index + 1}</td>
                <td class="text-center">${item.ma_chuong_trinh} - ${item.ten_chuong_trinh}</td>
                <td class="text-center">${item.ten_loai_hoc_phan}</td>
                <td class="text-center">${item.ten_khoi_kien_thuc}</td>
                <td class="text-center">${item.thu_tu_hoc_ky}</td>
              </tr>`;
            $('#tableChiTiet tbody').append(newRow);
          });
        }       
        $('#modalChiTiet').modal('show');
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  function themMonHoc(){
    var ma = $('#txtMaMon').val();
    var ten = $('#txtTenMon').val();
    var stc = $('#txtSoTC').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themmonhoc",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
          stc: stc,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          if(data != "Đã tồn tại"){
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Thêm thành công");

            var table = $('#datatable').DataTable();
            var newRow = table.row.add([
              `${data.num_row}`,
              `${ma}`,
              `${ten}`,
              `${stc}`,
              ` <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" onclick="chiTiet('${ma}')">
                  <i class="fas fa-eye"></i></a>

                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formSua(this, '${ma}', '${ten}', '${stc}')">
                  <i class="fas fa-pen"></i></a>

                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa(this, '${ma}')">
                  <i class="fas fa-trash-alt"></i></a>`
            ]).draw(false).node();

            $(newRow).find('td:first-child').attr('id', 'stt');
            $(newRow).find('td:eq(1)').addClass('text-center');
            $(newRow).find('td:eq(3)').addClass('text-center');
            $(newRow).attr('id', 'row_' + data.num_row);
          } else{
            customThongBao();
            toastr.error("Dữ liệu đã tồn tại", "Thêm không thành công");
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
        console.log(data);
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

  function suaMonHoc(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaMon').val();
    var ten = $('#txtTenMon').val();
    var stc = $('#txtSoTC').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/suamonhoc",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
          stc: stc,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          if(data){
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Cập nhật thành công");
            $("#row_" + tt).html(`
              <td id="stt">${tt}</td>
              <td class="text-center">${ma}</td>
              <td>${ten}</td>
              <td class="text-center">${stc}</td>
              <td>
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" 
                  onclick="chiTiet('${ma}', '${ten}', '${stc}')">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${ma}', '${ten}', '${stc}')">
                  <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${ma}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
              </td>`);             
            
          } else{
            customThongBao();
            toastr.error("Lỗi khi cập nhật dữ liệu", "Cập nhật không thành công");
          }
        },
        error: function(xhr, status, error){
          customThongBao();
          toastr.error("", "Cập nhật không thành công");                 
        }
      });
    }
  }

  function xoaMonHoc(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaMon').val();

    $.ajax({
      url: "/admin/xoamonhoc",
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

  function formSua(ele, ma, ten, stc, maNganh){
    customFormSua();  
    var mangID = ['txtMaMon',  'txtTenMon', 'txtSoTC'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });

    $('#btnThemFile').addClass('d-none');
    $('#nav-bar').addClass('d-none');

    var stt = $(ele).closest('tr').find('td:first').text();
    $('#txtStt').val(stt);
    $('#txtMaMon').val(ma);
    $('#txtMaMon').prop('readonly', true);
    $('#txtTenMon').val(ten);
    $('#txtSoTC').val(stc);
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