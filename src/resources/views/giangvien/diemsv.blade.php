@include('giangvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Bảng điểm</a></li>
          <li class="breadcrumb-item active">Điểm sinh viên</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="row">
        <div class="col-5">
          <div class="d-flex mb-3">
            <div class="mr-3">
              <select class="form-control pr-5" data-toggle="select2" id="slLop" name="slLop">
                @foreach($lop as $lop)
                  <option value="{{$lop->ma_lop}}">{{$lop->ma_lop}}</option>
                @endforeach
              </select>
            </div>
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
            <div class="row">
              <div class="col-12">
                <div class="row"><div class="col-3 font-weight-bold">MSSV:</div><div class="col-9" id="tt_mssv"></div></div>
                <div class="row mt-2"><div class="col-3 font-weight-bold">Họ tên:</div><div class="col-9" id="tt_ten"></div></div>
                <div class="row my-2"><div class="col-3 font-weight-bold">Lớp:</div><div class="col-9" id="tt_lop"></div></div>
              </div>
            </div>
          </div>
        </div>
        <!-- biểu đồ -->
        <div id="chart" class="col-7 mb-4">
          <h5 id="h5" class="d-none">Trung bình học kỳ</h5>
        </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>
<script> 
  function xemDiem(){
    var maSV = $('#slSV').val();
    
    $.ajax({
      url: '/gv/xemdiemsv',
      method: 'GET',
      data:{   
        maSV: maSV,
      },
      success: function(data) {
        // console.log(data);
        bdDiem(data.trung_binh, data.trung_binh_lop, data.trung_binh_khoa)

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

  function bdDiem(tb_sv, tb_lop, tb_khoa){
    const labels = tb_sv.map(item => item.ma_hoc_ky_nien_khoa);
    const tbHocKy = tb_sv.map(item => item.trung_binh_hoc_ky);
    const tbTichLuy = tb_sv.map(item => item.trung_binh_tich_luy);
    const tenHocKy = tb_sv.map(item => item.ten_hoc_ky_nien_khoa);

    const labels_1 = tb_lop.map(item => item.ma_hoc_ky_nien_khoa_lop);
    const tbHocKy_1 = tb_lop.map(item => item.trung_binh_hoc_ky_lop);
    const tbTichLuy_1 = tb_lop.map(item => item.trung_binh_tich_luy_lop);
    const tenHocKy_1 = tb_lop.map(item => item.ten_hoc_ky_nien_khoa_lop);

    const labels_2 = tb_khoa.map(item => item.ma_hoc_ky_nien_khoa_khoa);
    const tbHocKy_2 = tb_khoa.map(item => item.trung_binh_hoc_ky_khoa);
    const tbTichLuy_2 = tb_khoa.map(item => item.trung_binh_tich_luy_khoa);
    const tenHocKy_2 = tb_khoa.map(item => item.ten_hoc_ky_nien_khoa_khoa);

    $('#h5').removeClass('d-none');
    $("#chart #bieuDoDiem").remove();
    $("#chart").append('<canvas id="bieuDoDiem" height="100"></canvas>');

    const ctx = document.getElementById('bieuDoDiem').getContext('2d');
    const myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'TB sinh viên',
          data: tbHocKy,
          borderColor: '#64b0f2',
          fill: false,
          tension: 0.2
        }, {
          label: 'TB cả lớp',
          data: tbHocKy_1,
          borderColor: '#4BC0C0',
          fill: false,
          tension: 0.2
        }, {
          label: 'TB cả khóa',
          data: tbHocKy_2,
          borderColor: '#FFCD59',
          fill: false,
          tension: 0.2
        }]
      },
      options: {
        legend: {
          display: true,
          position: 'top'
        },
        scales: {
          yAxes: [{
            display: true,
          }]
        },
        tooltips: {
          callbacks: {
            title: function(tooltipItem, data) {
              return tenHocKy[tooltipItem[0].index];
            },
            label: function(tooltipItem, data) {
              let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
              let trungBinhHocKy = tooltipItem.value;
              let trungBinhTichLuy;
              if (tooltipItem.datasetIndex === 0) {
                trungBinhTichLuy = tbTichLuy[tooltipItem.index];
              } else {
                trungBinhTichLuy = tbTichLuy_1[tooltipItem.index];
              }
              
              return ['TB học kỳ: ' + trungBinhHocKy, 'TB tích lũy: ' + trungBinhTichLuy];
            }
          }
        },
        annotation: {
            annotations: [{
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: 2, 
                borderColor: 'red',
                borderWidth: 1.5,
            }]
        }
      }
    });
  }

  $(document).ready(function() {
    $('#slLop').on('change', function(){
      var maLop = $('#slLop').val();
      $.ajax({
        url: '/gv/slsinhvien',
        method: 'GET',
        data:{   
          maLop: maLop,
        },
        success: function(data) {
          $('#slSV').empty();
          data.forEach(function(sv) {
            $('#slSV').append('<option value="' + sv.ma_sinh_vien + '">' + sv.ma_sinh_vien+ '</option>');
          });
          $('#slSV').trigger('change');
        },
        error: function(xhr) {
          console.log("Lỗi lấy dữ liệu");
        }
      });
    });
  });

</script>

@include('giangvien.layout.footer')