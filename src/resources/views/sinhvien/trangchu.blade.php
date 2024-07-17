@include('sinhvien.layout.header')
<!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box mb-4">
          <div class="page-title font-weight-normal font-14">
            <ol class="breadcrumb m-0 p-0">
              <li class="breadcrumb-item">Trang chủ</li>
              <!-- <li class="breadcrumb-item active"></li> -->
            </ol>
          </div>
        </div>
      </div>
    </div><!-- end page title -->

    <div class="container-fluid">
      <div class="row">
        <div class="col col-xl-4 col-md-4">
          <div class="card-box tilebox-two px-4">
            <i class="fas fa-calendar-alt float-right text-muted" style="font-size: 50px"></i>
            <h5 class="text-primary text-uppercase">Học kỳ hiện tại</h5>
            <h2><span data-plugin="counterup">{{$hk_hien_tai}}</span>/{{$so_hoc_ky}}</h2>
            <div class="progress" style="height:10px">
              <div class="progress-bar bg-success" role="progressbar"
                style="width: {{ ($hk_hien_tai / $so_hoc_ky) * 100 }}%" aria-valuenow="{{ $hk_hien_tai }}" aria-valuemin="0"
                aria-valuemax="{{ $so_hoc_ky }}">
              </div>
            </div>
          </div>
        </div>

        <div class="col col-xl-4 col-md-4">
          <div class="card-box tilebox-two px-4">
            <i class="fas fa-check-double float-right text-muted" style="font-size: 50px"></i>
            <h5 class="text-primary text-uppercase">Tín chỉ tích lũy</h5>
            <h2><span data-plugin="counterup">{{$stc}}</span>/{{$tong_tc->tong_so_tin_chi}}</h2>
            <div class="progress" style="height:10px">
              <div class="progress-bar bg-success" role="progressbar"
                style="width: {{ ($stc / $tong_tc->tong_so_tin_chi) * 100 }}%;" aria-valuenow="{{ $stc }}" aria-valuemin="0"
                aria-valuemax="{{ $tong_tc->tong_so_tin_chi }}">
              </div>
            </div>
            <div class="font-italic pt-1" style="font-size: 12px;">
              *Không bao gồm KKT Giáo dục thể chất và Quốc phòng - An ninh
            </div>
          </div>
        </div>

        <div class="col col-xl-4 col-md-4">
          <div class="card-box tilebox-two px-4">
            <i class="fas fa-award float-right text-muted" style="font-size: 50px"></i>
            <h5 class="text-primary text-uppercase">Trung bình tích lũy</h5>
            <h2><span data-plugin="counterup">{{$tich_luy->trung_binh_tich_luy}}</span></h2>
          </div>
        </div>
      </div>
    <div>

    <div class="row">
      <div class="col-xl-6 col-md-12">
        <div class="card-box">
          <div class="d-flex align-items-center">
            <div class="mr-auto py-1">
              <h5 class="header-title font-18">Trung bình nhóm môn</h5>
            </div>
          </div>
          <div id="">
            <canvas id="tb_nhom" height="150"></canvas>
          </div>
        </div>
      </div>

      <div class="col-xl-6 col-md-12">
        <div class="card-box">
          <div class="d-flex align-items-center">
            <div class="mr-auto py-1">
              <h5 class="header-title font-18">Trung bình học kỳ</h5>
            </div>
          </div>
          <div id="">
            <canvas id="bieuDoDiem" height="150"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-12 col-md-12">
        <div class="card-box">
          <div class="d-flex align-items-center" style="height:40px !important">
            <div class="mr-auto">
              <h5 class="header-title mb-2 font-18">Chi tiết nhóm môn</h5>
            </div>

            <div class="mr-2">
              <select class="form-control pr-1" data-toggle="select2" id="slNhom" name="slNhom">
                @foreach($nhom_mon as $nhom)
                  <option value="{{$nhom->ma_nhom_mon}}">
                    {{substr($nhom->ten_nhom_mon, strpos($nhom->ten_nhom_mon, '-') + 1)}}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <button type="button" class="btn btn-success waves-effect waves-light" onclick="bdTrungBinh()">
                <i class="fas fa-filter font-16"></i>
              </button>
            </div>
          </div>

          <div id="chartNhom">
            <canvas id="bdNhom"></canvas>
          </div>
        </div>
        
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>
    <script>
      $(document).ready(function() {
        var maxHeight = 0;
        $('.container-fluid .tilebox-two').each(function() {
          if ($(this).height() > maxHeight) {
            maxHeight = $(this).height();
          }
        });
        $('.container-fluid .tilebox-two').height(maxHeight);
      });

      function bdTrungBinh() {
        var maNhom = $('#slNhom').val();

        $.ajax({
          url: '/sv/bieudotrungbinh',
          method: 'GET',
          data: {
            maNhom: maNhom,
          },
          success: function (data) {
            // console.log(data);
            dbNhom(data.trung_binh_nhom, data.diem_cac_mon);
          },
          error: function (xhr) {
            console.log("Lỗi lấy dữ liệu");
          }
        });
      }

      // biểu đồ trung bình nhóm môn
      var tb_nhom = <?php echo $trung_binh_nhom; ?>;
      var diem_cac_mon = <?php echo $diem_cac_mon; ?>;
      dbNhom(tb_nhom, diem_cac_mon);

      function dbNhom(tb_nhom, diem_cac_mon) {
        $("#chartNhom #bdNhom").remove();
        $("#chartNhom").append('<canvas id="bdNhom" height="90"></canvas>');

        tb_nhom_line = Array(tb_nhom[0].so_mon).fill(tb_nhom[0].trung_binh_nhom);

        var labels = diem_cac_mon.map(item => {
          var ma_mon_hoc = item.ma_mon_hoc;
          if (ma_mon_hoc.includes('.')) {
            ma_mon_hoc = ma_mon_hoc.split('.')[0];
          } else if (item.diem_he_4 == '') {
            ma_mon_hoc = ma_mon_hoc + '(Miễn thi)';
          }
          return ma_mon_hoc;
        });

        var ctx = document.getElementById('bdNhom').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Điểm môn học',
              data: diem_cac_mon.map(item => item.diem_he_4),
              backgroundColor: 'rgba(54, 162, 235, 0.5)',
              width: 0.5,
              order: 2,
              barPercentage: 0.8
            }, {
              label: 'Trung bình nhóm',
              type: 'line',
              data: tb_nhom_line,
              borderColor: '#f26464',
              backgroundColor: '#f26464',
              borderWidth: 2,
              fill: false,
              order: 1,
              pointRadius: 2
            }]
          },
          options: {
            scales: {
              yAxes: [{
                display: true,
                ticks: {
                  beginAtZero: true,
                  max: 4,
                }
              }]
            },
            tooltips: {
              mode: 'index',
              intersect: false,
              callbacks: {
                label: function (tooltipItem, data) {
                    if (tooltipItem.datasetIndex === 0) {
                      var tenMonHoc = diem_cac_mon[tooltipItem.index].ten_mon_hoc;
                      var diemHe4 = diem_cac_mon[tooltipItem.index].diem_he_4;
                      var tb_mon = tenMonHoc + ': ' + diemHe4;

                      if (diemHe4 === '') {
                        tb_mon += ' Miễn thi';
                      }
                      return tb_mon;
                    } else if (tooltipItem.datasetIndex === 1) {
                      var tb_nhom_mon = 'Trung bình nhóm: ' + tb_nhom[0].trung_binh_nhom;
                      return tb_nhom_mon;
                    }
                  return '';
                }
              }
            },
            hover: {
                mode: 'nearest',
                intersect: true,
                onHover: function(event, elements) {
                    var chart = this.chart;
                    if (!elements.length) {
                      chart.tooltip._active = [];
                      chart.tooltip.update(true);
                    } else {
                      var datasetIndex = elements[0]._datasetIndex;
                        chart.tooltip._active = [];
                        elements.forEach(function(element) {
                            if (element._datasetIndex === datasetIndex) {
                                chart.tooltip._active.push(element);
                            }
                        });
                        chart.tooltip.update(true);
                        
                    }
                }
            }
          }
        });
      }

      // biểu đồ so sánh trung bình
      var tb_sv = <?php echo $trung_binh; ?>;
      var tb_lop = <?php echo $trung_binh_lop; ?>;
      var tb_khoa = <?php echo $trung_binh_khoa; ?>;
      bdDiem(tb_sv, tb_lop, tb_khoa);

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

        // $("#chart #bieuDoDiem").remove();
        // $("#chart").append('<canvas id="bieuDoDiem" height="100"></canvas>');

        const ctx = document.getElementById('bieuDoDiem').getContext('2d');
        const myChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'TB sinh viên',
              data: tbHocKy,
              borderColor: '#64b0f2',
              borderWidth: 2,
              fill: false,
              tension: 0.2
            }, {
              label: 'TB cả lớp',
              data: tbHocKy_1,
              borderColor: '#4BC0C0',
              borderWidth: 2,
              fill: false,
              tension: 0.2
            }, {
              label: 'TB cả khóa',
              data: tbHocKy_2,
              borderColor: '#FFCD59',
              borderWidth: 2,
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
                ticks: {
                  beginAtZero: true,
                  max: 4,
                }
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
                    borderWidth: 1,
                }]
            }
          }
        })
      }

      // điểm trung bình tất cả nhóm
      var tb_nhom = <?php echo $diem_tb_nhom; ?>;
      console.log(tb_nhom);
      tbNhom(tb_nhom);

      function tbNhom(tb_nhom){
        var tbNhomLables = tb_nhom.map(function(item) { return item.ma_nhom_mon; });
        var diemTrungBinh = tb_nhom.map(function(item) { return item.trung_binh_nhom; });
        var tenNhomMon = tb_nhom.map(function(item) {
          var firstDashIndex = item.ten_nhom_mon.indexOf('-');
          return firstDashIndex !== -1 ? item.ten_nhom_mon.substring(firstDashIndex + 1).trim() : item.ten_nhom_mon;
        });

        var ctx = document.getElementById('tb_nhom').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: tbNhomLables,
            datasets: [{
              label: 'Trung bình nhóm',
              data: diemTrungBinh,
              backgroundColor: 'rgb(100, 176, 242, 0.2)', 
              borderColor: 'rgb(100, 176, 242, 1)', 
              borderWidth: 2
            }]
          },
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  max: 4,
                }
              }]
            },
            tooltips: {
                callbacks: {
                    title: function(tooltipItem, data) {
                        var index = tooltipItem[0].index;
                        return tenNhomMon[index];
                    }
                }
            }
          }
        });
      }


      // goiY();
      var data = <?php echo $data;?>;
      const requestData = {
        training_data: data,
      };
      function goiY(){
        fetch('http://127.0.0.1:5000/traindata', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
          console.log(data);
        })
        .catch((error) => {
          console.log("Lỗi py", error);
        });
      }

    </script>

    @include('sinhvien.layout.footer')