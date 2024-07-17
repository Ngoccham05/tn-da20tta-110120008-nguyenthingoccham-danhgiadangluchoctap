@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Quản lý lớp</a></li>
          <li class="breadcrumb-item active">Lớp</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách lớp</h4>

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Mã lớp</th>
            <th class="text-center">Tên lớp</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($lop as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_lop }}</td>
              <td>{{ $row->ten_lop }}</td>
              <td>
                <a href="/admin/dslop/{{encrypt($row->ma_lop)}}" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '{{ $row->ma_lop}}', '{{ $row->ten_lop }}', '{{ $row->ma_chuong_trinh }}', '{{$row->ma_giang_vien}}')">
                  <i class="fas fa-pen"></i>
                </a>
                @if($row->count_sv == 0)
                <a href="#" class="btn btn-danger py-1 px-2 btn-form-xoa" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_lop}}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2 disabled btn-form-xoa" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_lop}}')">
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
          <div class="form-group row m-0">
            <label for="txtMaLop" class="col-md-4 col-form-label px-0">Mã lớp: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <input type="text" class="form-control bg-white" id="txtMaLop" name="txtMaLop" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="txtTenLop" class="col-md-4 col-form-label px-0">Tên lớp: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <input type="text" class="form-control" id="txtTenLop" name="txtTenLop" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="slChuongTrinh" class="col-md-4 col-form-label px-0">CT đào tạo: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <select id="slChuongTrinh" name="slChuongTrinh" class="form-control">
                @foreach ($chuong_trinh as $ct)
                  <option value="{{ $ct->ma_chuong_trinh }}" >{{ $ct->ma_chuong_trinh }} - {{ $ct->ten_chuong_trinh }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="slCoVan" class="col-md-4 col-form-label px-0">Cố vấn học tập <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <select id="slCoVan" name="slCoVan" class="form-control" style="width: 100% !important" data-toggle="select2">
                @foreach ($giang_vien as $gv)
                  @if($gv->count == 0)
                  <option value="{{ $gv->ma_giang_vien }}">{{ $gv->ma_giang_vien }} - {{ $gv->ho_ten }}</option>
                  @else
                  <option disabled value="{{ $gv->ma_giang_vien }}">{{ $gv->ma_giang_vien }} - {{ $gv->ho_ten }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themLop()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaLop()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaLop()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#txtMaLop, #txtTenLop, #slChuongTrinh').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });

  });

  function kiemTraRong() {
    var mangID = ['txtMaLop', 'txtTenLop', 'slChuongTrinh', 'slCoVan'];
    var result = true;

    mangID.forEach(function(id) {
      var value = $('#' + id).val();
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

  function themLop(){
    var ma = $('#txtMaLop').val().toUpperCase();
    var ten = $('#txtTenLop').val();
    var chuongTrinh = $('#slChuongTrinh').val();
    var tenChuongTrinh = $('#slChuongTrinh option:selected').text();
    var gv = $('#slCoVan').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themlop",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
          chuongTrinh: chuongTrinh,
          gv: gv
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          if(data != "Đã tồn tại"){
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Thêm thành công");
            $("#slCoVan option[value="+gv+"]").attr("disabled", "disabled");

            var table = $('#datatable').DataTable();
            var newRow = table.row.add([
              `${data.num_row}`,
              `${ma}`,
              `${ten}`,
              `<a href="/admin/dslop/${data.ma_lop}" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px">
                  <i class="fas fa-eye"></i></a>

                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formSua(this, '${ma}', '${ten}', '${chuongTrinh}', '${gv}')">
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

  function suaLop(){
    var ma = $('#txtMaLop').val().toUpperCase();
    var ten = $('#txtTenLop').val();
    var chuongTrinh = $('#slChuongTrinh').val();
    var gv = $('#slCoVan').val();

    var tt = $('#txtStt').val();
    var row = $('#datatable tbody tr').filter(function() {
      return $(this).find('td:first').text().trim() == tt;
      console.log(row);
    });
    var btnXoa = row.find('.btn-form-xoa').hasClass('disabled');

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/sualop",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
          chuongTrinh: chuongTrinh,
          gv: gv,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          if(data != "Đã tồn tại"){
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Cập nhật thành công");
            
            $("#row_" + tt).html(`
              <td id="stt">${tt}</td>
              <td class="text-center">${ma}</td>
              <td>${ten}</td>
              <td>
                <a href="/admin/dslop/${data.ma_lop}" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${ma}', '${ten}', '${chuongTrinh}', '${gv}')">
                  <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger py-1 px-2 btn-form-xoa" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '${ma}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
              </td>`);             
            
          } else if(data == "Đã tồn tại"){
            customThongBao();
            toastr.error("Dữ liệu đã tồn tại", "Cập nhật không thành công");
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

  function xoaLop(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaLop').val().toUpperCase();

    $.ajax({
      url: "/admin/xoalop",
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
    var mangID = ['txtMaLop',  'txtTenLop', 'slChuongTrinh'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    $('#txtMaLop').prop('readonly', false);
    $('#slCoVan').val();
  }

  function formSua(ele, ma, ten, chuongTrinh, gv){
    customFormSua();  
    var mangID = ['txtMaLop',  'txtTenLop', 'slChuongTrinh'];
    mangID.forEach(function(id) {
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    var stt = $(ele).closest('tr').find('td:first').text();
    $('#txtStt').val(stt);
    $('#txtMaLop').val(ma);
    $('#txtMaLop').prop('readonly', true);
    $('#txtTenLop').val(ten);
    $('#slChuongTrinh').val(chuongTrinh);
    $('#slCoVan').select2('trigger', 'select', {
      data: {id: gv}
    });  
  }

  function formXoa(ele, ma){
    customFormXoa();
    var stt = $(ele).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaLop').val(ma);
  }

</script>

@include('admin.layout.footer')