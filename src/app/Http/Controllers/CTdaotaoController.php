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
use App\Models\bomon;
use App\Models\khoa;

class CTdaotaoController extends Controller
{
  //nhóm môn chương trình
  public function nhomMonCT($mact)
  {
    $mact = decrypt($mact);
    $nhom_mon_col = 0;

    $mon_hoc = DB::table('mon_hoc')
      ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
      ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
      ->join('loai_hoc_phan', 'loai_hoc_phan.ma_loai_hoc_phan', '=', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan')
      ->where('thuoc_chuong_trinh_dao_tao.ma_chuong_trinh', $mact)
      ->orderby('thu_tu_hoc_ky')
      ->orderby('mon_hoc.ma_mon_hoc')
      ->get();

    if (DB::table('nhom_mon')->where('ten_nhom_mon', 'like', $mact . '%')->exists()) {
      $ma_mon_hoc_list = $mon_hoc->pluck('ma_mon_hoc');

      $nhom_mon = DB::table('thuoc_nhom_mon')
        ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'thuoc_nhom_mon.ma_mon_hoc')
        ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
        ->whereIn('thuoc_nhom_mon.ma_mon_hoc', $ma_mon_hoc_list)
        ->where('nhom_mon.ten_nhom_mon', 'like', $mact . '%')
        ->get();

      $mon_hoc->each(function ($mon) use ($nhom_mon) {
        $mon->nhom_mon = $nhom_mon->where('ma_mon_hoc', $mon->ma_mon_hoc);
      });

      $nhom_mon_col = DB::table('nhom_mon')
        ->where('nhom_mon.ten_nhom_mon', 'like', $mact . '%')
        ->get();
    }
    return view('admin.nhommonct', compact('mon_hoc', 'mact', 'nhom_mon_col'));
  }

  public function themNhomMon(Request $rq)
  {
    $data = $rq->data;
    $mact = $rq->mact;

    DB::beginTransaction();
    try {
      $nhom_xoa = DB::table('nhom_mon')
        ->where('ten_nhom_mon', 'like', $mact . '%')
        ->get();

      if($nhom_xoa){
        foreach ($nhom_xoa as $nhom) {
          DB::table('thuoc_nhom_mon')
            ->where('ma_nhom_mon', $nhom->ma_nhom_mon)
            ->delete();          
          
          DB::table('nhom_mon')
            ->where('ma_nhom_mon', $nhom->ma_nhom_mon)
            ->delete();
        }
      }
      
      foreach ($data as $nhom_them) {
        $tenNhomMon = $mact . '-' . $nhom_them[0];

        $id = DB::table('nhom_mon')
          ->insertGetId([
            'ten_nhom_mon' => $tenNhomMon,
          ]);

        for ($i = 1; $i < count($nhom_them); $i++) {
          DB::table('thuoc_nhom_mon')->insertGetId([
            'ma_nhom_mon' => $id,
            'ma_mon_hoc' => $nhom_them[$i],
          ]);
        }
      }

      DB::commit();
      return $mact;
    } catch (Exception $e) {
      DB::rollBack();

      throw new Exception($e->getMessage());
    }
  }

