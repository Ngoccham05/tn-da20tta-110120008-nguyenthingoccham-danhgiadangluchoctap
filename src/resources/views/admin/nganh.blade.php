@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Danh mục</a></li>
          <li class="breadcrumb-item active">Ngành</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách Ngành</h4>

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Mã Ngành</th>
            <th class="text-center">Tên Ngành</th>
            <th class="text-center">Bộ môn</th>
            <th class="text-center">Khoa</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($nganh as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_nganh }}</td>
              <td>{{ $row->ten_nganh }}</td>
              <td>{{ $row->ten_bo_mon }}</td>
              <td>{{ $row->ten_khoa }}</td>
              <td>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua('{{ $row->ma_nganh }}', '{{ $row->ten_nganh }}', '{{ $row->ma_bo_mon }}', this)">
                  <i class="fas fa-pen"></i>
                </a>

                @if($row->count == 0)
                  <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa('{{ $row->ma_nganh }}', this)">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2 disabled" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa('{{ $row->ma_nganh }}', this)">
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
        
        <div class="form- d-none row m-0">
          <div class="col-12 px-0">
            <input type="text" class="form-control" id="txtStt" name="txtStt" readonly>
          </div>
        </div>

        <div id="themSuaForm">
          <div class="form-group row m-0">
            <label for="txtMaNganh" class="col-md-3 col-form-label px-0">Mã Ngành: <span class="text-danger">*</span></label>
            <div class="col-md-9 px-0">
              <input type="text" class="form-control bg-white" id="txtMaNganh" name="txtMaNganh" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="txtTenNganh" class="col-md-3 col-form-label px-0">Tên Ngành: <span class="text-danger">*</span></label>
            <div class="col-md-9 px-0">
              <input type="text" class="form-control" id="txtTenNganh" name="txtTenNganh" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3"> 
            <label for="slBoMon" class="col-md-3 col-form-label px-0">Bộ môn: <span class="text-danger">*</span></label>
            <div class="col-md-9 px-0">
              <select id="slBoMon" name="slBoMon" class="form-control" style="width: 100% !important" data-toggle="select2">
                @foreach ($khoa as $khoa)
                  <optgroup label="Khoa {{ $khoa->ten_khoa }}">
                    @foreach ($khoa->bomon as $bomon)
                      <option value="{{ $bomon->ma_bo_mon }}" >{{ $bomon->ten_bo_mon }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themNganh()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaNganh()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaNganh()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#txtMaNganh, #txtTenNganh').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });
  });

  function kiemTraRong() {
    var mangID = ['txtMaNganh', 'txtTenNganh'];
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

  function themNganh(){  
    var maNganh = $('#txtMaNganh').val();
    var tenNganh = $('#txtTenNganh').val();
    var maBoMon = $('#slBoMon').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themnganh",
        type: "POST",
        data: {
          maNganh: maNganh,
          tenNganh: tenNganh,
          maBoMon: maBoMon,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          console.log(data);
          if(data != "Đã tồn tại"){
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Thêm thành công");

            var table = $('#datatable').DataTable();
            var newRow = table.row.add([
              `${data.num_row}`,
              `${maNganh}`,
              `${tenNganh}`,
              `${data.nganh.ten_bo_mon}`,
              `${data.nganh.ten_khoa}`,
              `<a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formSua('${maNganh}', '${tenNganh}', '${maBoMon}', this)">
                  <i class="fas fa-pen"></i></a>

                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa('${maNganh}', this)">
                <i class="fas fa-trash-alt"></i></a>`
            ]).draw(false).node();

            $(newRow).find('td:first-child').attr('id', 'stt');
            $(newRow).find('td:eq(1)').addClass('text-center');
            $(newRow).attr('id', 'row_' + data.num_row);
          } else{
            customThongBao();
            toastr.error("Dữ liệu đã tồn tại", "Thêm không thành công");
          } 
        },
        error: function(xhr, status, error){
          console.log("Lỗi");                 
        }
      });
    }
  }

  function suaNganh(){
    var tt = $('#txtStt').val();
    var maNganh = $('#txtMaNganh').val();
    var tenNganh = $('#txtTenNganh').val();
    var maBoMon = $('#slBoMon').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/suanganh",
        type: "POST",
        data: {
          maNganh: maNganh,
          tenNganh: tenNganh,
          maBoMon: maBoMon,
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
              <td class="text-center">${maNganh}</td>
              <td>${tenNganh}</td>
              <td>${data.ten_bo_mon}</td>
              <td>${data.ten_khoa}</td>
              <td>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua('${maNganh}', '${tenNganh}', '${maBoMon}', this)">
                  <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa('${maNganh}', this)">
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
          console.log("Lỗi");                 
        }
      });
    }
  }

  function xoaNganh(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaNganh').val();

    $.ajax({
      url: "/admin/xoanganh",
      type: "POST",
      data: {
        maNganh: ma,
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
    $('#txtMaNganh').prop('readonly', false);
    var mangID = ['txtMaNganh', 'txtTenNganh'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    $('#slBoMon').val();
  }

  function formSua(maNganh, tenNganh, maBoMon, element){
    customFormSua();
    var stt = $(element).closest('tr').find('td:first').text();
    $('#txtStt').val(stt);
    $('#txtMaNganh').val(maNganh);
    $('#txtMaNganh').prop('readonly', true);
    $('#txtTenNganh').val(tenNganh);

    var mangID = ['txtTenNganh'];
    mangID.forEach(function(id) {
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });

    $('#slBoMon').select2('trigger', 'select', {
      data: {id: maBoMon}
    });

  }

  function formXoa(ma, element){
    customFormXoa();
    var stt = $(element).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaNganh').val(ma);
  }

</script>

@include('admin.layout.footer')