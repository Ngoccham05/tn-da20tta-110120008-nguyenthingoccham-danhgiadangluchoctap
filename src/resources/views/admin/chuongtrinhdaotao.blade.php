@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Chương trình đào tạo</a></li>
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
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách chương trình đào tạo</h4>

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Mã CT</th>
            <th class="text-center">Tên chương trình</th>
            <th class="text-center">Số quyết định</th>
            <th class="text-center">Ngành</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($ctdt as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_chuong_trinh }}</td>
              <td>{{ $row->ten_chuong_trinh }}</td>
              <td>{{ $row->so_quyet_dinh }}</td>
              <td>{{ $row->ten_nganh }}</td>
              <td>
                <a href="/admin/ctdt/{{encrypt($row->ma_chuong_trinh)}}" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '{{ $row->ma_chuong_trinh}}', '{{ $row->ten_chuong_trinh }}', '{{ $row->so_quyet_dinh }}', '{{ $row->ma_nganh }}')">
                  <i class="fas fa-pen"></i>
                </a>
                @if($row->countlop == 0 && $row->countct == 0)
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_chuong_trinh}}')">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2 disabled" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa(this, '{{ $row->ma_chuong_trinh}}')">
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
            <label for="txtMaCT" class="col-md-4 col-form-label px-0">Mã chương trình: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <input type="text" class="form-control bg-white" id="txtMaCT" name="txtMaCT" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="txtTenCT" class="col-md-4 col-form-label px-0">Tên chương trình: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <input type="text" class="form-control" id="txtTenCT" name="txtTenCT" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="txtSoQD" class="col-md-4 col-form-label px-0">Số quyết định: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <input type="text" class="form-control" id="txtSoQD" name="txtSoQD" autocomplete="off">
            </div>
          </div>

          <div class="form-group row m-0 mt-3"> 
            <label for="slNganh" class="col-md-4 col-form-label px-0">Ngành đào tạo: <span class="text-danger">*</span></label>
            <div class="col-md-8 px-0">
              <select id="slNganh" name="slNganh" class="form-control">
                @foreach ($nganh as $nganh)
                  <option value="{{ $nganh->ma_nganh }}" >{{ $nganh->ten_nganh }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themChuongTrinh()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaChuongTrinh()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaChuongTrinh()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('#txtMaCT, #txtTenCT, #txtSoQD').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });
  });

  function kiemTraRong() {
    var mangID = ['txtMaCT', 'txtTenCT', 'txtSoQD'];
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

  function themChuongTrinh(){
    var ma = $('#txtMaCT').val();
    var ten = $('#txtTenCT').val();
    var soQD = $('#txtSoQD').val();
    var maNganh = $('#slNganh').val();
    var tenNganh = $('#slNganh option:selected').text();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themctdaotao",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
          soQD: soQD,
          maNganh: maNganh,
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
              `${soQD}`,
              `${tenNganh}`,
              `
                <a href="/admin/ctdt/${data.id}" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formSua(this, '${ma}', '${ten}', '${soQD}', '${maNganh}')">
                  <i class="fas fa-pen"></i></a>

                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa(this, '${ma}')">
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
          toastr.error("", "Cập nhật không thành công");                 
        }
      });
    }
  }

  function suaChuongTrinh(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaCT').val();
    var ten = $('#txtTenCT').val();
    var soQD = $('#txtSoQD').val();
    var maNganh = $('#slNganh').val();
    var tenNganh = $('#slNganh option:selected').text();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/suactdaotao",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
          soQD: soQD,
          maNganh: maNganh,
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
              <td>${soQD}</td>
              <td>${tenNganh}</td>
              <td>
                <a href="/admin/ctdt/${data.id}" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua(this, '${ma}', '${ten}', '${soQD}', '${maNganh}')">
                  <i class="fas fa-pen"></i>
                </a>
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
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
          toastr.error("", "Cập nhật không thành công");                 
        }
      });
    }
  }

  function xoaChuongTrinh(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaCT').val();

    $.ajax({
      url: "/admin/xoactdaotao",
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
    var mangID = ['txtMaCT',  'txtTenCT', 'txtSoQD'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    $('txtMaCT').prop('readonly', false);
  }

  function formSua(ele, ma, ten, soQD, maNganh){
    customFormSua();  
    var mangID = ['txtMaCT',  'txtTenCT', 'txtSoQD'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });

    var stt = $(ele).closest('tr').find('td:first').text();
    $('#txtStt').val(stt);
    $('#txtMaCT').val(ma);
    $('#txtMaCT').prop('readonly', true);
    $('#txtTenCT').val(ten);
    $('#txtSoQD').val(soQD);
    $('#slNganh').val(maNganh);
  }

  function formXoa(ele, ma){
    customFormXoa();
    var stt = $(ele).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaCT').val(ma);
  }

</script>

@include('admin.layout.footer')