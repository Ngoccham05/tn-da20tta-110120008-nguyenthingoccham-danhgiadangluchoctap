@include('sinhvien.layout.header')
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Xem điểm</a></li>
          <li class="breadcrumb-item active">Điểm toàn khóa</li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Bảng điểm toàn khóa</h4>

        <input type="text" class="form-control col-2 py-1 font-14" id="inputSearch" name="inputSearch" placeholder="Tìm kiếm..." autocomplete="off">
      </div>

      <table id="customtable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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
        <tbody id="tbody">
          @php $hoc_ky = null; @endphp

          @foreach($diem_hoc_ky as $hk)
            @if ($hoc_ky == null || $hk->ma_hoc_ky_nien_khoa != $hoc_ky)
              @php
                $stt = 1;
              @endphp
              <tr>
                <td colspan="8" class="font-weight-bold bg-primary text-light">{{ $hk->ten_hoc_ky_nien_khoa }}</td>
              </tr>
            @endif

            @foreach($diem as $row)
              @if($row->ma_hoc_ky_nien_khoa == $hk->ma_hoc_ky_nien_khoa)
                @php  
                  $text_danger = ($row->diem_chu == 'F') ? 'text-danger font-weight-bold' : '';
                @endphp
                <tr class=""></tr>
                  <td class="text-center {{$text_danger}}">{{ $stt++ }}</td>
                  <td class="text-center {{$text_danger}}">{{ $row->ma_mon_hoc }}</td>
                  <td class="{{$text_danger}}">{{ $row->ten_mon_hoc }}</td>
                  <td class="text-center {{$text_danger}}">{{ $row->so_tin_chi }}</td>
                  <td class="text-center {{$text_danger}}">{{ $row->diem_lan_1 }}</td>
                  <td class="text-center {{$text_danger}}">{{ $row->diem_lan_2 }}</td>
                  <td class="text-center {{$text_danger}}">{{ strlen($row->diem_he_4) == 1 ? $row->diem_he_4 . '.0' : $row->diem_he_4 }}</td>

                  <td class="text-center {{$text_danger}}">{{ $row->diem_chu }}</td>
                </tr>
              @endif
            @endforeach

            <tr>
              <td colspan="2" class="font-weight-bold border-right-0" style="padding: 8px 10px !important">Trung bình học kỳ: {{$hk->trung_binh_hoc_ky}}</td>
              <td colspan="6" class="font-weight-bold border-left-0" style="padding: 8px 10px !important">Trung bình tích lũy: {{$hk->trung_binh_tich_luy}}</td>
            </tr>
          @endforeach
        </tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
  $(document).ready(function() {
    $("#inputSearch").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#customtable tbody tr").filter(function() {
        var found = false;
        $(this).find("td").each(function() {
          if ($(this).text().toLowerCase().indexOf(value) > -1) {
            found = true;
            return false;
          }
        });
        $(this).toggle(found);
      });
    });

  });

</script>

@include('sinhvien.layout.footer')