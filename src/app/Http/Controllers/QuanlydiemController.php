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
use Illuminate\Support\Facades\File;

class QuanlydiemController extends Controller
{
  //trang nhập điểm
  public function nhapDiem(Request $rq)
  {
    return view('admin.nhapdiem');
  } 

  // nhập điểm từng sinh viên
  public function nhapDiemSV(Request $rq)
  {
    $file = $rq->file('file');
    $data = Excel::toCollection(null, $file);

    DB::beginTransaction();
    try{
      $msv = $data[0][1][1];
      //mã học kỳ
      $hoc_ky = $data[0][6][1];
      $nh = explode(' - ', $data[0][5][1]);
      $hknk = $hoc_ky . substr($nh[0], -2) . substr($nh[1], -2);

      foreach ($data[0] as $row) {
        if (is_numeric($row[0])) {
          if (preg_match('/^(191)\.\d+$/', $row[1])) {  
            $ma_mon = "191.xx";
            } else if (preg_match('/^(192)\.\d+$/', $row[1])) {
            $ma_mon = "192.xx";
            } else if (preg_match('/^(193)\.\d+$/', $row[1])) {
            $ma_mon = "193.xx";
            } else{
            $ma_mon = $row[1];
          }

          $count_mon = DB::table('bang_diem_mon_hoc')->where('ma_sinh_vien', $msv)->where('ma_mon_hoc', $row[1])->where('ma_hoc_ky_nien_khoa', $hknk)->count();

          if($count_mon == 0){ 
            $mon = DB::table('bang_diem_mon_hoc')
              ->insert([
                  'ma_sinh_vien' => $msv,
                  'ma_mon_hoc' => $ma_mon,
                  'ma_hoc_ky_nien_khoa' => $hknk,
                  'diem_lan_1' => $row[4],
                  'diem_lan_2' => $row[5],
                  'diem_he_4' => $row[6],
                  'diem_chu' => $row[7],
                ]);
          } else {
            $mon = DB::table('bang_diem_mon_hoc')
              ->where('ma_sinh_vien', $msv)
              ->where('ma_mon_hoc', $row[1])
              ->where('ma_hoc_ky_nien_khoa', $hknk)
              ->update([
                'diem_lan_1' => $row[4],
                'diem_lan_2' => $row[5],
                'diem_he_4' => $row[6],
                'diem_chu' => $row[7],
              ]);
          }
        }

        if ($row[0] == "ĐTBHK (H4):"){
          $tbhk = $row[1];
        }
        if ($row[0] == "ĐTBTL (H4):"){
          $tbtl = $row[1];
        }    
      }

      // kiểm tra điểm
      $count_hk = DB::table('bang_diem_hoc_ky')->where('ma_sinh_vien', $msv)->where('ma_hoc_ky_nien_khoa',  $hknk)->count();

      if($count_hk == 0){ 
        $hk = DB::table('bang_diem_hoc_ky')
          ->insert([
            'ma_sinh_vien' => $msv,
            'ma_hoc_ky_nien_khoa' => $hknk,
            'trung_binh_hoc_ky' => $tbhk,
            'trung_binh_tich_luy' => $tbtl,
          ]);
      } else{
        $hk = DB::table('bang_diem_hoc_ky')
          ->where('ma_sinh_vien', $msv)
          ->where('ma_hoc_ky_nien_khoa', $hknk)
          ->update([
            'trung_binh_hoc_ky' => $tbhk,
            'trung_binh_tich_luy' => $tbtl,
        ]);
      }

      DB::commit();
      return "Thành công";
    } catch(\Exception $e){
      DB::rollBack();
      return "Lỗi dữ liệu";
    }
  }

