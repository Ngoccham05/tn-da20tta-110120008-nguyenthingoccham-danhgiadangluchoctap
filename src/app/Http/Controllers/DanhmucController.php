<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\bomon;
use App\Models\khoa;

class DanhmucController extends Controller
{
    //ds Khoa
    public function khoa()
    {
      $khoa = DB::table('khoa')
        ->leftjoin('bo_mon', 'bo_mon.ma_khoa', '=', 'khoa.ma_khoa')
        ->select('khoa.ma_khoa', 'ten_khoa',  DB::raw('count(bo_mon.ma_khoa) as count'))
        ->orderby('ten_khoa')
        ->groupby('khoa.ma_khoa', 'ten_khoa')
        ->get();        

      return view('admin.khoa', compact(['khoa']));
    }

    //thêm khoa
    public function themKhoa(Request $rq)
    {
      $tenKhoa = $rq->tenKhoa;

      $count = DB::table('khoa')->where('ten_khoa', $tenKhoa)->count();

      if($count == 0){
        $id = DB::table('khoa')->insertGetId(['ten_khoa' => $tenKhoa]);
        $num_row = DB::table('khoa')->count();

        return ['id' => encrypt($id), 'num_row' => $num_row];
      } else{
        return "Đã tồn tại";
      }
    }

