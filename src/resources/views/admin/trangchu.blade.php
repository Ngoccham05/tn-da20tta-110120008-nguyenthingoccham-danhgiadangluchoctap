@include('admin.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Quản trị</a></li>
          <li class="breadcrumb-item active">Trang chủ</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-xl-3 col-md-3">
    <div class="card-box tilebox-two px-4">
      <i class="icon-layers float-right text-muted" style="font-size: 50px"></i>
      <h5 class="text-primary text-uppercase"><a href="/admin/nganh">Ngành đào tạo</a></h5>
      <h2><span data-plugin="counterup">{{$nganh}}</span></h2>
    </div>
  </div>

  <div class="col-xl-3 col-md-3">
    <div class="card-box tilebox-two px-4">
      <i class="fas fa-users float-right text-muted" style="font-size: 50px"></i>
      <h5 class="text-primary text-uppercase"><a href="/admin/sinhvien">Sinh viên</a></h5>
      <h2><span data-plugin="counterup">{{$sv}}</span></h2>
    </div>
  </div>

  <div class="col-xl-3 col-md-3">
    <div class="card-box tilebox-two px-4">
      <i class="fas fa-users float-right text-muted" style="font-size: 50px"></i>
      <h5 class="text-primary text-uppercase"><a href="/admin/giangvien">Giảng viên</a></h5>
      <h2><span data-plugin="counterup">{{$gv}}</span></h2>      
    </div>
  </div>

  <div class="col-xl-3 col-md-3">
    <div class="card-box tilebox-two px-4">
      <i class="fas fa-bookmark float-right text-muted" style="font-size: 50px"></i>
      <h5 class="text-primary text-uppercase"><a href="/admin/giangvien">Môn học</a></h5>
      <h2><span data-plugin="counterup">{{$mon}}</span></h2>      
    </div>
  </div>
</div> <!-- end row -->

<div class="row">
  <div class="col-xl-6 col-md-12">
    <div class="card-box" style="height:55vh">
      <div class="d-flex align-items-center" style="height:40px !important">
        <div class="mr-auto">
          <h5 class="header-title font-18">Tỉ lệ xếp loại sinh viên <br>theo ngành - khóa </h5>
        </div>

        <div class="mr-2">
          <select class="form-control" style="min-width: 150px" data-toggle="select2" id="slNganhKhoa" name="slNganhKhoa">
            @foreach($ds_nganh as $item)
              <option @if ($item['ten_nganh'] === 'Công nghệ Thông tin' && $item['khoa'] === '2020') selected @endif>{{ $item['ten_nganh'] }} - Khóa {{ $item['khoa'] }}</option>
            @endforeach
          </select>
        </div>
          
        <div>
          <button type="button" class="btn btn-success waves-effect waves-light p-1 px-2" onclick="bd_xl_nganh_khoa()">
            <i class="fas fa-filter font-16"></i>
          </button>
        </div>
      </div>
      <div id="chart_nganh_khoa"></div>
    </div>
  </div>

  <div class="col-xl-6 col-md-12">
    <div class="card-box">
      <div class="d-flex align-items-center" style="height:40px !important">
        <div class="mr-auto">
          <h5 class="header-title font-18">Xếp loại sinh viên theo lớp</h5>
        </div>

        <div class="mr-2">
          <select class="form-control" style="min-width: 150px" data-toggle="select2" id="slLop" name="slLop">
            @foreach($ds_lop as $item)
              <option value="{{ $item->ma_lop }}" {{ $item->ma_lop == 'DA20TTA' ? 'selected' : '' }}>
                {{ $item->ma_lop }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <button type="button" class="btn btn-success waves-effect waves-light p-1 px-2" onclick="bd_xl_lop()">
            <i class="fas fa-filter font-16"></i>
          </button>
        </div>
      </div>

      <div id="chart"></div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/patternomaly@1.3.0/dist/patternomaly.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
var ds_nganh =<?php echo $ds_nganh; ?>;
console.log(ds_nganh);

var bdCot = <?php echo $dataSV; ?>;
bieuDoXLLop(bdCot);

var bd_mien = @json($bd_mien_data);
// console.log(bd_mien);
bdMien(bd_mien);

function bdMien(bd_mien){
  $("#chart_nganh_khoa #bd_xl_nganh_khoa").remove();
  $("#chart_nganh_khoa").append('<canvas id="bd_xl_nganh_khoa" class="mt-3"></canvas>');

  var labels = bd_mien.map(item => item.ma_hoc_ky_nien_khoa);
  const tenHocKy = bd_mien.map(item => item.ten_hoc_ky_nien_khoa);
  var kemData = bd_mien.map(item => parseFloat(item.kem));
  var trungBinhData = bd_mien.map(item => parseFloat(item.trung_binh));
  var khaData = bd_mien.map(item => parseFloat(item.kha));
  var gioiData = bd_mien.map(item => parseFloat(item.gioi));
  var xuatSacData = bd_mien.map(item => parseFloat(item.xuat_sac));

  var ctx = document.getElementById('bd_xl_nganh_khoa').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Kém',
          data: kemData,
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.2
        },
        {
          label: 'Trung Bình',
          data: trungBinhData,
          backgroundColor: 'rgba(255, 205, 89, 0.2)',
          borderColor: 'rgba(255, 205, 89, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.2
        },
        {
          label: 'Khá',
          data: khaData,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.2
        },
        {
          label: 'Giỏi',
          data: gioiData,
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.2
        },
        {
          label: 'Xuất Sắc',
          data: xuatSacData,
          backgroundColor: 'rgba(153, 102, 255, 0.2)',
          borderColor: 'rgba(153, 102, 255, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.2
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        x: {
          beginAtZero: true,
          stacked: true
        },
        y: {
          beginAtZero: true,
          stacked: true, 
          ticks: {
            callback: function(value) {
              return value + '%'; 
            }
          }
        }
      },
      tooltips: {
        callbacks: {
          title: function(tooltipItem, data) {
            return tenHocKy[tooltipItem[0].index] ;
          },
          label: function(tooltipItem, data) {
            var label = data.datasets[tooltipItem.datasetIndex].label || '';
            var value = tooltipItem.yLabel;
            return label + ': ' + value + '%';
          }
        }
      },
    }
  });
}

