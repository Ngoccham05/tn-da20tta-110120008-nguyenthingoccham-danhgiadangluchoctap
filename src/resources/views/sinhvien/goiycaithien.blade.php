@include('sinhvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Sinh viên</a></li>
          <li class="breadcrumb-item active">Gợi ý môn học</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title -->

<div class="d-flex flex-wrap" id="goi_y"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  var nguoi_dung = @json($nguoi_dung);
  var mon_cai_thien = @json($mon_cai_thien);
  var diem = @json($diem);
  var nhom_mon = @json($nhom_mon);
  var vi_tri = @json($vi_tri);
  
  // console.log(mon_cai_thien);
  goiYCaiThien({ diem: diem, nhom_mon: nhom_mon, vi_tri: vi_tri });

  function goiYCaiThien(data){
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
        let ma_sinh_vien = nguoi_dung['ten_dang_nhap'];
        let filtered = mon_cai_thien.filter(item => item.ma_sinh_vien === ma_sinh_vien);
        if (filtered.length > vi_tri_index) {
          return filtered[vi_tri_index-1].ma_mon_hoc;
        }
        return null;
      });

      // console.log(ma_mon_cai_thien);
      tt_mon(ma_mon_cai_thien);

    })
    .catch((error) => {
      console.log("Lỗi py", error);
    });
  }

  function tt_mon(ma_mon_cai_thien){
    $.ajax({
      url: "/sv/ttmoncaithien",
      type: "POST",
      data: {
        ma_mon_cai_thien: ma_mon_cai_thien
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data){
        var goi_y = data['mon_goi_y'];
        var k_goi_y = data['mon_k_goi_y'];  

        if(goi_y || k_goi_y){    
          $.each(goi_y, function(key, item) {
            var diem_he_4 = item.diem_he_4.length === 1 ? `${item.diem_he_4}.0` : item.diem_he_4;
            var diem_lan_2 = item.diem_lan_2 == '' ? `<span class=""></span>` : `<span class="">Điểm lần 2: <b>${item.diem_lan_2}</b></span>`;

            $('#goi_y').append(`  
            <div class="col-lg-4 col-md-6">
              <div class="card-box tilebox-three d-flex mb-3">
                <div class="mr-auto">
                  <div class="badge badge-pill badge-primary font-12 py-1 px-2 m-0">Đề xuất cải thiện</div>
                  <h6 class="my-2 font-14">${item.ma_mon_hoc} (${item.so_tin_chi} tín chỉ)</h6>
                  <h6 class="my-2 font-16">${item.ten_mon_hoc}</h6>
                  <div class="d-flex">
                    <div class="mr-5">
                      <span class="">Điểm lần 1: <b>${item.diem_lan_1}</b></span><br>
                      <span class="">Điểm hệ 4: <b>${diem_he_4}</b></span>
                    </div>

                    <div class="ml-5">
                      ${diem_lan_2}<br>
                      <span class="">Điểm chữ: <b>${item.diem_chu}</b></span>
                    </div>
                  </div>                
                </div>
              </div>
            </div>`);
          });

          console.log(k_goi_y);

          $.each(k_goi_y, function(key, item) {
            var diem_he_4 = item.diem_he_4.length === 1 ? `${item.diem_he_4}.0` : item.diem_he_4;
            var diem_lan_2 = item.diem_lan_2 == '' ? `<span class=""></span>` : `<span class="">Điểm lần 2: <b>${item.diem_lan_2}</b></span>`;

            $('#goi_y').append(`
            <div class="col-lg-4 col-md-6">
              <div class="card-box tilebox-three d-flex mb-3">
                <div class="mr-auto">
                  <div class="badge badge-pill badge-success font-12 py-1 px-2 m-0">Xem xét cải thiện</div>

                  <h6 class="my-2 font-14">${item.ma_mon_hoc} (${item.so_tin_chi} tín chỉ)</h6>
                  <h6 class="my-2 font-17">${item.ten_mon_hoc}</h6>
                  <div class="d-flex">
                    <div class="mr-5">
                      <span class="">Điểm lần 1: <b>${item.diem_lan_1}</b></span><br>
                      <span class="">Điểm hệ 4: <b>${diem_he_4}</b></span>
                    </div>

                    <div class="ml-5">
                      ${diem_lan_2}<br>
                      <span class="">Điểm chữ: <b>${item.diem_chu}</b></span>
                    </div>
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

</script>

@include('sinhvien.layout.footer')