    //sửa khoa
    public function suaKhoa(Request $rq)
    {
      $ma = decrypt($rq->maKhoa);
      $ten = $rq->tenKhoa;

      $count = DB::table('khoa')->where('ten_khoa', $ten)->where('ma_khoa', '!=', $ma)->count();

      if($count == 0){
        $result = DB::table('khoa')
          ->where('ma_khoa', $ma)
          ->update(['ten_khoa' => $ten]);

        $khoa = DB::table('khoa')->where('ma_khoa', $ma)->first();

        return ['id' => $rq->maKhoa, 'khoa' => $khoa];
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa khoa
    public function xoaKhoa(Request $rq)
    {
      $ma = decrypt($rq->maKhoa);

      $result = DB::table('khoa')
        ->where('ma_khoa', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }

//----------------------------------------------------------------------------------------------------------------------

    //ds bộ môn
    public function boMon()
    {
      $bo_mon = DB::table('bo_mon')
        ->leftjoin('nganh', 'nganh.ma_bo_mon', '=', 'bo_mon.ma_bo_mon')
        ->join('khoa', 'khoa.ma_khoa', '=', 'bo_mon.ma_khoa')
        ->select('bo_mon.ma_bo_mon', 'ten_bo_mon', 'khoa.ma_khoa', 'khoa.ten_khoa', DB::raw('count(nganh.ma_bo_mon) as count'))
        ->orderby('ten_khoa')
        ->orderby('ten_bo_mon')
        ->groupby('bo_mon.ma_bo_mon', 'ten_bo_mon', 'khoa.ma_khoa', 'khoa.ten_khoa')
        ->get();

      $khoa = DB::table('khoa')->orderby('ten_khoa')->get();

      return view('admin.bomon', compact(['bo_mon', 'khoa']));
    }

    //thêm bộ môn
    public function themBoMon(Request $rq)
    {
      $tenBoMon = $rq->tenBoMon;
      $maKhoa = $rq->maKhoa;

      $count = DB::table('bo_mon')->where('ten_bo_mon', $tenBoMon)->count();

      if($count == 0){
        $id = DB::table('bo_mon')
          ->insertGetId([
            'ten_bo_mon' => $tenBoMon,
            'ma_khoa' => $maKhoa,
          ]);

        $num_row = DB::table('bo_mon')->count();
        $boMon = DB::table('bo_mon')
          ->join('khoa', 'khoa.ma_khoa', '=', 'bo_mon.ma_khoa')
          ->where('ma_bo_mon', $id)
          ->first();

        return ['id' => encrypt($id), 'num_row' => $num_row, 'boMon' => $boMon];
      } else{
        return "Đã tồn tại";
      }
    }

    //sửa bộ môn
    public function suaBoMon(Request $rq)
    {
      $maBoMon = decrypt($rq->maBoMon);
      $tenBoMon = $rq->tenBoMon;
      $maKhoa = $rq->maKhoa;

      $count = DB::table('bo_mon')->where('ten_bo_mon', $tenBoMon)->where('ma_bo_mon', '!=', $maBoMon)->count();

      if($count == 0){
        $result = DB::table('bo_mon')
          ->where('ma_bo_mon', $maBoMon)
          ->update([
            'ten_bo_mon' => $tenBoMon,
            'ma_khoa' => $maKhoa,
          ]);

        if($result){
          $boMon = DB::table('bo_mon')
          ->join('khoa', 'khoa.ma_khoa', '=', 'bo_mon.ma_khoa')
          ->where('ma_bo_mon', $maBoMon)
          ->first();

          return ['id' => $rq->maBoMon, 'boMon' => $boMon];
        } else{
          return "Lỗi";
        }
        
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa bộ môn
    public function xoaBoMon(Request $rq)
    {
      $ma = decrypt($rq->maBoMon);

      $result = DB::table('bo_mon')
        ->where('ma_bo_mon', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }

//----------------------------------------------------------------------------------------------------------------------

    //ds ngành
    public function nganh()
    {
      $nganh = DB::table('nganh')
        ->leftjoin('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
        ->join('bo_mon', 'bo_mon.ma_bo_mon', '=', 'nganh.ma_bo_mon')
        ->join('khoa', 'khoa.ma_khoa', '=', 'bo_mon.ma_khoa')
        ->select('nganh.ma_nganh', 'ten_nganh', 'bo_mon.ma_bo_mon', 'ten_bo_mon', 'ten_khoa', DB::raw('count(chuong_trinh_dao_tao.ma_nganh) as count'))
        ->orderby('ten_khoa')
        ->orderby('ten_bo_mon')
        ->orderby('nganh.ma_nganh')
        ->groupby('nganh.ma_nganh', 'ten_nganh', 'bo_mon.ma_bo_mon', 'ten_bo_mon', 'ten_khoa')
        ->get();

      $khoa = khoa::with('bomon')->get();

      return view('admin.nganh', compact(['nganh', 'khoa']));
    }

    //thêm ngành
    public function themNganh(Request $rq)
    {
      $maNganh = $rq->maNganh;
      $tenNganh = $rq->tenNganh;
      $maBoMon = $rq->maBoMon;

      $countMa = DB::table('nganh')->where('ma_nganh', $maNganh)->count();
      $countTen = DB::table('nganh')->where('ten_nganh', $tenNganh)->count();

      if($countMa == 0 && $countTen == 0){
        $result = DB::table('nganh')
          ->insert([
            'ma_nganh' => $maNganh,
            'ten_nganh' => $tenNganh,
            'ma_bo_mon' => $maBoMon,
          ]);

        $num_row = DB::table('nganh')->count();
        $nganh = DB::table('nganh')
          ->join('bo_mon', 'bo_mon.ma_bo_mon', '=', 'nganh.ma_bo_mon')
          ->join('khoa', 'khoa.ma_khoa', '=', 'bo_mon.ma_khoa')
          ->where('ma_nganh', $maNganh)
          ->first();

        return ['num_row' => $num_row, 'nganh' => $nganh];
      } else{
        return "Đã tồn tại";
      }
    }

    function suaNganh(Request $rq){
      $maNganh = $rq->maNganh;
      $tenNganh = $rq->tenNganh;
      $maBoMon = $rq->maBoMon;

      $count = DB::table('nganh')->where('ten_nganh', $tenNganh)->where('ma_nganh', '!=', $maNganh)->count();

      if($count == 0){
        $result = DB::table('nganh')
            ->where('ma_nganh', $maNganh)
            ->update([
              'ten_nganh' => $tenNganh,
              'ma_bo_mon' => $maBoMon,
            ]);

        if($result){
          $nganh = DB::table('nganh')
            ->join('bo_mon', 'bo_mon.ma_bo_mon', '=', 'nganh.ma_bo_mon')
            ->join('khoa', 'khoa.ma_khoa', '=', 'bo_mon.ma_khoa')
            ->where('ma_nganh', $maNganh)
            ->first();

          return $nganh;
        } else{
          return "Lỗi";
        }
        
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa ngành
    public function xoaNganh(Request $rq)
    {
      $ma = $rq->maNganh;

      $result = DB::table('nganh')
        ->where('ma_nganh', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }
//-----------------------------------------------------------------------------------------

    //ds loại học phần
    public function loaihocphan()
    {
      $loai_hoc_phan = DB::table('loai_hoc_phan')
        ->leftjoin('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan', '=', 'loai_hoc_phan.ma_loai_hoc_phan')
        ->select('loai_hoc_phan.*', DB::raw('count(thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan) as count'))
        ->orderby('ten_loai_hoc_phan')
        ->groupby('loai_hoc_phan.ma_loai_hoc_phan', 'ten_loai_hoc_phan')
        ->get();

      return view('admin.loaihocphan', compact('loai_hoc_phan'));
    }

    //thêm loại
    public function themLoaiHocPhan(Request $rq)
    {
      $tenLoai = $rq->tenLoai;

      $count = DB::table('loai_hoc_phan')->where('ten_loai_hoc_phan', $tenLoai)->count();

      if($count == 0){
        $id = DB::table('loai_hoc_phan')->insertGetId(['ten_loai_hoc_phan' => $tenLoai]);
        $num_row = DB::table('loai_hoc_phan')->count();

        return ['id' => encrypt($id), 'num_row' => $num_row];
      } else{
        return "Đã tồn tại";
      }
    }

    //sửa loại học phần
    public function suaLoaiHocPhan(Request $rq)
    {
      $ma = decrypt($rq->maLoai);
      $ten = $rq->tenLoai;

      $count = DB::table('loai_hoc_phan')->where('ten_loai_hoc_phan', $ten)->where('ma_loai_hoc_phan', '!=', $ma)->count();

      if($count == 0){
        $result = DB::table('loai_hoc_phan')
          ->where('ma_loai_hoc_phan', $ma)
          ->update(['ten_loai_hoc_phan' => $ten]);

        $loai_hoc_phan = DB::table('loai_hoc_phan')->where('ma_loai_hoc_phan', $ma)->first();

        return ['id' => $rq->maLoai, 'loai_hoc_phan' => $loai_hoc_phan];
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa loại học phần
    public function xoaLoaiHocPhan(Request $rq)
    {
      $ma = decrypt($rq->maLoai);

      $result = DB::table('loai_hoc_phan')
        ->where('ma_loai_hoc_phan', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }

//-----------------------------------------------------------------------------------------
    //ds khối kiến thức
    public function khoiKienThuc()
    {
      $khoi_kien_thuc = DB::table('khoi_kien_thuc')
        ->leftjoin('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_khoi_kien_thuc', '=', 'khoi_kien_thuc.ma_khoi_kien_thuc')
        ->select('khoi_kien_thuc.ma_khoi_kien_thuc', 'ten_khoi_kien_thuc', DB::raw('count(thuoc_chuong_trinh_dao_tao.ma_khoi_kien_thuc) as count'))
        ->groupby('khoi_kien_thuc.ma_khoi_kien_thuc', 'ten_khoi_kien_thuc')
        ->orderby('ten_khoi_kien_thuc')
        ->get();

      return view('admin.khoikienthuc', compact('khoi_kien_thuc'));
    }

    //thêm khối kiến thức
    public function themkhoiKienThuc(Request $rq)
    {
      $ten = $rq->ten;

      $count = DB::table('Khoi_kien_thuc')->where('ten_khoi_kien_thuc', $ten)->count();

      if($count == 0){
        $id = DB::table('khoi_kien_thuc')
          ->insertGetId(['ten_khoi_kien_thuc' => $ten]);
        $num_row = DB::table('khoi_kien_thuc')->count();

        return ['id' => encrypt($id), 'num_row' => $num_row];
      } else{
        return "Đã tồn tại";
      }
    }

    //sửa khối kiến thức
    public function suakhoiKienThuc(Request $rq)
    {
      $ma = decrypt($rq->ma);
      $ten = $rq->ten;

      $count = DB::table('khoi_kien_thuc')->where('ten_khoi_kien_thuc', $ten)->where('ma_khoi_kien_thuc', '!=', $ma)->count();

      if($count == 0){
        $result = DB::table('khoi_kien_thuc')
          ->where('ma_khoi_kien_thuc', $ma)
          ->update(['ten_khoi_kien_thuc' => $ten]);

        $khoi_kien_thuc = DB::table('khoi_kien_thuc')->where('ma_khoi_kien_thuc', $ma)->first();

        return ['id' => $rq->ma, 'khoi_kien_thuc' => $khoi_kien_thuc];
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa khối kiến thức
    public function xoaKhoiKienThuc(Request $rq)
    {
      $ma = decrypt($rq->ma);
      // return 1;

      $result = DB::table('khoi_kien_thuc')
        ->where('ma_khoi_kien_thuc', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }

//-----------------------------------------------------------------------------------------

    //ds khối kiến thức
    public function nhomMon()
    {
      $nhom_mon = DB::table('nhom_mon')->orderby('ten_nhom_mon')->get();
      return view('admin.nhommon', compact('nhom_mon'));
    }

    //thêm khối kiến thức
    public function themNhomMon(Request $rq)
    {
      $ten = $rq->ten;

      $count = DB::table('nhom_mon')->where('ten_nhom_mon', $ten)->count();

      if($count == 0){
        $id = DB::table('nhom_mon')
          ->insertGetId(['ten_nhom_mon' => $ten]);
        $num_row = DB::table('nhom_mon')->count();

        return ['id' => encrypt($id), 'num_row' => $num_row];
      } else{
        return "Đã tồn tại";
      }
    }

    //sửa khối kiến thức
    public function suaNhomMon(Request $rq)
    {
      $ma = decrypt($rq->ma);
      $ten = $rq->ten;

      $count = DB::table('nhom_mon')->where('ten_nhom_mon', $ten)->where('ma_nhom_mon', '!=', $ma)->count();

      if($count == 0){
        $result = DB::table('nhom_mon')
          ->where('ma_nhom_mon', $ma)
          ->update(['ten_nhom_mon' => $ten]);

        $nhom_mon = DB::table('nhom_mon')->where('ma_nhom_mon', $ma)->first();

        return ['id' => $rq->ma, 'nhom_mon' => $nhom_mon];
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa khối kiến thức
    public function xoaNhomMon(Request $rq)
    {
      $ma = decrypt($rq->ma);
      // return 1;

      $result = DB::table('nhom_mon')
        ->where('ma_nhom_mon', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }

//-----------------------------------------------------------------------------------------

    //ds khối kiến thức
    public function hocKyNienKhoa()
    {
      $hoc_ky_nien_khoa = DB::table('hoc_ky_nien_khoa')
        ->orderByRaw("SUBSTRING(ma_hoc_ky_nien_khoa, -4)")
        ->orderByRaw("SUBSTRING(ma_hoc_ky_nien_khoa, 1, 1)")
        ->orderBy('ten_hoc_ky_nien_khoa')
        ->get();

      return view('admin.hockynienkhoa', compact('hoc_ky_nien_khoa'));
    }

    //thêm khối kiến thức
    public function themHocKyNienKhoa(Request $rq)
    {
      $ma = $rq->ma;
      $ten = $rq->ten;

      $count = DB::table('hoc_ky_nien_khoa')->where('ten_hoc_ky_nien_khoa', $ten)->count();

      if($count == 0){
        $id = DB::table('hoc_ky_nien_khoa')
          ->insertGetId([
            'ma_hoc_ky_nien_khoa' => $ma,
            'ten_hoc_ky_nien_khoa' => $ten
          ]);
        $num_row = DB::table('hoc_ky_nien_khoa')->count();

        return ['id' => encrypt($id), 'num_row' => $num_row];
      } else{
        return "Đã tồn tại";
      }
    }

    //sửa khối kiến thức
    public function suaHocKyNienKhoa(Request $rq)
    {
      $ma = decrypt($rq->ma);
      $ten = $rq->ten;

      $count = DB::table('hoc_ky_nien_khoa')->where('ten_hoc_ky_nien_khoa', $ten)->where('ma_hoc_ky_nien_khoa', '!=', $ma)->count();

      if($count == 0){
        $result = DB::table('hoc_ky_nien_khoa')
          ->where('ma_hoc_ky_nien_khoa', $ma)
          ->update(['ten_hoc_ky_nien_khoa' => $ten]);

        $hoc_ky_nien_khoa = DB::table('hoc_ky_nien_khoa')->where('ma_hoc_ky_nien_khoa', $ma)->first();

        return ['id' => $rq->ma, 'hoc_ky_nien_khoa' => $hoc_ky_nien_khoa];
      } else{
        return "Đã tồn tại";
      }
    }

    //xóa khối kiến thức
    public function xoaHocKyNienKhoa(Request $rq)
    {
      $ma = decrypt($rq->ma);
      // return 1;

      $result = DB::table('hoc_ky_nien_khoa')
        ->where('ma_hoc_ky_nien_khoa', $ma)
        ->delete();

      if($result){
        return 1;
      } else{
        return 0;
      }      
    }
}