@include('giangvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item">Phân tích</li>
          <!-- <li class="breadcrumb-item active"></li> -->
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title -->

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex mb-3">
        <div class="d-flex">
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
      </div>

      <div id="row_tt" class="mb-3 d-none">
        <div class="row">
          <div class="col-4">
            <div class="row"><div class="col-3 font-weight-bold">MSSV:</div><div class="col-9" id="tt_mssv"></div></div>
            <div class="row mt-2"><div class="col-3 font-weight-bold">Họ tên:</div><div class="col-9" id="tt_ten"></div></div>
            <div class="row my-2"><div class="col-3 font-weight-bold">Lớp:</div><div class="col-9" id="tt_lop"></div></div>
          </div>
        </div>
      </div>

      <div id="chart"></div>
    </div>
  </div>
</div>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  function xemDiem(){
    var maSV = $('#slSV').val();
    
    $.ajax({
      url: '/gv/phantichmanhyeu',
      method: 'GET',
      data:{   
        maSV: maSV,
      },
      success: function(data) {
        // console.log(data);
        bdDiem(data.diem);

        $('#row_tt').removeClass("d-none");
        $('#tt_mssv').html(data.sinh_vien.ma_sinh_vien);
        $('#tt_ten').html(data.sinh_vien.ho_ten);
        $('#tt_lop').html(data.sinh_vien.ten_lop);

      }
    });
  }

  function bdDiem(data){
    $("#chart #myChart").remove();
    $("#chart").append('<canvas id="myChart" height="80"></canvas>');
    Chart.register({
      id: 'customColor',
      beforeRender: function (chart) {
        const datasets = chart.data.datasets;
        for (let i = 0; i < datasets.length; i++) {
          const dataset = datasets[i];
          const meta = chart.getDatasetMeta(i);
          const data = dataset.data || [];
          for (let j = 0; j < data.length; j++) {
            const point = meta.data[j];
            const value = data[j].y;
            
            switch(value) {
            case 0:
              var color = "rgb(255,0,0, 0.8)";
              break;
            case 1.0:
              var color = "rgb(255,69,0, 0.8)";
              break;
            case 1.5:
              var color = "rgb(255,255,0, 0.8)";
              break;
            case 2.0:
              var color = "rgb(173,255,47, 0.8)";
              break;
            case 2.5:
              var color = "rgb(0,255,0, 0.8)";
              break;
            case 3.0:
              var color = "rgb(0,191,255, 0.8)";
              break;
            case 3.5:
              var color = "rgb(30,144,255)";
              break;
            default:
              var color = "rgb(0,0,255, 0.8)";
            }

            point.custom = {
              backgroundColor: color,
            };
          }
        }
      },
      afterDraw: function (chart) {
        const datasets = chart.data.datasets;
        for (let i = 0; i < datasets.length; i++) {
          const dataset = datasets[i];
          const meta = chart.getDatasetMeta(i);
          const data = dataset.data || [];
          for (let j = 0; j < data.length; j++) {
            const point = meta.data[j];
            const ctx = chart.ctx;
            ctx.save();
            ctx.fillStyle = point.custom.backgroundColor || 'rgba(54, 162, 235, 1)';
            ctx.beginPath();
            ctx.arc(point.x, point.y, 8, 0, Math.PI * 2);
            ctx.closePath();
            ctx.fill();
            ctx.restore();
          }
        }
      }
    });

    const nhom_hoc_ky = {};
    data.forEach(item => {
      const hoc_ky = item.ma_hoc_ky_nien_khoa;
      const ma_mon_hoc = item.ma_mon_hoc;

      if (!nhom_hoc_ky[hoc_ky]) {
        nhom_hoc_ky[hoc_ky] = [];
      }
      nhom_hoc_ky[hoc_ky].push({
        x: hoc_ky + '-' + ma_mon_hoc,
        y: parseFloat(item.diem_he_4),
        ma_mon_hoc: ma_mon_hoc,
        ten_mon_hoc: item.ten_mon_hoc
      });
    });

    const hoc_ky_sx = Object.keys(nhom_hoc_ky).sort((a, b) => {
      const nam_hoc_a = a.slice(-4);
      const nam_hoc_b = b.slice(-4);
      if (nam_hoc_a < nam_hoc_b) return -1;
      if (nam_hoc_a > nam_hoc_b) return 1;

      const hoc_ky_a = a.charAt(0);
      const hoc_ky_b = b.charAt(0);
      if (hoc_ky_a < hoc_ky_b) return -1;
      if (hoc_ky_a > hoc_ky_b) return 1;

      return 0;
    });

    const uniqueXValues = {};
    let currentIndex = 1;

    hoc_ky_sx.forEach(hoc_ky => {
      nhom_hoc_ky[hoc_ky].forEach(item => {
        const key = item.x;
        if (!uniqueXValues[key]) {
          uniqueXValues[key] = currentIndex++;
        }
      });
    });

    const chartData = {
      datasets: hoc_ky_sx.map(hoc_ky => {
        const dataPoints = nhom_hoc_ky[hoc_ky].map(diem_he_4 => ({
          x: uniqueXValues[diem_he_4.x],
          y: diem_he_4.y,
          ma_mon_hoc: diem_he_4.ma_mon_hoc,
          ten_mon_hoc: diem_he_4.ten_mon_hoc
        }));

        return {
          label: `Học kỳ ${hoc_ky}`,
          data: dataPoints,
          backgroundColor: '#fff',
          borderWidth: 1,
          pointRadius: 8,
          pointHoverRadius: 12,
          pointStyle: 'circle',
          showLine: false,
          plugin: {
            customColor: true
          }
        };
      }),
    };

    const ctx = document.getElementById('myChart').getContext('2d');

    const myChart = new Chart(ctx, {
      type: 'scatter',
      data: chartData,
      options: {
        scales: {
          x: {
            type: 'linear',
            position: 'bottom',
            ticks: {
              stepSize: 1,
              callback: (value) => {
                const xValue = Object.keys(uniqueXValues).find(key => uniqueXValues[key] === value);
                if (xValue) {
                  const [hoc_ky, ma_mon_hoc] = xValue.split('-');
                  return `Học kỳ ${hoc_ky}, Môn ${ma_mon_hoc}`;
                }
                return value;
              }
            },
            display: false,
          },
          y: {
            beginAtZero: true,
            suggestedMax: 4,
            ticks: {
              stepSize: 0.5
            }
          }
        },
        plugins: {
          interaction: {
            intersect: false,
            mode: 'index',
          },
          tooltip: {
            callbacks: {
              label: (tooltipItem) => {
                const dataPoint = tooltipItem.raw;
                const maMonHoc = dataPoint.ma_mon_hoc;
                const tenMonHoc = dataPoint.ten_mon_hoc;
                const diemHe4 = dataPoint.y;
                return `${tenMonHoc} (${maMonHoc}): ${diemHe4}`;
              },
            },
            yAlign: "top"
          },
          legend: {
            display: false
          }
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