  //ds chương trình đào tạo
  public function CTDaoTao()
  {
    $ctdt = DB::table('chuong_trinh_dao_tao')
      ->leftjoin('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
      ->leftjoin('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
      ->join('nganh', 'nganh.ma_nganh', '=', 'chuong_trinh_dao_tao.ma_nganh')
      ->select('chuong_trinh_dao_tao.*', 'nganh.*', DB::raw('count(lop.ma_chuong_trinh) as countlop'), DB::raw('count(thuoc_chuong_trinh_dao_tao.ma_chuong_trinh) as countct'))
      ->groupby('chuong_trinh_dao_tao.tong_so_tin_chi', 'chuong_trinh_dao_tao.ma_chuong_trinh', 'ten_chuong_trinh', 'so_quyet_dinh', 'chuong_trinh_dao_tao.ma_nganh', 'nganh.ma_bo_mon', 'nganh.ma_nganh', 'ten_nganh')
      ->orderby('ten_chuong_trinh')
      ->get();

    $nganh = DB::table('nganh')->get();
    return view('admin.chuongtrinhdaotao', compact(['ctdt', 'nganh']));
  }

  //chi tiết chương trình đào tạo
  public function chiTietChuongTrinh($mact)
  {
    $mact = decrypt($mact);

    $mon_hoc_trong_ct = DB::table('mon_hoc')
      ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
      ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
      ->join('loai_hoc_phan', 'loai_hoc_phan.ma_loai_hoc_phan', '=', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan')
      ->join('khoi_kien_thuc', 'khoi_kien_thuc.ma_khoi_kien_thuc', '=', 'thuoc_chuong_trinh_dao_tao.ma_khoi_kien_thuc')
      ->where('chuong_trinh_dao_tao.ma_chuong_trinh', $mact)
      ->orderby('thu_tu_hoc_ky')->orderby('mon_hoc.ma_mon_hoc')
      ->get();

    $mon_hoc = DB::table('mon_hoc')->get();
    $loai_hoc_phan = DB::table('loai_hoc_phan')->get();
    $khoi_kien_thuc = DB::table('khoi_kien_thuc')->get();
    $chuong_trinh = DB::table('chuong_trinh_dao_tao')->where('ma_chuong_trinh', $mact)->first();

    return view('admin.chitietct', compact(['mon_hoc_trong_ct', 'chuong_trinh', 'mon_hoc', 'loai_hoc_phan', 'khoi_kien_thuc', 'mact']));
  }

  //thêm chương trình
  public function themCTDaoTao(Request $rq)
  {
    $ma = $rq->ma;
    $ten = $rq->ten;
    $soQD = $rq->soQD;
    $maNganh = $rq->maNganh;

    $count = DB::table('chuong_trinh_dao_tao')->where('so_quyet_dinh', $soQD)->count();

    if ($count == 0) {
      $id = DB::table('chuong_trinh_dao_tao')
        ->insertGetId([
          'ma_chuong_trinh' => $ma,
          'ten_chuong_trinh' => $ten,
          'so_quyet_dinh' => $soQD,
          'ma_nganh' => $maNganh,
        ]);
      $num_row = DB::table('chuong_trinh_dao_tao')->count();

      return ['id' => encrypt($ma), 'num_row' => $num_row];
    } else {
      return "Đã tồn tại";
    }
  }

  //thêm môn học vào chương trình đào tạo
  public function themMonHocCTDT(Request $rq)
  {
    $ma = $rq->input('ma');
    $ten = $rq->input('ten');
    $stc = $rq->input('stc');
    $ctdt = $rq->input('ctdt');
    $lhp = $rq->input('lhp');
    $kkt = $rq->input('kkt');
    $hk = $rq->input('hk');

    DB::beginTransaction();
    try {
      if (!DB::table('mon_hoc')->where('ma_mon_hoc', $ma)->exists()) {
        DB::table('mon_hoc')->insert([
          'ma_mon_hoc' => $ma,
          'ten_mon_hoc' => $ten,
          'so_tin_chi' => $stc,
        ]);

        DB::table('thuoc_chuong_trinh_dao_tao')->insert([
          'ma_chuong_trinh' => $ctdt,
          'ma_mon_hoc' => $ma,
          'ma_loai_hoc_phan' => $lhp,
          'ma_khoi_kien_thuc' => $kkt,
          'thu_tu_hoc_ky' => $hk,
        ]);
      } else {
        if (!DB::table('thuoc_chuong_trinh_dao_tao')->where('ma_chuong_trinh', $ctdt)->where('ma_mon_hoc', $ma)->exists()) {

          DB::table('thuoc_chuong_trinh_dao_tao')->insert([
            'ma_chuong_trinh' => $ctdt,
            'ma_mon_hoc' => $ma,
            'ma_loai_hoc_phan' => $lhp,
            'ma_khoi_kien_thuc' => $kkt,
            'thu_tu_hoc_ky' => $hk,
          ]);
        }
      }
      DB::commit();
      return "Thành công";
    } catch (\Exception $e) {
      DB::rollBack();
      // return "Thất bại";
      // return response()->json([
      //   'status' => 'error',
      //   'message' => 'Thất bại: ' . $e->getMessage()
      // ]);
    }
  }

  //sửa chương trình
  public function suaCTDaoTao(Request $rq)
  {
    $ma = $rq->ma;
    $ten = $rq->ten;
    $soQD = $rq->soQD;
    $maNganh = $rq->maNganh;

    $count = DB::table('chuong_trinh_dao_tao')->where('so_quyet_dinh', $soQD)->where('ma_chuong_trinh', '!=', $ma)->count();

    if ($count == 0) {
      $result = DB::table('chuong_trinh_dao_tao')
        ->where('ma_chuong_trinh', $ma)
        ->update([
          'ten_chuong_trinh' => $ten,
          'so_quyet_dinh' => $soQD,
          'ma_nganh' => $maNganh,
        ]);

      $ctdt = DB::table('chuong_trinh_dao_tao')
        ->join('nganh', 'nganh.ma_nganh', '=', 'chuong_trinh_dao_tao.ma_nganh')
        ->where('ma_chuong_trinh', $ma)
        ->first();

      return ['ctdt' => $ctdt, 'id' => encrypt($ma)];
    } else {
      return "Đã tồn tại";
    }
  }

  //xóa chương trình
  public function xoaCTDaoTao(Request $rq)
  {
    $ma = $rq->ma;

    $result = DB::table('chuong_trinh_dao_tao')
      ->where('ma_chuong_trinh', $ma)
      ->delete();

    if ($result) {
      return 1;
    } else {
      return 0;
    }
  }

  //xóa môn học trong ctdt
  public function xoaMonCTDT(Request $rq)
  {
    $result = DB::table('thuoc_chuong_trinh_dao_tao')
      ->where('ma_chuong_trinh', $rq->maCT)
      ->where('ma_mon_hoc', $rq->maMon)
      ->delete();

    if ($result) {
      return 1;
    } else {
      return 0;
    }
  }
  //---------------------------------------------------------------------------------------

  public function monHoc()
  {
    $mon_hoc = DB::table('mon_hoc')
      ->leftjoin('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
      ->leftjoin('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
      ->leftjoin('bang_diem_mon_hoc', 'bang_diem_mon_hoc.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
      ->select(
        'mon_hoc.*',
        DB::raw('count(thuoc_chuong_trinh_dao_tao.ma_mon_hoc) as count_ct'),
        DB::raw('count(thuoc_nhom_mon.ma_mon_hoc) as count_nhom'),
        DB::raw('count(bang_diem_mon_hoc.ma_mon_hoc) as count_bdiem')
      )
      ->groupby('mon_hoc.ma_mon_hoc', 'ten_mon_hoc', 'so_tin_chi')
      ->orderby('mon_hoc.ma_mon_hoc')
      ->get();

    $chuong_trinh = DB::table('chuong_trinh_dao_tao')->get();
    $loai_hoc_phan = DB::table('loai_hoc_phan')->get();
    $khoi_kien_thuc = DB::table('khoi_kien_thuc')->get();

    return view('admin.monhoc', compact(['mon_hoc', 'chuong_trinh', 'loai_hoc_phan', 'khoi_kien_thuc']));
  }

  public function thongTinMonHoc(Request $rq)
  {
    $ma = $rq->ma_mon;

    $mon_hoc = DB::table('mon_hoc')
      ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
      ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
      ->join('loai_hoc_phan', 'loai_hoc_phan.ma_loai_hoc_phan', '=', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan')
      ->join('khoi_kien_thuc', 'khoi_kien_thuc.ma_khoi_kien_thuc', '=', 'thuoc_chuong_trinh_dao_tao.ma_khoi_kien_thuc')
      ->where('mon_hoc.ma_mon_hoc', $ma)
      ->get();

    return response()->json($mon_hoc);
    ;
  }

  //thêm môn
  public function themMonHoc(Request $rq)
  {
    $ma = $rq->ma;
    $ten = $rq->ten;
    $stc = $rq->stc;

    $count = DB::table('mon_hoc')->where('ma_mon_hoc', $ma)->count();

    if ($count == 0) {
      $id = DB::table('mon_hoc')
        ->insertGetId([
          'ma_mon_hoc' => $ma,
          'ten_mon_hoc' => $ten,
          'so_tin_chi' => $stc,
        ]);
      $num_row = DB::table('mon_hoc')->count();

      return ['id' => encrypt($ma), 'num_row' => $num_row];
    } else {
      return "Đã tồn tại";
    }
  }

  public function importMonHoc(Request $rq)
  {
    $chuongTrinh = $rq->input('chuongTrinh');
    $file = $rq->file('file');
    $data = Excel::toCollection(null, $file);

    DB::beginTransaction();
    try {
      if (!empty($data) && $data->count()) {
        $firstRow = true;
        foreach ($data[0] as $row) {
          if ($firstRow) {
            $firstRow = false;
            continue;
          }

          $row[5] == "x" ? $ten_loai = "Bắt buộc" : $ten_loai = "Tự chọn";
          $ma_loai = DB::table('loai_hoc_phan')->where('ten_loai_hoc_phan', $ten_loai)->value('ma_loai_hoc_phan');
          $ma_khoi_kien_thuc = DB::table('khoi_kien_thuc')->where('ten_khoi_kien_thuc', $row[6])->value('ma_khoi_kien_thuc');

          if (!DB::table('mon_hoc')->where('ma_mon_hoc', $row[2])->exists()) {
            DB::table('mon_hoc')->insert([
              'ma_mon_hoc' => $row[2],
              'ten_mon_hoc' => $row[3],
              'so_tin_chi' => $row[4],
            ]);

            DB::table('thuoc_chuong_trinh_dao_tao')->insert([
              'ma_chuong_trinh' => $chuongTrinh,
              'ma_mon_hoc' => $row[2],
              'ma_loai_hoc_phan' => $ma_loai,
              'ma_khoi_kien_thuc' => $ma_khoi_kien_thuc,
              'thu_tu_hoc_ky' => $row[1],
            ]);

          } else {
            if (!DB::table('thuoc_chuong_trinh_dao_tao')->where('ma_chuong_trinh', $chuongTrinh)->where('ma_mon_hoc', $row[2])->exists()) {
              DB::table('thuoc_chuong_trinh_dao_tao')->insert([
                'ma_chuong_trinh' => $chuongTrinh,
                'ma_mon_hoc' => $row[2],
                'ma_loai_hoc_phan' => $ma_loai,
                'ma_khoi_kien_thuc' => $ma_khoi_kien_thuc,
                'thu_tu_hoc_ky' => $row[1],
              ]);
            }
          }
        }
      }
      DB::commit();
      return "Đã thêm";
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json(['error' => 'Thất bại'], 500);
    }
  }

  //sửa môn
  public function suaMonHoc(Request $rq)
  {
    $ma = $rq->ma;
    $ten = $rq->ten;
    $stc = $rq->stc;

    $result = DB::table('mon_hoc')
      ->where('ma_mon_hoc', $ma)
      ->update([
        'ten_mon_hoc' => $ten,
        'so_tin_chi' => $stc,
      ]);

    if ($result) {
      $mon_hoc = DB::table('mon_hoc')->where('ma_mon_hoc', '=', $ma)->first();
      return ['mon_hoc' => $mon_hoc];
    }
  }

  //xóa môn
  public function xoaMonHoc(Request $rq)
  {
    $ma = $rq->ma;

    $result = DB::table('mon_hoc')
      ->where('ma_mon_hoc', $ma)
      ->delete();

    if ($result) {
      return 1;
    } else {
      return 0;
    }
  }
}