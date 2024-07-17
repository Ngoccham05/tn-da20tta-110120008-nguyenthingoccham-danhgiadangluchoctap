@include('admin.layout.header')
<!-- start page title -->
<style>
  .table-container {
    overflow: auto;
    height: 450px;
  }

  thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background-color: #64b0f2;
    color: #f8f9fa;
    border-right: none !important;
  }
</style>


<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Chương trình đào tạo</a></li>
          <li class="breadcrumb-item active">Nhóm môn</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title -->

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3 ">
        <div id="mact" class="d-none">{{$mact}}</div>
        <h4 class="header-title font-18 m-0 mr-auto">Nhóm môn</h4>
          <button type="button" class="btn btn-success waves-effect waves-light py-1 mr-2" id="btnThemCot"
            onclick="themCot()">
            <i class="fas fa-plus mr-1 font-12"></i>Thêm nhóm
          </button>
          <button type="button" class="btn btn-success waves-effect waves-light py-1" id="btnLuu" onclick="luu()">
            <i class="fas fa-check mr-1 font-12"></i>Lưu
          </button>
      </div>

      <!-- Bảng hiện tại -->
      <div class="table-container">
        @if($nhom_mon_col != '0')
      <table id="customtable" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>STT</th>
          <th>Mã môn</th>
          <th>Tên môn</th>
          <th>Bắt buộc</th>

          @foreach($nhom_mon_col as $nhom)
          <th>
            <textarea class="form-control form-control-sm" id="col-name" name="col-name" value="" rows="1">{{substr(strstr($nhom->ten_nhom_mon, '-'),1)}}</textarea>
          </th>
          @endforeach
        </tr>
        </thead>
        <tbody id="table-container">
          @php  $stt = 1; @endphp
        @foreach($mon_hoc as $row)
          <tr>
            <td>{{$stt++}}</td>
            <td>{{$row->ma_mon_hoc}}</td>
            <td class="text-left">{{$row->ten_mon_hoc}}</td>
            <td>{{$row->ten_loai_hoc_phan == "Bắt buộc" ? "x" : ""}}</td>
            @foreach($nhom_mon_col as $nhom)
          <td>
          <div class="form-check">
            <input class="form-check-input position-static" type="checkbox" value="{{$row->ma_mon_hoc}}"
            name="rd{{$row->ma_mon_hoc}}" aria-label="..." {{ $row->nhom_mon->contains('ma_nhom_mon', $nhom->ma_nhom_mon) ? 'checked' : '' }}>
          </div>
          </td>
        @endforeach
      </tr>
    @endforeach
        </tbody>
      </table>
    @else
    <table id="customtable" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th>STT</th>
        <th>Mã môn</th>
        <th>Tên môn</th>
        <th>Bắt buộc</th>
        <th><textarea class="form-control form-control-sm" id="col-name" name="col-name" value=""
          rows="1"></textarea></th>
      </tr>
      </thead>
      <tbody id="table-container">
        @php  $stt = 1; @endphp
        @foreach($mon_hoc as $row)
          <tr>
            <td>{{$stt++}}</td>
            <td>{{$row->ma_mon_hoc}}</td>
            <td class="text-left">{{$row->ten_mon_hoc}}</td>
            <td>{{$row->ten_loai_hoc_phan == "Bắt buộc" ? "x" : ""}}</td>
            <td>
            <div class="form-check">
              <input class="form-check-input position-static" type="checkbox" value="{{$row->ma_mon_hoc}}" name="rd{{$row->ma_mon_hoc}}" aria-label="...">
            </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
      </div>

    </div>
  </div>
</div> <!-- end row -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  function kiemTraRong() {
    var rs = true;
    $("#customtable tbody tr").each(function () {
      var radioChecked = $(this).find("input[type='checkbox']:checked");
      if (radioChecked.length == 0) {
        rs = false;
      }
    });
    return rs;
  }

  function capNhat() {
    var mact = $('#mact').text();
    var data = laydl();
  }

  function luu() {
    var mact = $('#mact').text();
    var data = laydl();

    if (kiemTraRong() == true) {
      $.ajax({
        url: "/admin/themnhommon",
        type: "POST",
        data: {
          mact: mact,
          data: data,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
          // console.log(data);
          customThongBao();
          toastr.success("", "Thêm thành công");
          setTimeout(() => window.location.reload(), 1300);
        },
        error: function (xhr, status, error) {
          // console.log(error);
          customThongBao();
          toastr.error("", "Thêm không thành công");
        }
      });
    } else {
      customThongBao();
      toastr.error("Vui lòng nhập đầy đủ thông tin", "Thêm không thành công");
    }
  }

  function laydl() {
    var data = [];
    var columns = [];
    var numColumns = $("#customtable tbody tr:first td").length;

    $("#customtable thead th").each(function (index) {
      if (index >= 4 && index < numColumns) {
        var textarea = $(this).find("textarea");
        if (textarea.length) {
          columns.push([textarea.val()]);
        } else {
          columns.push([]);
        }
      }
    });

    $("#customtable tbody tr").each(function () {
      for (var col = 4; col < numColumns; col++) {
        var checkbox = $(this).find(`td:nth-child(${col + 1}) input[type='checkbox']:checked`);
        if (checkbox.length) {
          columns[col - 4].push(checkbox.val());
        }
      }
    });

    for (var i = 0; i < columns.length; i++) {
      data.push(columns[i]);
    }

    return data;
  }

  var tt = 1;
  function themCot() {
    tt = tt + 1;
    $("#customtable thead tr").append(`<th><textarea class="form-control form-control-sm" id="col-name" name="col-name" value="" rows="1"></textarea></th>`);

    $("#customtable tbody tr").each(function () {
      var tenMonHoc = $(this).find("td:first").text();
      var checkboxName = $(this).find("input[type='checkbox']").attr("name");
      var checkboxValue = $(this).find("input[type='checkbox']").val();

      $(this).append(`
        <td>
          <div class="form-check">
            <input class="form-check-input position-static" type="checkbox" name="${checkboxName}" value="${checkboxValue}" aria-label="...">
          </div>
        </td>
      `);
    });

    $('#ds_nhom').append(`
      <div id="nhom${tt}" class="card-box border border-secondary rounded py-2 mt-2">
        Nhóm ${tt}
      </div>
    `);
  }
</script>


@include('admin.layout.footer')