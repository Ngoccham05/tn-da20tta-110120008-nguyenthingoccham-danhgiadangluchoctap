<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class QuanlylopController extends Controller
{
  //ds lớp
  public function lop()
  {
    $lop = DB::table('lop')
      ->leftjoin('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
      ->leftjoin('giang_vien', 'giang_vien.ma_giang_vien', '=', 'quan_ly_lop.ma_giang_vien')
      ->leftjoin('sinh_vien', 'sinh_vien.ma_lop', '=', 'lop.ma_lop')
      ->select('lop.*', 'giang_vien.ma_giang_vien', 'giang_vien.ho_ten', 'quan_ly_lop.trang_thai', DB::raw('count(sinh_vien.ma_lop) as count_sv'))
      ->groupby('lop.ma_lop', 'ten_lop', 'ma_chuong_trinh', 'giang_vien.ma_giang_vien', 'giang_vien.ho_ten', 'quan_ly_lop.trang_thai')
      ->orderby('ma_lop')
      // ->where('quan_ly_lop.trang_thai', '=', 'Hiệu lực')
      ->get();

    $chuong_trinh = DB::table('chuong_trinh_dao_tao')->get();

    $giang_vien = DB::table('giang_vien')
      ->leftJoin('quan_ly_lop', 'quan_ly_lop.ma_giang_vien', '=', 'giang_vien.ma_giang_vien')
        ->select('giang_vien.ma_giang_vien', 'ho_ten', DB::raw('COUNT(CASE WHEN quan_ly_lop.trang_thai = "Hiệu lực" THEN 1 ELSE NULL END) as count'))
      ->groupby('giang_vien.ma_giang_vien', 'ho_ten')
      ->get();

    return view('admin.lop', compact(['lop', 'chuong_trinh', 'giang_vien']));
  }

  //ds sinh viên lớp
  public function dsLop($malop)
  {
    $maLop = decrypt($malop);

    $sv = DB::table('sinh_vien')
      ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
      ->leftjoin('bang_diem_mon_hoc', 'bang_diem_mon_hoc.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
      ->leftjoin('bang_diem_hoc_ky', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
      ->select('sinh_vien.*', 'ten_lop', DB::raw('count(bang_diem_mon_hoc.ma_sinh_vien) as count_mon'), DB::raw('count(bang_diem_hoc_ky.ma_sinh_vien) as count_hk'))
      ->groupby('sinh_vien.ma_sinh_vien', 'ho_ten', 'gioi_tinh', 'ngay_sinh', 'ma_lop', 'dia_chi', 'so_dien_thoai', 'email', 'trang_thai_hoc', 'ten_lop')
      ->orderby('ma_sinh_vien')
      ->where('lop.ma_lop', $maLop)
      ->get();

    $lop = DB::table('lop')->get();
    $gv = Db::table('giang_vien')
      ->join('quan_ly_lop', 'quan_ly_lop.ma_giang_vien', '=', 'giang_vien.ma_giang_vien')
      ->where('ma_lop', $maLop)
      ->where('quan_ly_lop.trang_thai', '=', 'Hiệu lực')
      ->value('ho_ten');

    return view('admin.sinhvien', compact(['sv', 'lop', 'maLop', 'gv']));
  }

  //thêm lớp
  public function themLop(Request $rq)
  {
    $ma = $rq->ma;
    $ten = $rq->ten;
    $chuongTrinh = $rq->chuongTrinh;
    $gv = $rq->gv;

    $count = DB::table('lop')
      ->where('ma_lop', $ma)
      ->orwhere('ten_lop', $ten)
      ->count();

    if($count == 0){
      $id = DB::table('lop')
        ->insertGetId([
          'ma_lop' => $ma, 
          'ten_lop' => $ten,
          'ma_chuong_trinh' => $chuongTrinh
        ]);

      $rs = DB::table('quan_ly_lop')
        ->insert([
          'ma_lop' => $ma, 
          'ma_giang_vien' => $gv,
          'trang_thai' => 'Hiệu lực',
        ]);
      $num_row = DB::table('lop')->count();

      return ['id' => encrypt($ma), 'num_row' => $num_row];
    } else{
      return "Đã tồn tại";
    }
  }

  //sửa lớp
  public function suaLop(Request $rq)
  {
    $ma = $rq->ma;
    $ten = $rq->ten;
    $chuongTrinh = $rq->chuongTrinh;
    $gv = $rq->gv;

    $count = DB::table('lop')->where('ten_lop', $ten)->where('ma_lop', '!=', $ma)->count();

    if($count == 0){
      $result = DB::table('lop')
        ->where('ma_lop', $ma)
        ->update([
          'ten_lop' => $ten,
          'ma_chuong_trinh' => $chuongTrinh,
        ]);

      $ql_cu= DB::table('quan_ly_lop')
        ->where('ma_lop', $ma)
        ->update([
          'trang_thai' => 'Hết hiệu lực',
        ]);

      $ql_moi = DB::table('quan_ly_lop')
        ->insert([
          'ma_lop' => $ma, 
          'ma_giang_vien' => $gv,
          'trang_thai' => 'Hiệu lực',
        ]);

      $lop = DB::table('lop')->where('ma_lop', $ma)->first();

      return ['lop' => $lop, 'ma_lop' => encrypt($ma)];
    } else{
    return "Đã tồn tại";
    }
  }

  //xóa lớp
  public function xoaLop(Request $rq)
  {
    $ma = $rq->ma;
    $rs = DB::table('quan_ly_lop')->where('ma_lop', $ma)->delete();

    $result = DB::table('lop')
      ->where('ma_lop', $ma)
      ->delete();

    if($result){
      return 1;
    } else{
      return 0;
    }    
  }
//---------------------------------------------------------------------------------------------------

  //ds sinh viên
  public function sinhVien()
  {
    $sv = DB::table('sinh_vien')
      ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
      ->leftjoin('bang_diem_mon_hoc', 'bang_diem_mon_hoc.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
      ->leftjoin('bang_diem_hoc_ky', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
      ->select('sinh_vien.*', 'ten_lop', DB::raw('count(bang_diem_mon_hoc.ma_sinh_vien) as count_mon'), DB::raw('count(bang_diem_hoc_ky.ma_sinh_vien) as count_hk'))
      ->groupby('sinh_vien.ma_sinh_vien', 'ho_ten', 'gioi_tinh', 'ngay_sinh', 'ma_lop', 'dia_chi', 'so_dien_thoai', 'email', 'trang_thai_hoc', 'ten_lop')
      ->orderby('ma_sinh_vien')
      ->get();

    $lop = DB::table('lop')->get();
    $maLop = '';
    $gv = '';

    return view('admin.sinhvien', compact(['sv', 'lop', 'maLop', 'gv']));
  }

  // Chi tiết sinh viên
  public function thongTinSinhVien(Request $rq)
  {
    $ma = $rq->ma;

    $sv = DB::table('sinh_vien')
      ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
      ->join('tai_khoan', 'tai_khoan.ten_dang_nhap', '=', 'sinh_vien.ma_sinh_vien')
      ->where('ma_sinh_vien', $ma)
      ->first();

    return $sv;
  }

  //reset mật khẩu
  public function doiMatKhau(Request $rq)
  {
    $ma = $rq->ma;

    DB::table('tai_khoan')
      ->where('ten_dang_nhap', $ma)
      ->update([
        'mat_khau' => Hash::make($ma),
      ]);

    return $ma;
  }

  //cập nhật trạng thái tài khoản
  public function capNhatTrangThaiTK(Request $rq)
  {
    $ma = $rq->ma;
    $trang_thai = DB::table('tai_khoan')->where('ten_dang_nhap', $ma)->value('trang_thai');

    $trang_thai == 0 ? $trang_thai_moi = 1 : $trang_thai_moi = 0;

    DB::table('tai_khoan')
      ->where('ten_dang_nhap', $ma)
      ->update([
        'trang_thai' => $trang_thai_moi,
      ]);
      
    return $trang_thai_moi;
  }

  //Cập nhật trạng thái học
  public function capNhatTrangThai(Request $rq)
  {
    $ma = $rq->ma;
    $tt_hien_tai = $rq->trang_thai;

    if($tt_hien_tai == "Đang học"){
      $tt_moi = "Đã thôi học";
      $trang_thai_moi = 1;
    } else{
      $tt_moi = "Đang học";
      $trang_thai_moi = 0;
    }

    DB::table('sinh_vien')
      ->where('ma_sinh_vien', $ma)
      ->update([
        'trang_thai_hoc' => $tt_moi,
      ]);

    DB::table('tai_khoan')
      ->where('ten_dang_nhap', $ma)
      ->update([
        'trang_thai' => $trang_thai_moi,
      ]);
      
    return $tt_moi;
  }

  //thêm sinh viên
  public function themSinhVien(Request $rq)
  {
    $mssv = $rq->input('mssv');
    $lop = $rq->input('lop');
    $ten = $rq->input('ten');
    $gioi = $rq->input('gioi');
    $ngaySinh = $rq->input('ngaySinh');
    $diaChi = $rq->input('diaChi');
    $sdt = $rq->input('sdt');
    $email = $rq->input('email');

    if (!DB::table('sinh_vien')->where('ma_sinh_vien', $mssv)->exists()) {
      DB::beginTransaction();

      if($ngaySinh != ''){
        $ngaySinh = Carbon::parse($ngaySinh)->timestamp;
      } else{
        $ngaySinh = NULL;
      }

      try{
        $result = DB::table('sinh_vien')->insert([            
          'ma_sinh_vien'  => $mssv,
          'ho_ten'        => $ten,
          'gioi_tinh'     => $gioi,
          'ngay_sinh'     => $ngaySinh,
          'dia_chi'       => $diaChi,
          'so_dien_thoai' => $sdt,            
          'email'         => $email,
          'ma_lop'        => $lop,
        ]);

        DB::table('tai_khoan')->insert([            
          'ten_dang_nhap'  => $mssv,
          'mat_khau'       => Hash::make($mssv),
          'quyen_truy_cap' => 2,
          'trang_thai'     => 0,
        ]);

        DB::table('tai_khoan_sinh_vien')->insert([            
          'ten_dang_nhap'  => $mssv,
          'ma_sinh_vien'  => $mssv,
        ]);       

        DB::commit();

        $num_row = DB::table('sinh_vien')->count();
        $sv = DB::table('sinh_vien')->where('ma_sinh_vien', $mssv)->first();
        return ['num_row'=>$num_row, 'sv'=>$sv];
      } catch (\Exception $e){
        DB::rollBack();
        return "Lỗi khi thêm dữ liệu";
      }
    } else{
      return "Đã tồn tại";
    }

  }

  //thêm sinh viên bằng file excel
  public function importSinhVien(Request $rq)
  {
    try {
      $file = $rq->file('file');
      $data = Excel::toCollection(null, $file);

      DB::beginTransaction();
      try{
        if (!empty($data) && $data->count()) {
          $firstRow = true;
          foreach ($data[0] as $row) {
            if ($firstRow) {
              $firstRow = false; 
              continue;
            }
                      
            if (!DB::table('sinh_vien')->where('ma_sinh_vien', $row[2])->exists()
                && DB::table('lop')->where('ma_lop', strtoupper($row[1]))->exists()) {

              if($row[5] != ''){
                list($day, $month, $year) = explode('/', $row[5]);
                if (checkdate($month, $day, $year)) {
                  $timestamp = mktime(0, 0, 0, $month, $day, $year);
                }
              } else{
                $timestamp = NULL;
              }             

              DB::table('sinh_vien')->insert([            
                'ma_sinh_vien'  => $row[2],
                'ho_ten'        => $row[3],
                'gioi_tinh'     => $row[4],
                'ngay_sinh'     => $timestamp,
                'dia_chi'       => $row[6],
                'so_dien_thoai' => $row[7],            
                'email'         => $row[8],
                'ma_lop'        => strtoupper($row[1]),
              ]);

              DB::table('tai_khoan')->insert([            
                'ten_dang_nhap'  => $row[2],
                'mat_khau'       => Hash::make($row[2]),
                'quyen_truy_cap' => 2,
                'trang_thai'     => 0,
              ]);

              DB::table('tai_khoan_sinh_vien')->insert([            
                'ten_dang_nhap'  => $row[2],
                'ma_sinh_vien'  => $row[2],
              ]);
              
            }
          }
        }

        DB::commit();
        return "Thành công";
      } catch(\Exception $e){
        DB::rollBack();
        return "Lỗi thêm dữ liệu";
      }

    } catch (\Exception $e) {
      return "Lỗi thêm dữ liệu";
    }
  }

  //sửa sinh viên
  public function suaSinhVien(Request $rq)
  {
    $mssv = $rq->input('mssv');
    $lop = $rq->input('lop');
    $ten = $rq->input('ten');
    $gioi = $rq->input('gioi');
    $ngaySinh = $rq->input('ngaySinh');
    $diaChi = $rq->input('diaChi');
    $sdt = $rq->input('sdt');
    $email = $rq->input('email');

    try{
      if($ngaySinh != ''){
        $ngaySinh = Carbon::parse($ngaySinh)->timestamp;
      } else{
        $ngaySinh = NULL;
      }

      $result = DB::table('sinh_vien')
        ->where('ma_sinh_vien', $mssv)
        ->update([
          'ho_ten'        => $ten,
          'gioi_tinh'     => $gioi,
          'ngay_sinh'     => $ngaySinh,
          'dia_chi'       => $diaChi,
          'so_dien_thoai' => $sdt,            
          'email'         => $email,
          'ma_lop'        => $lop,
        ]);
      
      $sv = DB::table('sinh_vien')->where('ma_sinh_vien', $mssv)->first();
      return $sv;

    } catch(\Exception $e) {
      return "Lỗi khi cập nhật dữ liệu";
    }
  }

  public function xoaSinhVien(Request $rq)
  {
    $ma = $rq->ma;

    DB::beginTransaction();
    try{
      $result = DB::table('tai_khoan_sinh_vien')
        ->where('ma_sinh_vien', $ma)
        ->delete();
      $result_sv = DB::table('sinh_vien')
        ->where('ma_sinh_vien', $ma)
        ->delete();
      $result_tk = DB::table('tai_khoan')
        ->where('ten_dang_nhap', $ma)
        ->delete();

      DB::commit();
      return 1;
    } catch(\Exception $e){
      DB::rollBack();
      return 0;
    } 
  }

  public function xoaNhieuSinhVien(Request $rq)
  {
    $sv = $rq->dataXoa;

    DB::beginTransaction();
    try{
      foreach ($sv as $row){
        $result = DB::table('tai_khoan_sinh_vien')
          ->where('ma_sinh_vien', $row)
          ->delete();
        $result_sv = DB::table('sinh_vien')
          ->where('ma_sinh_vien', $row)
          ->delete();
        $result_tk = DB::table('tai_khoan')
          ->where('ten_dang_nhap', $row)
          ->delete();
      }

      DB::commit();
      return 1;
    } catch(\Exception $e){
      DB::rollBack();
      return 0;
    }  
  }

//------------------------------------------------------------------------------------------
  //ds giảng viên
  public function giangVien()
  {
    $gv = DB::table('giang_vien')
      ->leftjoin('quan_ly_lop', 'quan_ly_lop.ma_giang_vien', '=', 'giang_vien.ma_giang_vien')
      ->select('giang_vien.*', DB::raw('count(quan_ly_lop.ma_giang_vien) as count'))
      ->groupby('giang_vien.ma_giang_vien', 'ho_ten', 'gioi_tinh', 'ngay_sinh', 'dia_chi', 'so_dien_thoai', 'email')
      ->orderby('ma_giang_vien')
      ->get();

    $lop = DB::table('lop')->get();

    return view('admin.giangvien', compact(['gv', 'lop']));
  }

  public function themGiangVien(Request $rq)
  {
    $mgv = $rq->input('mgv');
    $ten = $rq->input('ten');
    $gioi = $rq->input('gioi');
    $ngaySinh = $rq->input('ngaySinh');
    $diaChi = $rq->input('diaChi');
    $sdt = $rq->input('sdt');
    $email = $rq->input('email');

    if (!DB::table('giang_vien')->where('ma_giang_vien', $mgv)->exists()) {
      DB::beginTransaction();
      try{
        if($ngaySinh != ''){
          $ngaySinh = Carbon::parse($ngaySinh)->timestamp;
        } else{
          $ngaySinh = NULL;
        }

        $result = DB::table('giang_vien')->insert([            
          'ma_giang_vien'  => $mgv,
          'ho_ten'        => $ten,
          'gioi_tinh'     => $gioi,
          'ngay_sinh'     => $ngaySinh,
          'dia_chi'       => $diaChi,
          'so_dien_thoai' => $sdt,            
          'email'         => $email,
        ]);

        DB::table('tai_khoan')->insert([            
          'ten_dang_nhap'  => $mgv,
          'mat_khau'       => Hash::make($mgv),
          'quyen_truy_cap' => 1,
          'trang_thai'     => 0,
        ]);

        DB::table('tai_khoan_giang_vien')->insert([            
          'ten_dang_nhap'  => $mgv,
          'ma_giang_vien'  => $mgv,
        ]);       

        DB::commit();

        $num_row = DB::table('giang_vien')->count();
        $gv = DB::table('giang_vien')->where('ma_giang_vien', $mgv)->first();
        return ['num_row'=>$num_row, 'gv'=>$gv];
      } catch (\Exception $e){
        DB::rollBack();
        return "Lỗi khi thêm dữ liệu";
      }
    } else{
      return "Đã tồn tại";
    }

  }

  public function suaGiangVien(Request $rq)
  {
    $mgv = $rq->input('mgv');
    $ten = $rq->input('ten');
    $gioi = $rq->input('gioi');
    $ngaySinh = $rq->input('ngaySinh');
    $diaChi = $rq->input('diaChi');
    $sdt = $rq->input('sdt');
    $email = $rq->input('email');

    try{
      if($ngaySinh != ''){
        $ngaySinh = Carbon::parse($ngaySinh)->timestamp;
      } else{
        $ngaySinh = NULL;
      }

      $result = DB::table('giang_vien')
        ->where('ma_giang_vien', $mgv)
        ->update([
          'ho_ten'        => $ten,
          'gioi_tinh'     => $gioi,
          'ngay_sinh'     => $ngaySinh,
          'dia_chi'       => $diaChi,
          'so_dien_thoai' => $sdt,            
          'email'         => $email,
        ]);
      
      $gv = DB::table('giang_vien')->where('ma_giang_vien', $mgv)->first();
      $count = DB::table('quan_ly_lop')->where('ma_giang_vien', $mgv)->count();
      return ['gv'=>$gv, 'count'=>$count];

    } catch(\Exception $e) {
      return "Lỗi khi cập nhật dữ liệu";
    }
  }

  public function thongTinGiangVien(Request $rq)
  {
    $ma = $rq->ma;

    $sv = DB::table('giang_vien')
      ->join('tai_khoan', 'tai_khoan.ten_dang_nhap', '=', 'giang_vien.ma_giang_vien')
      ->where('ma_giang_vien', $ma)
      ->first();

    return $sv;
  }

  public function xoaGiangVien(Request $rq)
  {
    $ma = $rq->ma;

    $result = DB::table('tai_khoan_giang_vien')
      ->where('ma_giang_vien', $ma)
      ->delete();
    $result_gv = DB::table('giang_vien')
      ->where('ma_giang_vien', $ma)
      ->delete();
    $result_tk = DB::table('tai_khoan')
      ->where('ten_dang_nhap', $ma)
      ->delete();

    if($result){
      return 1;
    } else{
      return 0;
    }   
  }

}