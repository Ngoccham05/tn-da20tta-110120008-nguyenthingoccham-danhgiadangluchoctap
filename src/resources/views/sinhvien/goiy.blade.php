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

<div class="d-flex flex-wrap" id="tu_chon"></div>

<div class="d-flex flex-wrap" id="bat_buoc">
  @foreach($mon_bat_buoc as $mon)
    <div class="col-lg-4 col-md-6">
      <div class="card-box tilebox-three d-flex mb-3">
        <div class="">
          <div class="badge badge-pill badge-info font-12 py-1 px-2 m-0">Học phần bắt buộc</div>
          <h6 class="">{{$mon['ma_mon_hoc']}} - {{$mon['ten_mon_hoc']}}</h6>
          <div class="d-flex text-wrap">
            <div>Nhóm: &nbsp;</div>            
            <div>
              @foreach($mon['nhom_mon'] as $nhom)
              {{ $nhom }}<br>
              @endforeach
            </div>
            
          </div>
          
        </div>
      </div>
    </div>
  @endforeach
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  var data = <?php echo $data;?>;
  var mon_hoc = <?php echo $mon_hoc;?>;
  var msv = <?php echo $nguoi_dung['ten_dang_nhap'];?>;

  let newStudentDataList = mon_hoc.map(item => {
    return {
      ma_sinh_vien: msv, 
      ma_mon_hoc: item.ma_mon_hoc,
      ten_mon_hoc: item.ten_mon_hoc,
      ma_nhom_mon: item.ma_nhom_mon,
      ten_nhom_mon: item.ten_nhom_mon,
      similar_student_registration: 0
    };
  });

  const requestData = {
    training_data: data,
    new_student_data: newStudentDataList
  };  

  goiY();

  function goiY(){
    fetch('http://127.0.0.1:5000/goiy', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
      console.log(data);

      data.forEach(item => {
        if(item.lable == "Dang_ky"){
          var icon = `<i class="fas fa-check-circle avatar-title text-success" style="font-size: 36px !important"></i>`;
        } else{
          var icon = `<i class="fas fa-exclamation-circle avatar-title text-warning" style="font-size: 36px !important"></i>`;
        }

        $('#tu_chon').append(`
        <div class="col-lg-4 col-md-6">
          <div class="card-box tilebox-three d-flex mb-3">
            <div class="mr-auto">
              <div class="badge badge-pill badge-info font-12 py-1 px-2 m-0">Học phần tự chọn</div>
              <h6 class="">${item.ma_mon_hoc} - ${item.ten_mon_hoc}</h6>
              <div>Nhóm: ${item.ten_nhom_mon.substring(item.ten_nhom_mon.indexOf('-') + 1).trim()}</div>
            </div>
            <div class="">${icon}</div>
          </div>
        </div>`);
      });

    })
    .catch((error) => {
      console.log("Lỗi py", error);
    });
  }

</script>

@include('sinhvien.layout.footer')