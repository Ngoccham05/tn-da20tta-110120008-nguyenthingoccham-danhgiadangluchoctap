@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Danh mục</a></li>
          <li class="breadcrumb-item active">Khối kiến thức</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách khối kiến thức</h4>

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Tên</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($khoi_kien_thuc as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td>{{ $row->ten_khoi_kien_thuc }}</td>
              <td>
                <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formSua('{{ encrypt($row->ma_khoi_kien_thuc) }}', '{{ $row->ten_khoi_kien_thuc }}', this)">
                  <i class="fas fa-pen"></i>
                </a>
                @if($row->count == 0)
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa('{{ encrypt($row->ma_khoi_kien_thuc) }}', this)">
                  <i class="fas fa-trash-alt"></i>
                </a>
                @else
                <a href="#" class="btn btn-danger py-1 px-2 disabled" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa('{{ encrypt($row->ma_khoi_kien_thuc) }}', this)">
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
            <input type="text" class="form-control" id="txtMaKKT" name="txtMaKKT" readonly>
          </div>
        </div>
        <div id="themSuaForm" class="form-group row m-0">
          <label for="txtTenKKT" class="col-md-4 col-form-label px-0">Tên khối kiến thức: <span class="text-danger">*</span></label>
          <div class="col-md-8 px-0">
            <input type="text" class="form-control" id="txtTenKKT" name="txtTenKKT" autocomplete="off">
            <span id="bao_loi"></span>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themKhoiKienThuc()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaKhoiKienThuc()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaKhoiKienThuc()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> 
  $(document).ready(function() {
    $('#txtTenKKT').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });
  });

  function kiemTraRong(){
    var mangID = ['txtTenKKT'];
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

  function themKhoiKienThuc(){
    var ten = $('#txtTenKKT').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themkhoikienthuc",
        type: "POST",
        data: {
          ten: ten,
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
              `${ten}`,
              `<a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formSua('${data.id}', '${ten}', this)">
                  <i class="fas fa-pen"></i></a>

              <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa('${data.id}', this)">
                <i class="fas fa-trash-alt"></i></a>`
            ]).draw(false).node();
            $(newRow).find('td:first-child').attr('id', 'stt');
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

  function suaKhoiKienThuc(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaKKT').val();
    var ten = $('#txtTenKKT').val();

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/suakhoikienthuc",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){  
          if(data == "Đã tồn tại"){
            customThongBao();
            toastr.error("Dữ liệu đã tồn tại", "Cập nhật không thành công");
          } else{
            customThongBao();
            toastr.success("", "Cập nhật thành công");
            $('.modal-center').modal('hide');

            $("#row_" + tt).html(`
                <td id="stt">${tt}</td>
                <td>${ten}</td>
                <td>
                  <a href="#" class="btn btn-primary py-1 px-2 mr-1" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formSua('${data.id}', '${ten}', this)">
                    <i class="fas fa-pen"></i>
                  </a>
                  <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa('${ma}', this)">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </td>`);
          }
        },
        error: function(xhr, status, error){
          console.log("Lỗi");                 
        }
      });
    }
  }

  function xoaKhoiKienThuc(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMaKKT').val();

    $.ajax({
      url: "/admin/xoakhoikienthuc",
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
    var mangID = ['txtTenKKT'];
    mangID.forEach(function(id) {
      $('#' + id).val('');
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
  }

  function formSua(ma, ten, element){
    customFormSua();
    var stt = $(element).closest('tr').find('td:first').text();
    $('#txtStt').val(stt);
    $('#txtMaKKT').val(ma);
    $('#txtTenKKT').val(ten);

    var mangID = ['txtTenKKT'];
    mangID.forEach(function(id) {
      $('#' + id).removeClass('border-danger');
      $('#' + id).siblings('.error-text').remove();
    });
    
  }

  function formXoa(ma, element){
    customFormXoa();
    var stt = $(element).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMaKKT').val(ma);
  }

</script>

@include('admin.layout.footer')