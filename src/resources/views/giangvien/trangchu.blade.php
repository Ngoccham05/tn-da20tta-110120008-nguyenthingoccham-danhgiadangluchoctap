@include('giangvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Giảng viên</a></li>
          <li class="breadcrumb-item active">Trang chủ</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-8">
    <div class="card-box">
      <h5 class="header-title mb-3">Trung bình học kỳ @if($lop_hl)- {{$lop_hl->ma_lop}} @endif</h5>
      <canvas id="bieuDoDiem" height="150"></canvas>
    </div>
  </div>
  <div class="col-4">
    <div class="card-box">
      <h5 class="header-title mb-3">Cố vấn</h5>
      @if($lop != null)
      <table id="customtable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">Mã lớp</th>
            <th class="text-center">Hiệu lực</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @foreach($lop as $row)
            <tr>
              <td class="text-center"><a href="/gv/dslop/{{encrypt($row->ma_lop)}}" class="text-dark font-weight-bold">{{ $row->ma_lop }}</a></td>
              <td class="text-center">
                @if ($row->trang_thai == "Hiệu lực")
                  <span class="badge badge-success p-2 rounded-pill font-12">Hiệu lực</span>
                @else
                  <span class="badge badge-warning p-2 rounded-pill font-12">Hết hiệu lực</span>
                @endif
              </td>

            </tr>
          @endforeach
        </tbody>
      </table> 
      @endif
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/patternomaly@1.3.0/dist/patternomaly.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> 
var data = <?php echo $data; ?>;
if(data == 0){
  $('.header-title').after('(Không có dữ liệu)');
  $("#bieuDoDiem").addClass('d-none');
} else{
  drawGradeChart(data);
}

function drawGradeChart(data){
  $("#chart #bieuDoDiem").remove();
  $("#chart").append('<canvas id="bieuDoDiem" class="mt-3"></canvas>');

  var ctx = document.getElementById('bieuDoDiem').getContext('2d');

  const labels = data.map(item => item.ma_hoc_ky_nien_khoa);
  const tenHocKy = data.map(item => item.ten_hoc_ky_nien_khoa);

  var datasets = [{
    label: 'Kém',
    data: data.map(function(item) { return item.kem; }),
    backgroundColor: pattern.draw('plus', 'rgba(255, 99, 132, 0.5)'),
    borderWidth: 1
  }, {
    label: 'Trung bình',
    data: data.map(function(item) { return item.trung_binh; }),
    backgroundColor: pattern.draw('dash', 'rgba(255, 205, 86, 0.5)'),
    borderWidth: 1
  }, {
    label: 'Khá',
    data: data.map(function(item) { return item.kha; }),
    backgroundColor: pattern.draw('cross-dash', 'rgba(75, 192, 192, 0.5)'),
    borderWidth: 1
  }, {
    label: 'Giỏi',
    data: data.map(function(item) { return item.gioi; }),
    backgroundColor: pattern.draw('diagonal-right-left', 'rgba(54, 162, 235, 0.5)'),
    borderWidth: 1
  }, {
    label: 'Xuất sắc',
    data: data.map(function(item) { return item.xuat_sac; }),
    backgroundColor: pattern.draw('weave', 'rgba(153, 102, 255, 0.5)'),
    borderWidth: 1
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

@include('giangvien.layout.footer')