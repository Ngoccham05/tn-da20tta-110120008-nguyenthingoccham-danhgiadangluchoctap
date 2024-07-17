@if(Auth::guard('admin')->check())
  @include('admin.layout.header')
@elseif(Auth::guard('gv')->check())
  @include('giangvien.layout.header')
@elseif(Auth::guard('sv')->check())
  @include('sinhvien.layout.header')
@endif

<style>
  .indent{
    text-indent: 20px;
    text-align:justify;
    font-size: 15px;
    margin-bottom: 8px;
  }
  tbody tr td{
    text-align: center;
    padding: 6px !important
  }
</style>
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box mb-4">
      <div class="page-title font-weight-normal font-14">
        <ol class="breadcrumb m-0 p-0">
          <li class="breadcrumb-item"><a href="#">Một số quy định khác</a></li>
        </ol>
      </div>
    </div>
  </div>
</div><!-- end page title --> 

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Học cải thiện điểm số</h4>
      </div>
      <div class="indent">
        1. Học cải thiện điểm do điểm trung bình chung tích lũy của năm học hoặc toàn khóa dưới trung bình:
         nếu điểm trung bình chung tích lũy dưới 2.0, sinh viên phải chọn các học phần có điểm tổng kết là điểm D để đăng ký học cải thiện điểm
          nhằm cải thiện điểm trung bình chung tích lũy đạt từ 2.0 trở lên.
      </div>
      <div class="indent">
        2. Học cải thiện điểm để nâng cao điểm trung bình chung tích lũy: 
        sinh viên có điểm tổng kết học phần đã đạt yêu cầu (từ C trở lên) và muốn có kết quả cao hơn, sinh viên có quyền đăng ký học cải thiện điểm.
      </div>

      <div class="indent">
        3. Việc làm thủ tục đăng ký học cải thiện điểm được thực hiện thường xuyên, sinh viên làm thủ tục đăng ký học cải thiện điểm 
         tại Phòng Đào tạo hoặc các đơn vị QLĐT.
      </div>

      <div class="indent">
        4. Kết quả học cải thiện điểm là kết quả sau cùng của học phần và được tính là kết quả chính thức của sinh viên. 
        Do đó, nếu sinh viên có kết quả trong lần cải thiện điểm ở mức không đạt sau 2 lần thi thì sinh viên phải đăng ký học lại học phần đó.
      </div>

      <div class="indent">
        5. Sinh viên không nên học cải thiện điểm để nâng cao điểm trung bình chung tích lũy đối với những học phần trong học kỳ cuối 
        nhằm tránh trường hợp đến thời điểm xét tốt nghiệp mà sinh viên chưa hoàn thành điểm học cải thiện. 
        Trường hợp sinh vien vẫn mong muốn học cải thiện để nâng cao điểm trung bình chung tích lũy ở học kỳ cuối thì sinh viên làm đơn cam kết, có xác nhận của cố vấn học tập.
      </div>

      <div class="indent">
        6. Sinh viên làm thủ tục đăng ký học cải thiện điểm số tại Phòng Đào tạo hoặc các đơn vị QLĐT.
      </div>
    </div>
  </div>
</div> <!-- end row -->

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Thang điểm đánh giá học phần</h4>
      </div>

      <div class="indent">
        1. Điểm đánh giá quá trình và đánh giá kết thúc được chấm theo thang điểm 10 và làm tròn 1 chữ số thập phân.
      </div>

      <div class="indent">
        2. Điểm trung bình quá trình và điểm tổng kết học phần được làm tròn đến 1 chữ số thập phân.
      </div>

      <div class="indent">
        3. Điểm tổng kết học phần được chuyển thành điểm chữ như sau:
      </div>

      <table id="" class="table table-bordered dt-responsive nowrap table-custom" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
          <tr>
            <th class="text-center">Điểm số theo thang điểm 10</th>
            <th class="text-center">Điểm số theo thang điểm 4</th>
            <th class="text-center">Điểm chữ</th>
          </tr>
        </thead>
        <tbody id="tbody">
          <tr> <td>9.0 - 10.0</td> <td>4.0</td>  <td>A</td> </tr>
          <tr> <td>8.0 - 8.9</td>  <td>3.5</td>  <td>B+</td> </tr>
          <tr> <td>7.0 - 7.9</td>  <td>3.0</td>  <td>B</td> </tr>
          <tr> <td>6.5 - 6.9</td>  <td>2.5</td>  <td>C+</td> </tr>
          <tr> <td>5.5 - 6.4</td>  <td>2.0</td>  <td>C</td> </tr>
          <tr> <td>5.0 - 5.4</td>  <td>1.5</td>  <td>D+</td> </tr>
          <tr> <td>4.0 - 4.9</td>  <td>1.0</td>  <td>D</td> </tr>
          <tr> <td>Nhỏ hơn 4.0</td>  <td>0.0</td>  <td>F</td> </tr>
        </tbody>
      </table> 
    </div>
  </div>
</div> <!-- end row -->

<div class="row">
  <div class="col-12">
    <div class="card-box">
      <div class="d-flex align-items-center mb-3">
        <h4 class="header-title font-18 m-0 mr-auto">Cảnh báo học vụ</h4>
      </div>
      <div class="font-weight-bold mb-2">
        Cuối mỗi học kỳ chính, sinh viên bị cảnh báo học tập dựa trên một số điều kiện như:
      </div>
      <div class="indent">
        1. Tổng số tín chỉ không đạt trong học kỳ vượt quá 50% khối lượng đã đăng kí học trong học kỳ, hoặc tổng số tín chỉ nợ đọng từ đầu khóa học vượt quá 24 tín chỉ.
      </div>

      <div class="indent">
        2. Điểm trung bình học kỳ đạt dưới 0,8 đối với học kỳ đầu của khóa học, dưới 1,0 đối với các học kỳ tiếp theo.
      </div>

      <div class="indent">
        3. Điểm trung bình tích lũy đạt dưới 1,2 đối với sinh viên trình độ năm thứ nhất, dưới 1,4 đối với sinh viên trình độ năm thứ hai, dưới 1,6 đối với sinh viên trình độ năm thứ ba dưới 1,8 đối với sinh viên các năm tiếp theo.
      </div>

      <div class="indent">
        4. sinh viên không đăng ký học trong học kỳ chính mà không được sự cho phép của Hiệu trưởng.
      </div>
    </div>
  </div>
</div> <!-- end row -->

@if(Auth::guard('admin')->check())
  @include('admin.layout.footer')
@elseif(Auth::guard('gv')->check())
  @include('giangvien.layout.footer')
@elseif(Auth::guard('sv')->check())
  @include('sinhvien.layout.footer')
@endif