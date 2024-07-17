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

class GiangvienController extends Controller
{
    public function trangGoiY()
    {
        $nguoi_dung = Auth::guard('gv')->user();

        $lop = DB::table('lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        $sv = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        return view('giangvien.goiy', compact('lop', 'sv'));
    }

    function monGoiY(Request $rq)
    {
        $msv = $rq->maSV;
        $sinh_vien = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop','=','sinh_vien.ma_lop')
            ->where('ma_sinh_vien', $msv)
            ->first();
        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $sinh_vien->ma_lop)
            ->first();      
        $khoa = substr($sinh_vien->ma_lop, 2, 2);

        $mon_cai_thien = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien','=', 'bang_diem_mon_hoc.ma_sinh_vien')
            ->join('lop', 'lop.ma_lop','=', 'sinh_vien.ma_lop')
            ->join('chuong_trinh_dao_tao','chuong_trinh_dao_tao.ma_chuong_trinh','=','lop.ma_chuong_trinh')
            ->join('nganh', 'nganh.ma_nganh', '=', 'chuong_trinh_dao_tao.ma_nganh')
            ->where('ten_nganh', $nganh->ten_nganh)
            ->where('sinh_vien.ma_lop', 'like', '%'. $khoa .'%')
            ->where('ten_nhom_mon', 'like', '%'. $khoa .'%')
            ->select('bang_diem_mon_hoc.ma_sinh_vien', 'nhom_mon.ma_nhom_mon', 'bang_diem_mon_hoc.ma_mon_hoc', 'diem_he_4')
            ->where('nhom_mon.ten_nhom_mon', 'not like', '%Kỹ năng mềm%')
            ->where('ten_nhom_mon', 'not like', '%Quốc phòng%')
            ->where('diem_chu', '!=', 'F')
            ->orderBy('bang_diem_mon_hoc.ma_sinh_vien')
            ->orderBy('nhom_mon.ma_nhom_mon')
            ->orderBy('mon_hoc.ma_mon_hoc')
            ->get();
    
        $diem = [];
        $diemsv = [];
        $sv = null;
        $allSubjects = [];
        $studentSubjects = [];
        
        foreach($mon_cai_thien as $mon) {
            $allSubjects[$mon->ma_mon_hoc] = $mon->ma_mon_hoc;
        }
        
        $sv = null;
        foreach($mon_cai_thien as $mon) {
            if ($sv != $mon->ma_sinh_vien) {
                $sv = $mon->ma_sinh_vien;
                if (!empty($diemsv)) {
                    $diem[] = $diemsv;
                }
                $diemsv = array_fill_keys(array_keys($allSubjects), 0); // Initialize with zeros for all subjects
            }
            $diemsv[$mon->ma_mon_hoc] = $mon->diem_he_4 == '' ? 2 : $mon->diem_he_4;
        }
        if (!empty($diemsv)) {
            $diem[] = $diemsv;
        }
        
        foreach($diem as &$diemsv) {
            $diemsv = array_values($diemsv);
        }     

        //nhóm
        $mon_hoc_trong_nhom = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->select('bang_diem_mon_hoc.ma_sinh_vien', 'mon_hoc.ma_mon_hoc', 'nhom_mon.ma_nhom_mon')
            ->where('bang_diem_mon_hoc.ma_sinh_vien', '110120002')
            ->where('nhom_mon.ten_nhom_mon', 'not like', '%Kỹ năng mềm%')
            ->groupBy('bang_diem_mon_hoc.ma_sinh_vien', 'mon_hoc.ma_mon_hoc', 'nhom_mon.ma_nhom_mon');

        $mon_k_trung = DB::table(DB::raw("({$mon_hoc_trong_nhom->toSql()}) as subquery"))
            ->mergeBindings($mon_hoc_trong_nhom) // Merge bindings from subquery
            ->select('subquery.ma_sinh_vien', 'subquery.ma_mon_hoc', DB::raw('MIN(subquery.ma_nhom_mon) as ma_nhom_mon'))
            ->groupBy('subquery.ma_sinh_vien', 'subquery.ma_mon_hoc');

        $mon_k_trung_sorted = $mon_k_trung->orderBy('ma_nhom_mon')->orderBy('ma_mon_hoc')->get();

        $mon_hoc_k_trung_list = $mon_k_trung_sorted->pluck('ma_mon_hoc');

        $mon_hoc_k_trung_array = $mon_hoc_k_trung_list->toArray();

        $so_nhom = DB::table(DB::raw("({$mon_k_trung->toSql()}) as final_query"))
            ->mergeBindings($mon_k_trung) // Merge bindings from subquery
            ->select('final_query.ma_nhom_mon', DB::raw('COUNT(final_query.ma_mon_hoc) as so_mon'))
            ->groupBy('final_query.ma_nhom_mon')
            ->get();

        $nhom_mon = $so_nhom->pluck('so_mon', 'ma_nhom_mon');

        $sinh_vien_diem = DB::table('bang_diem_mon_hoc')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_mon_hoc.ma_sinh_vien')
            ->select('bang_diem_mon_hoc.ma_sinh_vien')
            ->where('sinh_vien.ma_lop', 'like', '%'. $khoa .'%')
            ->distinct()
            ->get()
            ->pluck('ma_sinh_vien')
            ->toArray();
        $vi_tri = array_search($msv, $sinh_vien_diem);

        return ['mon_cai_thien'=>$mon_cai_thien, 'diem'=>$diem, 'nhom_mon'=>$nhom_mon, 'vi_tri'=>$vi_tri, 'sinh_vien'=>$sinh_vien];
    }

