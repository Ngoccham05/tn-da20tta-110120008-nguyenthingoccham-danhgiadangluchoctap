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

      <div id="row_tt" class="d-none">
        <div class="row">
          <div class="col-4">
            <div class="row"><div class="col-3 font-weight-bold">MSSV:</div><div class="col-9" id="tt_mssv"></div></div>
            <div class="row mt-2"><div class="col-3 font-weight-bold">Họ tên:</div><div class="col-9" id="tt_ten"></div></div>
            <div class="row my-2"><div class="col-3 font-weight-bold">Lớp:</div><div class="col-9" id="tt_lop"></div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap" id="goi_y"></div>
  </div>
</div>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  function xemDiem(){
    var maSV = $('#slSV').val();
    
    $.ajax({
      url: '/gv/mongoiy',
      method: 'GET',
      data:{   
        maSV: maSV,
      },
      success: function(data) {
        console.log(data);
        var ma_sinh_vien = data.sinh_vien.ma_sinh_vien;
        goiYCaiThien({ diem: data.diem, nhom_mon: data.nhom_mon, vi_tri: data.vi_tri }, ma_sinh_vien, data.mon_cai_thien);

        $('#row_tt').removeClass("d-none");
        $('#tt_mssv').html(data.sinh_vien.ma_sinh_vien);
        $('#tt_ten').html(data.sinh_vien.ho_ten);
        $('#tt_lop').html(data.sinh_vien.ten_lop);

      }
    });
  }

  function goiYCaiThien(data, ma_sinh_vien, mon_cai_thien){
    fetch('http://127.0.0.1:5000/goiycaithien', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
      let ma_mon_cai_thien = data.map(index => {
        let vi_tri_index = index;
        let filtered = mon_cai_thien.filter(item => item.ma_sinh_vien === ma_sinh_vien);
        if (filtered.length > vi_tri_index) {
          return filtered[vi_tri_index-1].ma_mon_hoc;
        }
        return null;
      });

      console.log(ma_mon_cai_thien);
      tt_mon(ma_mon_cai_thien, ma_sinh_vien);

    })
    .catch((error) => {
      console.log("Lỗi py", error);
    });
  }

  function tt_mon(ma_mon_cai_thien, msv){
    $.ajax({
      url: "/gv/ttmoncaithien",
      type: "POST",
      data: {
        ma_mon_cai_thien: ma_mon_cai_thien,
        msv: msv,
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data){
        console.log(data);
        var goi_y = data['mon_goi_y'];
        var k_goi_y = data['mon_k_goi_y'];  

        if(goi_y || k_goi_y){  
          $('#goi_y').html('');  
          $.each(goi_y, function(key, item) {
            var diem_he_4 = item.diem_he_4.length === 1 ? `${item.diem_he_4}.0` : item.diem_he_4;

            $('#goi_y').append(`  
            <div class="col-lg-4 col-md-6">
              <div class="card-box tilebox-three d-flex mb-3">
                <div class="mr-auto">
                  <div class="badge badge-pill badge-primary font-12 py-1 px-2 m-0">Đề xuất cải thiện</div>
                  <h6 class="my-2 font-16">${item.ma_mon_hoc} - ${item.ten_mon_hoc}</h6>
                  <div class="d-flex">
                    <span class="mr-5">Điểm hệ 4: ${diem_he_4}</span>
                    <span>Số tín chỉ: ${item.so_tin_chi}</span>
                  </div>                  
                </div>
              </div>
            </div>`);
          });

          $.each(k_goi_y, function(key, item) {
            var diem_he_4 = item.diem_he_4.length === 1 ? `${item.diem_he_4}.0` : item.diem_he_4;

            $('#goi_y').append(`
            <div class="col-lg-4 col-md-6">
              <div class="card-box tilebox-three d-flex mb-3">
                <div class="mr-auto">
                  <div class="badge badge-pill badge-success font-12 py-1 px-2 m-0">Xem xét cải thiện</div>

                  <h6 class="my-2 font-16">${item.ma_mon_hoc} - ${item.ten_mon_hoc}</h6>
                  <div class="d-flex">
                    <span class="mr-5">Điểm hệ 4: ${diem_he_4}</span>
                    <span>Số tín chỉ: ${item.so_tin_chi}</span>
                  </div> 
                </div>
              </div>
            </div>`);
          });
        }
      },
      error: function(xhr, status, error){
        customThongBao();
        toastr.error("", "Lỗi dữ liệu");             
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