  //Nhập điểm nhiều sv cùng 1 học kỳ
  public function nhapDiemNhieuSV(Request $rq)
  {
    $file = $rq->file('file');
    $data = Excel::toCollection(null, $file);

    DB::beginTransaction();
    try{     
      foreach ($data[0] as $row) {      
        if ($row[0] == "MSSV:") {
          $masv = $row[1];
        }
      
        if ($row[0] == "Năm học:") {
          $nk = explode(' - ', $row[1]);
          $nk = substr($nk[0], -2) . substr($nk[1], -2);
        }
      
        if ($row[0] == "Học kỳ:") {
          $hk = $row[1];
          $hknk = $hk . $nk;
        }
      
        if (is_numeric($row[0])) {
          if (preg_match('/^(191)\.\d+$/', $row[1])) {
            $maMon = "191.xx";
          } else if (preg_match('/^(192)\.\d+$/', $row[1])) {
            $maMon = "192.xx";
          } else if (preg_match('/^(193)\.\d+$/', $row[1])) {
            $maMon = "193.xx";
          } else {
            $maMon = $row[1];
          }

          $diem_mon[] = [
            'ma_sv' => $masv,
            'hknk' => $hknk,
            'ma_mon' => $maMon,
            'stc' => $row[3],
            'lan1' => $row[4],
            'lan2' => $row[5],
            'he4' => $row[6],
            'chu' => $row[7]
          ];
        }
      
        if ($row[0] == "ĐTBHK (H4):") {
          $tbhk = $row[1];
        }
      
        if ($row[0] == "ĐTBTL (H4):") {
          $tbtl = $row[1];
        }

        if ($row[0] == "ĐTBTL (H4):") {
          $diem_hk[] = [
            'ma_sv' => $masv,
            'hknk' => $hknk,
            'tbhk' => $tbhk,
            'tbtl' => $tbtl,
          ];
        }
      }

      $this->nhapDiemMon($diem_mon);
      $this->nhapDiemHK($diem_hk);

      DB::commit();
      return "Thành công";
    } catch(\Exception $e){
      DB::rollBack();
      return "Lỗi dữ liệu: " . $e->getMessage();
    }
  }

  public function nhapDiemMon($diem_mon)
  {
    foreach($diem_mon as $row){
      $count_mon = DB::table('bang_diem_mon_hoc')
        ->where('ma_sinh_vien', $row['ma_sv'])
        ->where('ma_mon_hoc', $row['ma_mon'])
        ->where('ma_hoc_ky_nien_khoa', $row['hknk'])
        ->count();

      if($count_mon == 0){ 
        $mon = DB::table('bang_diem_mon_hoc')
          ->insert([
            'ma_sinh_vien' => $row['ma_sv'],
            'ma_mon_hoc' => $row['ma_mon'],
            'ma_hoc_ky_nien_khoa' => $row['hknk'],
            'diem_lan_1' => $row['lan1'],
            'diem_lan_2' => $row['lan2'],
            'diem_he_4' => $row['he4'],
            'diem_chu' => $row['chu'],
            ]);
      } else{
        $mon = DB::table('bang_diem_mon_hoc')
          ->where('ma_sinh_vien', $row['ma_sv'])
          ->where('ma_mon_hoc', $row['ma_mon'])
          ->where('ma_hoc_ky_nien_khoa', $row['hknk'])
          ->update([
            'diem_lan_1' => $row['lan1'],
            'diem_lan_2' => $row['lan2'],
            'diem_he_4' => $row['he4'],
            'diem_chu' => $row['chu'],
          ]);
      }
    }
  }

  public function nhapDiemHK($diem_hk)
  {
    foreach($diem_hk as $row){
      $count_hk = DB::table('bang_diem_hoc_ky')->where('ma_sinh_vien', $row['ma_sv'])->where('ma_hoc_ky_nien_khoa',  $row['hknk'])->count();

      if($count_hk == 0){ 
        $hk = DB::table('bang_diem_hoc_ky')
          ->insert([
            'ma_sinh_vien' => $row['ma_sv'],
            'ma_hoc_ky_nien_khoa' => $row['hknk'],
            'trung_binh_hoc_ky' => $row['tbhk'],
            'trung_binh_tich_luy' => $row['tbtl'],
          ]);
      } else{
        DB::table('bang_diem_hoc_ky')
          ->where('ma_sinh_vien', $row['ma_sv'])
          ->where('ma_hoc_ky_nien_khoa', $row['hknk'])
          ->update([
            'trung_binh_hoc_ky' => $row['tbhk'],
            'trung_binh_tich_luy' => $row['tbtl'],
        ]);
      }
    }
  }