    public function ttMonCaiThien(Request $rq)
    {
        $nguoi_dung = DB::table('sinh_vien')->where('ma_sinh_vien', $rq->msv)->first();
        
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ma_sinh_vien)->value('ma_lop');
        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();
        $ctdt = Db::table('lop')->where('ma_lop', $ma_lop)->value('ma_chuong_trinh');

        $mon_cai_thien = $rq->ma_mon_cai_thien;

        $mon_goi_y = DB::table('bang_diem_mon_hoc')
            ->select('bang_diem_mon_hoc.*', 'mon_hoc.*', 'nhom_mon.*') 
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->whereIn('bang_diem_mon_hoc.ma_mon_hoc', $mon_cai_thien)
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ma_sinh_vien)
            ->where('bang_diem_mon_hoc.diem_he_4', '<=', 2.5)
            ->where('bang_diem_mon_hoc.diem_he_4', '!=', '')
            ->get();

        $ds_mon_goi_y = [];
        if($mon_goi_y){
            foreach ($mon_goi_y as $item) {
                $ma_mon_hoc = $item->ma_mon_hoc;

                if (!isset($ds_mon_goi_y[$ma_mon_hoc])) {
                    $ds_mon_goi_y[$ma_mon_hoc] = [
                        'ma_mon_hoc' => $item->ma_mon_hoc,
                        'ten_mon_hoc' => $item->ten_mon_hoc,
                        'so_tin_chi' => $item->so_tin_chi,
                        'diem_he_4' => $item->diem_he_4,
                        'nhom_mon' => []
                    ];
                }

                $ds_mon_goi_y[$ma_mon_hoc]['nhom_mon'][] = [
                    'ma_nhom_mon' => $item->ma_nhom_mon,
                    'ten_nhom_mon' => $item->ten_nhom_mon
                ];
            }
        }

        $diem_k_goi_y = DB::table('bang_diem_mon_hoc')
            ->select('bang_diem_mon_hoc.*', 'mon_hoc.*', 'nhom_mon.*')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ma_sinh_vien)
            ->where('bang_diem_mon_hoc.diem_he_4', '!=', '')
            ->where('bang_diem_mon_hoc.diem_he_4', '<=', 2)
            ->where('bang_diem_mon_hoc.diem_chu', '!=', 'F')
            ->where('ten_nhom_mon', 'not like', '%Kỹ năng mềm')
            ->where('ten_nhom_mon', 'not like', '%Quốc phòng%')
            ->whereNotIn('bang_diem_mon_hoc.ma_mon_hoc', $mon_cai_thien)
            ->get();
        
        $ds_k_goi_y = [];

        if($diem_k_goi_y){
            foreach ($diem_k_goi_y as $item) {
                $ma_mon_hoc = $item->ma_mon_hoc;
        
                if (!isset($ds_k_goi_y[$ma_mon_hoc])) {
                    $ds_k_goi_y[$ma_mon_hoc] = [
                    'ma_mon_hoc' => $item->ma_mon_hoc,
                    'ten_mon_hoc' => $item->ten_mon_hoc,
                    'so_tin_chi' => $item->so_tin_chi,
                    'diem_he_4' => $item->diem_he_4,
                    'nhom_mon' => []
                    ];
                }
        
                $ds_k_goi_y[$ma_mon_hoc]['nhom_mon'][] = [
                    'ma_nhom_mon' => $item->ma_nhom_mon,
                    'ten_nhom_mon' => $item->ten_nhom_mon
                ];
            }
        }

        
        return ['mon_goi_y'=>$ds_mon_goi_y, 'mon_k_goi_y'=>$ds_k_goi_y];

    }

    public function trangDiemManhYeuSV()
    {
        $nguoi_dung = Auth::guard('gv')->user();

        $lop = DB::table('lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        $sv = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        return view('giangvien.phantich', compact('lop', 'sv'));
    }

    public function bdDiemManhYeuSV(Request $rq)
    {
        $ma_sv = $rq->maSV;
        $sinh_vien = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->where('ma_sinh_vien', $ma_sv)->first();

        $diem = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')   
            ->where('ma_sinh_vien', $ma_sv)
            ->where('ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->select('bang_diem_mon_hoc.ma_mon_hoc', 'ten_mon_hoc', 'diem_he_4', 'diem_chu', 'ma_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_mon_hoc.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_mon_hoc.ma_hoc_ky_nien_khoa, 1, 1)")
            ->where('diem_he_4', '!=', '')
            ->get();

        return ['diem'=>$diem, 'sinh_vien'=>$sinh_vien];
    }

    public function ttCaNhan()
    {
        $nguoi_dung = Auth::guard('gv')->user();

        $tt = DB::table('giang_vien')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->first();

        return view('giangvien.ttcanhan', compact(['nguoi_dung', 'tt']));
    }

    public function suaTTCaNhan(Request $rq)
    {
        $nguoi_dung = Auth::guard('gv')->user();

        $gioi = $rq->input('gioi');
        $ngaySinh = $rq->input('ngaySinh');
        $diaChi = $rq->input('diaChi');
        $sdt = $rq->input('sdt');
        $email = $rq->input('email');

        if($ngaySinh != ''){
            $ngaySinh = Carbon::parse($ngaySinh)->timestamp;
        } else{
            $ngaySinh = NULL;
        }
    
        $result = DB::table('giang_vien')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->update([
              'gioi_tinh'     => $gioi,
              'ngay_sinh'     => $ngaySinh,
              'dia_chi'       => $diaChi,
              'so_dien_thoai' => $sdt,            
              'email'         => $email,
            ]);

        $gv = DB::table('giang_vien')->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)->first();
          
        return $gv;
    }

    public function doiMK(Request $rq)
    {
        $nguoi_dung = Auth::guard('gv')->user();
        $mkCu = $rq->mkCu;
        $mkMoi = $rq->mkMoi;

        if(Hash::check($mkCu, $nguoi_dung->mat_khau)){
            $rs = DB::table('tai_khoan')
                ->where('ten_dang_nhap', $nguoi_dung->ten_dang_nhap)
                ->update([
                    'mat_khau' => Hash::make($mkMoi),
                ]);

            return $mkMoi;
        } else{
            return 0;
        }
        
    }

    public function diemLop()
    {
        $nguoi_dung = Auth::guard('gv')->user();
        $lop = DB::table('lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        $maLop = DB::table('lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->where('quan_ly_lop.trang_thai', '=', 'Hiệu lực')
            ->first();

        $hoc_ky = DB::table('bang_diem_mon_hoc')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_mon_hoc.ma_hoc_ky_nien_khoa')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_mon_hoc.ma_sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->where('sinh_vien.ma_lop', $maLop->ma_lop)
            ->distinct()
            ->select('bang_diem_mon_hoc.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->get();

        return view('giangvien.diemlop', compact('lop', 'hoc_ky'));
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

    public function slHocKy(Request $rq)
    {
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

    public function diemSinhVien()
    {
        $nguoi_dung = Auth::guard('gv')->user();

        $lop = DB::table('lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        $sv = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_giang_vien', $nguoi_dung->ten_dang_nhap)
            ->get();

        return view('giangvien.diemsv', compact('lop', 'sv'));
    }

    public function xemDiemSV(Request $rq)
    {
        $maSV = $rq->maSV;

        $sv = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->where('ma_sinh_vien', $maSV)
            ->first();

        $khoa_hoc = substr($sv->ma_lop, 2, 2);

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $sv->ma_lop)
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

        $trung_binh = DB::table('bang_diem_hoc_ky')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->where('ma_sinh_vien', $maSV)
            ->where('trung_binh_hoc_ky', '!=', 0)
            ->where('trung_binh_tich_luy', '!=', 0)
            ->get();

        
        $trung_binh_lop = DB::table('bang_diem_hoc_ky')
            ->select(
                'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa',
                'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa',
                DB::raw('ROUND(AVG(bang_diem_hoc_ky.trung_binh_hoc_ky), 2) as trung_binh_hoc_ky_lop'),
                DB::raw('ROUND(AVG(bang_diem_hoc_ky.trung_binh_tich_luy), 2) as trung_binh_tich_luy_lop')
            )
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_hoc_ky.ma_sinh_vien')
            ->where('sinh_vien.ma_lop', $sv->ma_lop)
            ->where('bang_diem_hoc_ky.trung_binh_hoc_ky', '!=', 0)
            ->where('bang_diem_hoc_ky.trung_binh_tich_luy', '!=', 0)
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->get();

        $trung_binh_khoa = DB::table('bang_diem_hoc_ky')
            ->select(
                'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa',
                'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa',
                DB::raw('ROUND(AVG(bang_diem_hoc_ky.trung_binh_hoc_ky), 2) as trung_binh_hoc_ky_khoa'),
                DB::raw('ROUND(AVG(bang_diem_hoc_ky.trung_binh_tich_luy), 2) as trung_binh_tich_luy_khoa')
            )
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_hoc_ky.ma_sinh_vien')
            ->join('lop','lop.ma_lop','=', 'sinh_vien.ma_lop')
            ->join('chuong_trinh_dao_tao','chuong_trinh_dao_tao.ma_chuong_trinh','=', 'lop.ma_chuong_trinh')
            ->join('nganh','nganh.ma_nganh','=','chuong_trinh_dao_tao.ma_nganh')
            ->where('ten_nganh', $nganh->ten_nganh)
            ->where('sinh_vien.ma_lop', 'like', '%'.$khoa_hoc.'%')
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->where('bang_diem_hoc_ky.trung_binh_hoc_ky', '!=', 0)
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
            ->get();

        return ['sv'=>$sv, 'diem'=>$diem, 'diem_hoc_ky'=>$diem_hoc_ky,
                'trung_binh'=>$trung_binh, 'trung_binh_lop'=>$trung_binh_lop, 'trung_binh_khoa'=>$trung_binh_khoa];

    }
    
    public function slSinhVien(Request $rq)
    {
        $maLop = $rq->maLop;

        $sv = DB::table('sinh_vien')
        ->where('ma_lop', $maLop)
        ->get();

        return $sv;
    }

    public function dsLop($maLop)
    {
        $ma = decrypt($maLop);
        $sv = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->where('sinh_vien.ma_lop', $ma)
            ->get();

        return view('giangvien.dslop', compact('ma', 'sv'));
    }

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

    public function nhapDiem()
    {
        return view('giangvien.nhapdiem');
    }

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
            return "Lỗi dữ liệu";
        }

        } else {
        return "Tệp không tồn tại!";
        }
    }
}