function bd_xl_nganh_khoa(){
  var nganhKhoa = $('#slNganhKhoa option:selected').text();

  $.ajax({
      url: '/admin/bdxlnganhkhoa',
      method: 'GET',
      data:{   
        nganhKhoa: nganhKhoa,
      },
      success: function(data) {
        // console.log(data);
        bdMien(data);
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
}

function bd_xl_lop(){
  var maLop = $('#slLop').val();

  $.ajax({
      url: '/admin/bieudoxeploai',
      method: 'GET',
      data:{   
        maLop: maLop,
      },
      success: function(data) {
        bieuDoXLLop(data);
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
}

function bieuDoXLLop(data){
  $("#chart #bieuDoXepLoai").remove();
  $("#chart").append('<canvas id="bieuDoXepLoai" class="mt-3"></canvas>');

  var ctx = document.getElementById('bieuDoXepLoai').getContext('2d');

  const labels = data.map(item => item.ma_hoc_ky_nien_khoa);
  const tenHocKy = data.map(item => item.ten_hoc_ky_nien_khoa);

  var datasets = [{
    label: 'Kém',
    data: data.map(function(item) { return item.kem; }),
    backgroundColor: pattern.draw('plus', 'rgba(255, 99, 132, 0.5)'),
    barPercentage: 0.7
  }, {
    label: 'Trung bình',
    data: data.map(function(item) { return item.trung_binh; }),
    backgroundColor: pattern.draw('dash', 'rgba(255, 205, 86, 0.5)'),
    barPercentage: 0.7

  }, {
    label: 'Khá',
    data: data.map(function(item) { return item.kha; }),
    backgroundColor: pattern.draw('cross-dash', 'rgba(75, 192, 192, 0.5)'),
    barPercentage: 0.7

  }, {
    label: 'Giỏi',
    data: data.map(function(item) { return item.gioi; }),
    backgroundColor: pattern.draw('diagonal-right-left', 'rgba(54, 162, 235, 0.5)'),
    barPercentage: 0.7

  }, {
    label: 'Xuất sắc',
    data: data.map(function(item) { return item.xuat_sac; }),
    backgroundColor: pattern.draw('weave', 'rgba(153, 102, 255, 0.5)'),
    barPercentage: 0.7

  }];

  var gradeChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: datasets
    },
    options: {
      scales: {
        yAxes: [{
          stacked: true,
          ticks: {
            beginAtZero: true
          }
        }],
        xAxes: [{
          stacked: true
        }]
      },
      tooltips: {
        callbacks: {
          title: function(tooltipItem, data) {
            return tenHocKy[tooltipItem[0].index];
          },
        }
      },
    }
  });
}

</script>



@include('admin.layout.footer')