  // nhập điểm toàn khóa
  public function nhapDiemFile(Request $rq)
  {
    $diem_mon = $rq->diem_mon;
    $diem_hk = $rq->diem_hk;

    if (File::exists($diem_mon) && File::exists($diem_hk)) {
      $data_diem_mon = Excel::toCollection(null, $diem_mon);
      $data_diem_hk = Excel::toCollection(null, $diem_hk);

      DB::beginTransaction();
      try{
        if (!empty($data_diem_mon) && $data_diem_mon->count()) {
          $firstRow = true;
          foreach ($data_diem_mon[0] as $row) {
            if ($firstRow) {
              $firstRow = false; 
              continue;
            }

            // kiểm tra mã môn thể chất
            if (preg_match('/^(191)\.\d+$/', $row[2])) {  
              $ma_mon = "191.xx";
              } else if (preg_match('/^(192)\.\d+$/', $row[2])) {
              $ma_mon = "192.xx";
              } else if (preg_match('/^(193)\.\d+$/', $row[2])) {
              $ma_mon = "193.xx";
              } else{
              $ma_mon = $row[2];
            }

            // đổi điểm chữ
            switch ($row[5]) {
              case "":
                $diem_chu = $row[3];
                break;
              case 4.0:
                $diem_chu = "A";
                break;
              case 3.5:
                $diem_chu = "B+";
                break;
              case 3.0:
                $diem_chu = "B";
                break;
              case 2.5:
                $diem_chu = "C+";
                break;
              case 2.0:
                $diem_chu = "C";
                break;
              case 1.5:
                $diem_chu = "D+";
                break;
              case 1.0:
                $diem_chu = "D";
                break;
              default:
                $diem_chu = "F";
                break;
            }

            if(!DB::table('sinh_vien')->where('ma_sinh_vien', $row[1])->exists()){
              return $row[1];
            }

            if(!DB::table('mon_hoc')->where('ma_mon_hoc', $ma_mon)->exists()){
              return $ma_mon;
            }

            $count = DB::table('bang_diem_mon_hoc')->where('ma_sinh_vien', $row[1])->where('ma_mon_hoc', $ma_mon)->where('ma_hoc_ky_nien_khoa', $row[0])->count();

            if($count == 0){      
              DB::table('bang_diem_mon_hoc')
                ->insert([
                  'ma_sinh_vien' => $row[1],
                  'ma_mon_hoc' => $ma_mon,
                  'ma_hoc_ky_nien_khoa' => $row[0],
                  'diem_lan_1' => $row[3],
                  'diem_lan_2' => $row[4],
                  'diem_he_4' => $row[5],
                  'diem_chu' => $diem_chu,
                ]);
                
            } else{
              DB::table('bang_diem_mon_hoc')
                ->where('ma_sinh_vien', $row[1])
                ->where('ma_mon_hoc', $ma_mon)
                ->where('ma_hoc_ky_nien_khoa', $row[0])
                ->update([
                  'diem_lan_1' => $row[3],
                  'diem_lan_2' => $row[4],
                  'diem_he_4' => $row[5],
                  'diem_chu' => $diem_chu,
                ]);
            }
          }
        }
        

        if (!empty($data_diem_hk) && $data_diem_hk->count()) {
          $firstRow = true;
          foreach ($data_diem_hk[0] as $row) {
            if ($firstRow) {
              $firstRow = false; 
              continue;
            }

            $count = DB::table('bang_diem_hoc_ky')->where('ma_sinh_vien', $row[1])->where('ma_hoc_ky_nien_khoa', $row[0])->count();

            if($count == 0){   
              DB::table('bang_diem_hoc_ky')
                ->insert([
                  'ma_sinh_vien' => $row[1],
                  'ma_hoc_ky_nien_khoa' => $row[0],
                  'trung_binh_hoc_ky' => $row[3],
                  'trung_binh_tich_luy' => $row[4],
                ]);

                
            } else{
              DB::table('bang_diem_hoc_ky')
                ->where('ma_sinh_vien', $row[1])
                ->where('ma_hoc_ky_nien_khoa', $row[0])
                ->update([
                  'trung_binh_hoc_ky' => $row[3],
                  'trung_binh_tich_luy' => $row[4],
              ]);
            }
          }
        }

        DB::commit();
        return "Thành công";
      } catch(\Exception $e){
        DB::rollBack();
        return $e;
      }

    } else {
      return "Tệp không tồn tại!";
    }
  }

