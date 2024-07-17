@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Danh mục</a></li>
          <li class="breadcrumb-item active">Học kỳ - niên khóa</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách Học kỳ - niên khóa</h4>

        <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" data-toggle="modal" data-target=".modal-center">
          <i class="fas fa-plus mr-1 font-12"></i>Thêm</button>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <th class="text-center">Mã Học kỳ - Niên khóa</th>
            <th class="text-center">Tên Học kỳ - Niên khóa</th>
            <th class="text-center">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($hoc_ky_nien_khoa as $row)
            <tr id="row_{{ $stt }}">
              <td id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_hoc_ky_nien_khoa }}</td>
              <td>{{ $row->ten_hoc_ky_nien_khoa }}</td>
              <td>
                <a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                  onclick="formXoa('{{ encrypt($row->ma_hoc_ky_nien_khoa) }}', this)">
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
            <input type="text" class="form-control" id="txtMa" name="txtMa" readonly>
          </div>
        </div>
        <div id="themSuaForm">
          <div class="form-group row m-0">
            <label for="slHocKy" class="col-md-3 col-form-label px-0">Học kỳ: <span class="text-danger">*</span></label>
            <div class="col-md-9 px-0">
              <select class="form-control" id="slHocKy" name="slHocKy">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
            </div>
          </div>

          <div class="form-group row m-0 mt-3">
            <label for="" class="col-md-3 col-form-label px-0">Năm học: <span class="text-danger">*</span></label>
            <div class="col-md-9 px-0">
              <select class="form-control" id="slNamHoc" name="slNamHoc"></select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThem" onclick="themHKNK()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSua" onclick="suaHKNK()">Lưu</button>
        <button type="button" class="btn btn-danger waves-effect waves-light" id="btnXoa" onclick="xoaHKNK()">Xóa</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> 
  $(document).ready(function() {
    $('#slHocKy, #slNamHoc').on('input', function() {
      if ($(this).val().trim()) {
        $(this).removeClass('border-danger');
        $(this).siblings('.error-text').remove(); 
      }
    });

    // tạo option năm học 
    var select = $("#slNamHoc");
    var currentYear = new Date().getFullYear();
    var currentMonth = new Date().getMonth() + 1;

    var startYear;
    if (currentMonth < 9) {
      startYear = currentYear - 1; 
    } else {
      startYear = currentYear;
    }

    for (var year = startYear - 3; year <= startYear + 3; year++) {
      var option = $("<option></option>")
        .attr("value", year + " - " + (year + 1))
        .text(year + " - " + (year + 1));
      select.append(option);
    }
    var defaultYear = currentMonth < 9 ? startYear : currentYear;
    $("#slNamHoc").val(defaultYear + " - " + (defaultYear + 1));
  });

  function kiemTraRong(){
    var mangID = ['#slHocKy, #slNamHoc'];
    var result = true;

    mangID.forEach(function(id) {
      var value = $(id).val();
      var errorText = `Vui lòng điền trường này`;

      if (!value) {
        $(id).addClass('border-danger');
        $(id).siblings('.error-text').remove();
        $(id).after(`<div class="error-text text-danger" style="font-size: 12px;"><i class='fas fa-exclamation-circle mr-1'></i>${errorText}</div>`);
        result = false;
      } else {
        $(id).removeClass('border-danger');
        $(id).siblings('.error-text').remove();
      }
    });

    return result;
  }

  function themHKNK(){
    var hocKy = $('#slHocKy').val();
    var namHoc = $('#slNamHoc').val();

    var namHocP = namHoc.split(" - ");
    var namDau = namHocP[0].slice(-2);
    var namSau = namHocP[1].slice(-2);
    
    var ma = hocKy + namDau + namSau;
    var ten = "Học kỳ " + hocKy + ", năm học " + namHoc;  

    if(kiemTraRong() == true){
      $.ajax({
        url: "/admin/themhockynienkhoa",
        type: "POST",
        data: {
          ma: ma,
          ten: ten,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){ 
          console.log(ma);
          if(data != "Đã tồn tại"){
            $('.modal-center').modal('hide');
            customThongBao();
            toastr.success("", "Thêm thành công");

            var table = $('#datatable').DataTable();
            var newRow = table.row.add([
              `${data.num_row}`,
              `${ma}`,
              `${ten}`,
              `<a href="#" class="btn btn-danger py-1 px-2" style="font-size: 12px" data-toggle="modal" data-target=".modal-center"
                    onclick="formXoa('${data.id}', this)">
                <i class="fas fa-trash-alt"></i></a>`
            ]).draw(false).node();
            $(newRow).find('td:eq(1)').addClass('text-center');
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

  function xoaHKNK(){
    var tt = $('#txtStt').val();
    var ma = $('#txtMa').val();

    $.ajax({
      url: "/admin/xoahockynienkhoa",
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
    var mangID = ['#slHocKy', '#slNamHoc'];
    mangID.forEach(function(id) {
      $(id).val('');
      $(id).removeClass('border-danger');
      $(id).siblings('.error-text').remove();
    });
  }

  function formXoa(ma, element){
    customFormXoa();
    var stt = $(element).closest('tr').attr('id').split('_')[1];
    $('#txtStt').val(stt);
    $('#txtMa').val(ma);
  }

</script>

@include('admin.layout.footer')