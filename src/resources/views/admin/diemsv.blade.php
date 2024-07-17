@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Bảng điểm</a></li>
          <li class="breadcrumb-item active">Xem theo lớp</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex mb-3">
        <div class="mr-3">
          <select class="form-control pr-5" data-toggle="select2" id="slSV" name="slSV">
            @foreach($sv as $sv)
              <option value="{{$sv->ma_sinh_vien}}">{{$sv->ma_sinh_vien}}</option>
            @endforeach
          </select>
        </div>

        <div>
          <button type="button" class="btn btn-success waves-effect waves-light" onclick="xemDiem()">
            <i class="fas fa-filter font-16"></i></button>
        </div>
      </div>

      <div id="row_tt" class="d-none">
        <div class="row"><div class="col-1 font-weight-bold">MSSV:</div><div class="col-11" id="tt_mssv"></div></div>
        <div class="row mt-2"><div class="col-1 font-weight-bold">Họ tên:</div><div class="col-11" id="tt_ten"></div></div>
        <div class="row my-2"><div class="col-1 font-weight-bold">Lớp:</div><div class="col-11" id="tt_lop"></div></div>
      </div>

      <table id="customtable" class="table table-bordered dt-responsive nowrap table-custom d-none" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center sorting_disabled">STT</th>
            <th class="text-center">Mã môn</th>
            <th class="text-center">Tên môn</th>
            <th class="text-center">STC</th>
            <th class="text-center">ĐTK L1 (10)</th>
            <th class="text-center">ĐTK L2 (10)</th>
            <th class="text-center">ĐTK (4)</th>
            <th class="text-center">ĐTK (C)</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> 
  function xemDiem(){
    var maSV = $('#slSV').val();
    
    $.ajax({
      url: '/admin/xemdiemsv',
      method: 'GET',
      data:{   
        maSV: maSV,
      },
      success: function(data) {
        // console.log(data);

        $('#row_tt').removeClass("d-none");
        $('#tt_mssv').html(data.sv.ma_sinh_vien);
        $('#tt_ten').html(data.sv.ho_ten);
        $('#tt_lop').html(data.sv.ten_lop);

        if ($.fn.DataTable.isDataTable('#customtable')) {
          $('#customtable').DataTable().destroy();
        }

        // format table
        var table = $('#customtable').DataTable({
          paging: false,
          info: false,
          searching: false,
          columnDefs: [
            { "orderable": false, "targets": "_all" } 
          ] 
        });

        var hk_hien_tai = null;
        var tbody = $('#customtable tbody');
        $.each(data.diem_hoc_ky, function(index, dhk) {
          var stt = 0;
          // học kỳ
          var newRow = $('<tr>'); 
          newRow.append($('<td colspan="8" class="text-left bg-primary text-light font-weight-bold">').text(dhk.ten_hoc_ky_nien_khoa));
          tbody.append(newRow);

          // điểm môn
          $.each(data.diem, function(index, item) {
            if (dhk.ma_hoc_ky_nien_khoa == item.ma_hoc_ky_nien_khoa) {
              stt = stt + 1
              var diem = (item.diem_he_4.length == 1) ? item.diem_he_4 + '.0' : item.diem_he_4;
              var newRow = $('<tr>');
              newRow.append($('<td>').text(stt));
              newRow.append($('<td>').text(item.ma_mon_hoc));
              newRow.append($('<td class="text-left">').text(item.ten_mon_hoc));
              newRow.append($('<td>').text(item.so_tin_chi));
              newRow.append($('<td>').text(item.diem_lan_1));
              newRow.append($('<td>').text(item.diem_lan_2));
              newRow.append($('<td>').text(diem));
              newRow.append($('<td>').text(item.diem_chu));
              tbody.append(newRow); 
            }
          });

          // điểm trung bình học kỳ
          
          var newRow = $('<tr>'); 
          newRow.append($('<td colspan="2" class="text-left font-weight-bold py-2 border-right-0">').text('Trung bình học kỳ: ' + dhk.trung_binh_hoc_ky));
          newRow.append($('<td colspan="6" class="text-left font-weight-bold py-2">').text('Trung bình tích lũy: ' + dhk.trung_binh_tich_luy));
          tbody.append(newRow);
        });

        $('#customtable').removeClass('d-none');
        $('#customtable tbody tr .dataTables_empty').remove();
        $('#customtable thead tr th:eq(0)').removeClass('sorting_asc');
        $('#customtable thead tr th:eq(0)').addClass('sorting_disabled');
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

</script>

@include('admin.layout.footer')