  public function diemLop()
  {
    $lop = DB::table('lop')->get();
    $maLop = DB::table('lop')->first();

    $hoc_ky = DB::table('bang_diem_mon_hoc')
      ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_mon_hoc.ma_hoc_ky_nien_khoa')
      ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_mon_hoc.ma_sinh_vien')
      ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
      ->where('sinh_vien.ma_lop', $maLop->ma_lop)
      ->distinct()
      ->select('bang_diem_mon_hoc.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
      ->get();

    return view('admin.diemlop', compact('lop', 'hoc_ky'));
  }

  public function slHocKy(Request $rq){
    $maLop = $rq->maLop;

    $hoc_ky = DB::table('bang_diem_mon_hoc')
      ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_mon_hoc.ma_hoc_ky_nien_khoa')
      ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_mon_hoc.ma_sinh_vien')
      ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
      ->where('sinh_vien.ma_lop', $maLop)
      ->distinct()
      ->select('bang_diem_mon_hoc.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
      ->get();

    return $hoc_ky;
  }

  public function xemDiemLop(Request $rq)
  {
    $maLop = $rq->maLop;
    $maKy = $rq->maHK;

    $diem = DB::table('sinh_vien')
      ->join('bang_diem_mon_hoc', 'bang_diem_mon_hoc.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
      ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
      ->where('sinh_vien.ma_lop', $maLop)
      ->where('bang_diem_mon_hoc.ma_hoc_ky_nien_khoa', $maKy)
      ->select('sinh_vien.ho_ten', 'mon_hoc.*', 'bang_diem_mon_hoc.*')
      ->get()
      ->groupBy('ma_sinh_vien');

    $hoc_ky = DB::table('sinh_vien')
      ->join('bang_diem_hoc_ky', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
      ->where('sinh_vien.ma_lop', $maLop)
      ->select('sinh_vien.ho_ten', 'bang_diem_hoc_ky.*')
      ->get()
      ->groupBy('ma_sinh_vien');

    $tich_luy = Db::table('bang_diem_hoc_ky')
      ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_hoc_ky.ma_sinh_vien')
      ->where('ma_lop', $maLop)
      ->where('ma_hoc_ky_nien_khoa', $maKy)
      ->get();

    return ['diem'=>$diem, 'tich_luy'=>$tich_luy];
  }

  public function diemSinhVien()
  {
    $sv = DB::table('sinh_vien')->get();
    return view('admin.diemsv', compact('sv'));
  }

  public function xemDiemSV(Request $rq)
  {
    $maSV = $rq->maSV;

    $sv = DB::table('sinh_vien')
      ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
      ->where('ma_sinh_vien', $maSV)
      ->first();

    $diem = DB::table('bang_diem_mon_hoc')
      ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
      ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_mon_hoc.ma_hoc_ky_nien_khoa')
      ->where('ma_sinh_vien', $maSV)
      ->orderby('id')
      ->get();

    $diem_hoc_ky = DB::table('bang_diem_hoc_ky')
      ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
      ->where('ma_sinh_vien', $maSV)
      ->orderby('id', 'desc')
      ->get();

    return ['sv'=>$sv, 'diem'=>$diem, 'diem_hoc_ky'=>$diem_hoc_ky];

  }


}