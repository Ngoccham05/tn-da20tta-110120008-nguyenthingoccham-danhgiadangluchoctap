@include('sinhvien.layout.header')

<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Chương trình đào tạo</a></li>
          <li class="breadcrumb-item active">{{ $ctdt->ten_chuong_trinh }} </li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Danh sách môn học</h4>

        <input type="text" class="form-control col-2 py-1 font-14" id="inputSearch" name="inputSearch" placeholder="Tìm kiếm..." autocomplete="off">
      </div>

      <table id="customtable" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center" style="width: 100px !important">STT</th>
            <th class="text-center" style="width: 150px !important">Mã môn</th>
            <th class="text-center">Tên môn</th>
            <th class="text-center" style="width: 120px !important">Số tín chỉ</th>
            <th class="text-center" style="width: 150px !important">Loại học phần</th>
            <th class="text-center" style="width: 120px !important">Đã tích lũy</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @php
            $hoc_ky = null;
            $stt = 1;
          @endphp

          @foreach($mon_hoc as $row)
            @if ($hoc_ky === null || $row->thu_tu_hoc_ky != $hoc_ky)
              @php
                $stt = 1;
              @endphp
              <tr>
                <td colspan="6" class="font-weight-bold bg-primary text-light">Học kỳ {{ $row->thu_tu_hoc_ky }}</td>
              </tr>
            @endif

            <tr id="row_{{ $stt }}">
              <td class="text-center" id="stt">{{ $stt++ }}</td>
              <td class="text-center">{{ $row->ma_mon_hoc }}</td>
              <td>{{ $row->ten_mon_hoc }}</td>
              <td class="text-center">{{ $row->so_tin_chi }}</td>
              <td class="text-center">
                @if($row->ten_loai_hoc_phan == "Bắt buộc")
                <span class="badge badge-pill badge-success px-2 py-1 font-11">Bắt buộc</span>
                @endif
              </td>
              <td class="text-center">
                @php
                  $tich_luy = false;
                @endphp
                @foreach($diem as $d)
                  @if($d->ma_mon_hoc == $row->ma_mon_hoc)
                    <i class="fas fa-check text-success"></i>
                    @php
                      $tich_luy = true;
                    @endphp
                  @endif
                @endforeach
                @if(!$tich_luy && $row->ten_loai_hoc_phan == "Bắt buộc" && $row->thu_tu_hoc_ky <= $hk_hien_tai)
                  <span><i class="fas fa-exclamation-triangle text-danger font-18"></i></span>
                @endif
              </td>
            </tr>

            @php
              $hoc_ky = $row->thu_tu_hoc_ky;
            @endphp
          @endforeach

        </tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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