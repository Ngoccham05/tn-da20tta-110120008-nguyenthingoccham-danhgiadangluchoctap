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
          <select class="form-control pr-5" data-toggle="select2" id="slLop" name="slLop">
            @foreach($lop as $lop)
              <option value="{{$lop->ma_lop}}">{{$lop->ma_lop}}</option>
            @endforeach
          </select>
        </div>

        <div class="mr-3">
          <select class="form-control pr-5" style="min-width: 250px" data-toggle="select2" id="slHocKy" name="slHocKy">
            <option>Chọn học kỳ</option>
            @foreach($hoc_ky as $hk)
              <option value="{{$hk->ma_hoc_ky_nien_khoa}}">{{$hk->ten_hoc_ky_nien_khoa}}</option>
            @endforeach
          </select>
        </div>

        <div>
          <button type="button" class="btn btn-success waves-effect waves-light" onclick="xemDiem()">
            <i class="fas fa-filter font-16"></i></button>
        </div>
      </div>

      <div id="diemTable"></div>

      <div id="ghiChu"></div>
    </div>
  </div>
</div> <!-- end row -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> 
  function xemDiem(){
    var maLop = $('#slLop').val();
    var maHK = $('#slHocKy').val();
    
    $.ajax({
      url: '/admin/xemdiemlop',
      method: 'GET',
      data:{   
        maLop: maLop,
        maHK: maHK,
      },
      success: function(data) {
        console.log(data);
        if(data.diem.length == 0){
          console.log('rỗng');
        } else{
          $('#diemTable').empty();

          // Tạo bảng mới
          var table = $('<table>').addClass('table table-bordered table-striped').attr('id', 'customtablediemmon');
          var thead = $('<thead>').appendTo(table);
          var tbody = $('<tbody>').appendTo(table);

          // ds môn
          var monHocList = [];
          for (var maMonHoc in data.diem) {
            data.diem[maMonHoc].forEach(function(subject) {
              if (!monHocList.find(x => x.ma_mon_hoc === subject.ma_mon_hoc)) {
                monHocList.push({
                  ma_mon_hoc: subject.ma_mon_hoc,
                  ten_mon_hoc: subject.ten_mon_hoc
                });
              }
            });
          }

          // ds môn theo mã môn
          monHocList.sort(function(a, b) {
            return a.ma_mon_hoc.localeCompare(b.ma_mon_hoc);
          });

          var headerRow1 = $('<tr>').appendTo(thead);
          $('<th class="text-center align-middle" rowspan="2">').text('STT').appendTo(headerRow1);
          $('<th class="text-center align-middle" rowspan="2">').text('MSSV').appendTo(headerRow1);
          $('<th class="text-center align-middle" rowspan="2">').text('Họ tên').appendTo(headerRow1);
          $('<th class="text-center align-middle" colspan="' + monHocList.length + '">').text('Môn học').appendTo(headerRow1);
          $('<th class="text-center align-middle" rowspan="2">').text("TBHK").appendTo(headerRow1);
          $('<th class="text-center align-middle" rowspan="2">').text("TBTL").appendTo(headerRow1);

          // Thêm tiêu đề cột cho từng môn học
          var headerRow = $('<tr>').appendTo(thead);
          monHocList.forEach(function(monHoc, index) {
            $('<th class="text-center">').text(index + 1).appendTo(headerRow);
          });

          // Thêm dữ liệu vào bảng
          var stt = 0;
          for (var maSinhVien in data.diem) {
            stt = stt + 1;
            var sinhVien = data.diem[maSinhVien];
            var row = $('<tr>').appendTo(tbody);
            $('<td class="text-center">').text(stt).appendTo(row);
            $('<td class="text-center">').text(sinhVien[0].ma_sinh_vien).appendTo(row);
            $('<td>').text(sinhVien[0].ho_ten).appendTo(row);

            // Tạo một đối tượng map dùng để tìm điểm theo mã môn học nhanh chóng
            var diemMap = {};
            sinhVien.forEach(function(subject) {
              if(subject.diem_he_4 != ''){
                var diem = subject.diem_he_4;

                if(diem.length == 1){
                  var diem_he_4 = subject.diem_he_4 + '.0';
                } else{
                  var diem_he_4 = subject.diem_he_4
                }

                diemMap[subject.ma_mon_hoc] = diem_he_4;
              } else{ 
                diemMap[subject.ma_mon_hoc] = subject.diem_chu;
              }
            });

            // Điền điểm vào từng ô tương ứng với mỗi môn học
            monHocList.forEach(function(monHoc) {
              $('<td class="text-center">').text(diemMap[monHoc.ma_mon_hoc] || '-').appendTo(row);
            });

            data.tich_luy.forEach(function(tich_luy) {              
              if(sinhVien[0].ma_sinh_vien == tich_luy.ma_sinh_vien){
                $('<td class="text-center font-weight-bold">').text(tich_luy.trung_binh_hoc_ky).appendTo(row);
                let length = tich_luy.trung_binh_tich_luy.toString().length;
                $('<td class="text-center font-weight-bold">').text(tich_luy.trung_binh_tich_luy).appendTo(row);
              }
            });

          }

          // Thêm bảng vào trang
          $('#diemTable').append(table);

          $('#ghiChu').html('');
          var noteDiv = $('#ghiChu');
          monHocList.forEach(function(monHoc, index) {
            $('<div>').text((index + 1) + ': ' + monHoc.ma_mon_hoc + ' - ' + monHoc.ten_mon_hoc).appendTo(noteDiv);
          });

          $("#customtablediemmon tr").slice(2).each(function() {
            var tbhk = $(this).find('td').eq(-2).text();
            var tbtl = $(this).find('td:last').text();

            var hk = maHK.charAt(0);
            var nam_hoc = maHK.substring(1, 3);
            var khoa = maLop.substring(2, 4);

            if(hk !=3 ){
              if(hk == 1){
                if(nam_hoc == khoa){
                  if(tbhk < 0.8){
                    $(this).addClass('text-danger font-weight-bold');
                  }
                } else{
                  if(tbhk < 1.0){
                    $(this).addClass('text-danger font-weight-bold');
                  }
                }  
              } else if(hk == 2){
                if(tbhk < 1.0){
                  $(this).addClass('text-danger font-weight-bold');
                }
              }

              if(nam_hoc == khoa){
                if(tbtl < 1.2){
                  $(this).addClass('text-danger font-weight-bold');
                }
              } else if(nam_hoc - khoa == 1){
                if(tbtl < 1.4){
                  $(this).addClass('text-danger font-weight-bold');
                }
              } else if(nam_hoc - khoa == 2){
                if(tbtl < 1.6){
                  $(this).addClass('text-danger font-weight-bold');
                }
              } else{
                if(tbtl < 1.8){
                  $(this).addClass('text-danger font-weight-bold');
                }
              }
            }
          });
        }
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }

  $(document).ready(function() {
    $('#slLop').on('change', function(){
      var maLop = $('#slLop').val();
      $.ajax({
        url: '/admin/slhocky',
        method: 'GET',
        data:{   
          maLop: maLop,
        },
        success: function(data) {
          $('#slHocKy').empty();
          data.forEach(function(hoc_ky) {
            $('#slHocKy').append('<option value="' + hoc_ky.ma_hoc_ky_nien_khoa + '">' + hoc_ky.ten_hoc_ky_nien_khoa + '</option>');
          });
          $('#slHocKy').trigger('change');
        },
        error: function(xhr) {
          console.log("Lỗi lấy dữ liệu");
        }
      });
    });
  });

</script>

@include('admin.layout.footer')