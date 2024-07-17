@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Bảng điểm</a></li>
          <li class="breadcrumb-item active">Nhập điểm</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title -->

<div class="row">
  <div class="col-6">
    <div class="card card-body">
      <h4 class="card-title font-18">Nhập điểm theo lớp</h4>
      <p class="card-text text-center p-3"><i class="fas fa-users" style="font-size: 15vh"></i></p>
      <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThem()" id="btnFormThem"
        data-toggle="modal" data-target=".modal-center">
        <i class="fas fa-plus mr-1 font-12"></i>Thêm
      </button>
    </div>
  </div>

  <div class="col-6">
    <div class="card card-body">
      <h4 class="card-title font-18">Nhập điểm từng sinh viên</h4>
      <p class="card-text text-center p-3"><i class="fas fa-user" style="font-size: 15vh"></i></p>
      <button type="button" class="btn btn-success waves-effect waves-light py-1" onclick="formThemSV()"
        id="btnFormThemSV" data-toggle="modal" data-target=".modal-center">
        <i class="fas fa-plus mr-1 font-12"></i>Thêm
      </button>
    </div>
  </div>
</div> <!-- end row -->

<div class="row">
  <div class="col-12">
    <div class="card card-body">
      <h4 class="card-title font-18">Hướng dẫn</h4>
      <embed src="/images/huongdan/Huong-dan.pdf" type="application/pdf" width="100%" height="700px" />
    </div>
  </div>
</div>

<!-- modal thêm -->
<div id="modalThemSua" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" id="model-resize">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCenterModalLabel">Nhập điểm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body">
        <div id="themSuaForm">
          <div class="d-flex mt-2">
            <label for="fileUpload" class="col-form-label p-1 pt-2 w-25">
              Chọn tệp: <span class="text-danger">*</span></br>
              <a href="/excelmau/Diem_ca_nhan.xls" id="mauNhieu" class="font-weight-light" download><i>Mẫu tại đây</i></a>
              <a href="/excelmau/Diem_ca_nhan.xls" id="mau1" class="font-weight-light" download><i>Mẫu tại đây </i></a>
            </label>
            <div class="w-100">
              <input type="file" class="dropify" data-height="100" id="fileUpload" name="fileUpload">
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThemNhSV"
          onclick="themDiemNhieuSV()">Lưu</button>
        <button type="button" class="btn btn-primary waves-effect waves-light" id="btnThemSV"
          onclick="themSV()">Lưu</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  function kiemTraFile(fileInput) {
    // Kiểm tra đã chọn file hay chưa
    if (fileInput.files.length === 0) {
      $('.dropify-wrapper').siblings('.error-text').remove();
      $('.dropify-wrapper').after(`<div class="error-text text-danger pt-1" style="font-size: 14px;">
        <i class='fas fa-exclamation-circle mr-1'></i>Vui lòng chọn một tệp dữ liệu (.xls hoặc .xlsx)</div>`);
      return false;
    }

    var fileName = fileInput.files[0].name;
    var isExcelFile = /\.(xlsx|xls)$/i.test(fileName);

    // kiểm tra kiểu file
    if (isExcelFile == false) {
      $('.dropify-wrapper').siblings('.error-text').remove();
      $('.dropify-wrapper').after(`<div class="error-text text-danger pt-1" style="font-size: 14px;">
        <i class='fas fa-exclamation-circle mr-1'></i>Định dạng tệp không phù hợp. Vui lòng sử dụng tệp .xls hoặc .xlsx</div>`);
      return false;
    }

    return true;
  }

  function sendDataToPy() {
    var formData = new FormData();
    var fileInput = document.getElementById('fileUpload');

    if (kiemTraFile(fileInput) == true) {
      formData.append('file', fileInput.files[0]);
      $('#btnThemNhSV').addClass('disabled');

      customThongBaoCho();
      var toast = toastr.info('Vui lòng chờ cho đến khi quá trình hoàn tất', 'Đang lưu');

      // gửi dữ liệu sang python
      fetch('http://127.0.0.1:5000/upload', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          themNhSV(data.output_file_mon, data.output_file_hk, toast);
        })
        .catch((error) => {
          toastr.clear(toast);
          $('#btnThemNhSV').removeClass('disabled');
          customThongBao();
          toastr.error('Vui lòng dùng đúng mẫu dữ liệu đã được cung cấp', 'Thêm không thành công');
        });
    }
  }

  function themNhSV(diem_mon, diem_hk, toast) {
    var diem_mon = diem_mon.replace(/\\/g, "/").substring(diem_mon.indexOf('/') + 1);
    var diem_hk = diem_hk.replace(/\\/g, "/").substring(diem_hk.indexOf('/') + 1);

    $.ajax({
      url: '/admin/nhapdiemfile',
      method: 'POST',
      data: {
        diem_mon: diem_mon,
        diem_hk: diem_hk,
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (data) {
        toastr.clear(toast);
        $('#modalThemSua').modal('hide');
        $('#btnThemNhSV').removeClass('disabled');
        if (data = "Thành công") {
          customThongBao();
          toastr.success('', 'Đã thêm thành công');
        } else {
          customThongBao();
          toastr.error('Lỗi định dạng dữ liệu', 'Thêm thành công');
        }

      },
      error: function (xhr) {
        console.log(xhr.responseJSON.error);
      }
    });
  }

  function themDiemNhieuSV() {
    var formData = new FormData();
    var fileInput = document.getElementById('fileUpload');

    if (kiemTraFile(fileInput) == true) {
      formData.append('file', fileInput.files[0]);

      $.ajax({
        url: '/admin/nhapdiemfile',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          // console.log(data);
          if (data == "Thành công") {
            customThongBao();
            toastr.success('', 'Đã thêm thành công');
          } else {
            customThongBao();
            toastr.erorr('Lỗi định dạng dữ liệu', 'Thêm không thành công');
          }
        },
        error: function (xhr) {
          console.log(xhr.responseJSON.error);
        }
      });
    }
  }

  function themSV() {
    var formData = new FormData();
    var fileInput = document.getElementById('fileUpload');

    if (kiemTraFile(fileInput) == true) {
      formData.append('file', fileInput.files[0]);

      $.ajax({
        url: '/admin/nhapdiem1sv',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          if (data == "Thành công") {
            customThongBao();
            toastr.success('', 'Đã thêm thành công');
          } else {
            customThongBao();
            toastr.erorr('Lỗi định dạng dữ liệu', 'Thêm không thành công');
          }

        },
        error: function (xhr) {
          console.log(xhr.responseJSON.error);
        }
      });
    }
  }

  function formThem() {
    $('#mauNhieu').removeClass('d-none');
    $('#btnThemNhSV').removeClass('d-none')
    $('#mau1').addClass('d-none');
    $('#btnThemSV').addClass('d-none');
    $('.dropify-wrapper').siblings('.error-text').remove();
  }

  function formThemSV() {
    $('#mauNhieu').addClass('d-none');
    $('#btnThemNhSV').addClass('d-none')
    $('#mau1').removeClass('d-none');
    $('#btnThemSV').removeClass('d-none');
    $('.dropify-wrapper').siblings('.error-text').remove();
  }

</script>

@include('admin.layout.footer')