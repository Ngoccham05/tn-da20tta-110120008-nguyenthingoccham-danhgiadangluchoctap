@include('giangvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Quản lý lớp</a></li>
          <li class="breadcrumb-item active">Sinh viên</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách sinh viên {{$ma}}</h4>
      </div>

      <table id="datatable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">STT</th>
            <!-- <th class="text-center">Lớp</th> -->
            <th class="text-center">MSSV</th>
            <th class="text-center">Họ tên</th>
            <th class="text-center">Giới</th>
            <th class="text-center">Ngày sinh</th>
            <!-- <th class="text-center">Địa chỉ</th>  -->
            <th class="text-center">SĐT</th>
            <!-- <th class="text-center">Email</th> -->
            <th class="text-center">Trạng thái</th>
            <th class="text-center thao-tac-col">Thao tác</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $stt = 1;                                    
          @endphp
          @foreach($sv as $row)
            <tr id="row_{{ $stt }}" class="select-row">
              <td class="text-center" id="stt">{{ $stt++ }}</td>
              <!-- <td class="text-center">{{ $row->ma_lop }}</td> -->
              <td class="text-center">{{ $row->ma_sinh_vien }}</td>
              <td>{{ $row->ho_ten }}</td>
              <td class="text-center">{{ $row->gioi_tinh }}</td>
              <td class="text-center">{{ $row->ngay_sinh ? \Carbon\Carbon::createFromTimestamp($row->ngay_sinh)->format('d/m/Y') : '' }}</td>
              <!-- <td>{{ $row->dia_chi }}</td> -->
              <td class="text-center">{{ $row->so_dien_thoai }}</td>
              <!-- <td class="text-left">{{ $row->email }}</td> -->
              <td class="text-center">{{ $row->trang_thai_hoc }}</td>
              <td class="thao-tac-col">
                <a href="#" class="btn btn-success py-1 px-2 mr-1" style="font-size: 12px" onclick="chiTiet('{{$row->ma_sinh_vien}}')">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->

<!-- modal chi tiết sinh viên -->
<div id="modalChiTiet" class="modal fade modal-center" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myCenterModalLabel">Thông tin sinh viên</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body py-2 px-3">        
        <table id="tableChiTiet" class="w-100">
          <tr><th>Mã số sinh viên:</th><td id="mssv"></td></tr>
          <tr><th>Họ tên:</th><td id="ten"></td></tr>
          <tr><th>Giới tính:</th><td id="gioi"></td></tr>
          <tr><th>Ngày sinh:</th><td id="ngaySinh"></td></tr>
          <tr><th>Địa chỉ:</th><td id="diaChi"></td></tr>
          <tr><th>Số điện thoại:</th><td id="sdt"></td></tr>
          <tr><th>Email:</th><td id="email"></td></tr>
          <tr><th>Lớp:</th><td id="lop"></td></tr>
          <tr><th>Trạng thái:</th><td class="pb-2" id="trangThai"></td></tr>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Đóng</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  function chiTiet(ma){
    $.ajax({
      url: '/gv/ttsv',
      method: 'GET',
      data:{
        ma: ma,
      },
      success: function(data) {
        if(data.ngay_sinh != null){
          var ngaySinh = formatDateFromTimestamp(data.ngay_sinh);
          ngaySinh = ngaySinh[0] + '/' + ngaySinh[1] + '/' + ngaySinh[2]
        } else{
          var ngaySinh = '';
        } 

        $('#mssv').html(data.ma_sinh_vien);
        $('#ten').html(data.ho_ten);
        $('#gioi').html(data.gioi_tinh);
        $('#ngaySinh').html(ngaySinh);
        $('#diaChi').html(data.dia_chi);
        $('#sdt').html(data.so_dien_thoai);
        $('#email').html(data.email);
        $('#lop').html(data.ten_lop);
        $('#trangThai').html(data.trang_thai_hoc);
        
        $('#modalChiTiet').modal('show');
      },
      error: function(xhr) {
        console.log("Lỗi lấy dữ liệu");
      }
    });
  }
</script>

@include('giangvien.layout.footer')