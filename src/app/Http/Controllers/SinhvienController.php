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

class SinhvienController extends Controller
{
    public function BDTrungBinhNhom(Request $rq)
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_chuong_trinh = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->where('sinh_vien.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->value('ma_chuong_trinh');
        $ma_nhom = $rq->maNhom;

        $trung_binh_nhom = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->selectRaw('nhom_mon.ten_nhom_mon, 
                        nhom_mon.ma_nhom_mon, 
                        ROUND(AVG(CASE 
                                    WHEN bang_diem_mon_hoc.diem_lan_1 != "MT" 
                                    THEN bang_diem_mon_hoc.diem_he_4 
                                    ELSE NULL 
                                END), 2) as trung_binh_nhom, 
                        COUNT(nhom_mon.ma_nhom_mon) as so_mon')

            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->whereNotNull('bang_diem_mon_hoc.diem_he_4')
            ->where(function ($query) use ($ma_chuong_trinh, $ma_nhom) {
                $query->where('nhom_mon.ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
                    ->where('nhom_mon.ma_nhom_mon', $ma_nhom);
            })
            ->groupBy('nhom_mon.ma_nhom_mon', 'nhom_mon.ten_nhom_mon')
            ->get();
            
        $diem_cac_mon = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->select('mon_hoc.ma_mon_hoc', 'mon_hoc.ten_mon_hoc', 'bang_diem_mon_hoc.diem_he_4')
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            // ->whereNotNull('bang_diem_mon_hoc.diem_he_4')
            ->where(function($query) use ($ma_chuong_trinh, $ma_nhom) {
                $query->where('nhom_mon.ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
                    ->where('nhom_mon.ma_nhom_mon', $ma_nhom);
            })
            ->get();
            
        return ['diem_cac_mon'=>$diem_cac_mon, 'trung_binh_nhom'=>$trung_binh_nhom];

    }

    public function bdDiemManhYeu()
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();

        $diem = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')   
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->where('ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->select('bang_diem_mon_hoc.ma_mon_hoc', 'ten_mon_hoc', 'diem_he_4', 'diem_chu', 'ma_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_mon_hoc.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_mon_hoc.ma_hoc_ky_nien_khoa, 1, 1)")
            ->where('diem_he_4', '!=', '')
            ->get();

        return view('sinhvien.phantich', compact('nguoi_dung', 'nganh', 'diem'));
    }

    public function SVCTDaoTao()
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();

        $mon_hoc = DB::table('mon_hoc')
            ->leftjoin('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->leftjoin('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
            ->leftjoin('loai_hoc_phan', 'loai_hoc_phan.ma_loai_hoc_phan', '=', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan')
            ->leftjoin('khoi_kien_thuc', 'khoi_kien_thuc.ma_khoi_kien_thuc', '=', 'thuoc_chuong_trinh_dao_tao.ma_khoi_kien_thuc')
            ->leftjoin('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('lop.ma_lop', $ma_lop)
            ->orderby('thu_tu_hoc_ky')
            ->orderby('mon_hoc.ma_mon_hoc')
            ->get();

        $diem = DB::table('bang_diem_mon_hoc')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->where('diem_chu', '!=', 'F')
            ->get();

        $ctdt = DB::table('chuong_trinh_dao_tao')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('lop.ma_lop', '=', $ma_lop)
            ->first();

        $hk_hien_tai = DB::table('bang_diem_mon_hoc')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->distinct('ma_hoc_ky_nien_khoa')
            ->count('ma_hoc_ky_nien_khoa');

        return view('sinhvien.chuongtrinhdaotao', compact(['nguoi_dung', 'mon_hoc', 'ctdt', 'diem', 'nganh', 'hk_hien_tai']));
    }

    public function xemDiem()
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();

        $diem = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_mon_hoc.ma_hoc_ky_nien_khoa')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->orderby('id')
            ->get();

        $diem_hoc_ky = DB::table('bang_diem_hoc_ky')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->orderby('id', 'desc')
            ->get();

        return view('sinhvien.xemdiem', compact('diem', 'diem_hoc_ky', 'nganh'));
    }

    public function ttCaNhan()
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();

        $tt = DB::table('sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'lop.ma_chuong_trinh')
            ->join('nganh', 'nganh.ma_nganh', '=', 'chuong_trinh_dao_tao.ma_nganh')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->first();

        $so_hk = DB::table('chuong_trinh_dao_tao')
            ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('chuong_trinh_dao_tao.ma_chuong_trinh', $tt->ma_chuong_trinh)
            ->max('thu_tu_hoc_ky');

        return view('sinhvien.ttcanhan', compact(['nguoi_dung', 'tt', 'so_hk', 'nganh']));
    }

    public function suaTTCaNhan(Request $rq)
    {
        $nguoi_dung = Auth::guard('sv')->user();

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
    
        $result = DB::table('sinh_vien')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->update([
              'gioi_tinh'     => $gioi,
              'ngay_sinh'     => $ngaySinh,
              'dia_chi'       => $diaChi,
              'so_dien_thoai' => $sdt,            
              'email'         => $email,
            ]);

        $sv = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->first();
          
        return $sv;
    }

    public function doiMK(Request $rq)
    {
        $nguoi_dung = Auth::guard('sv')->user();
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

    function trangGoiY()
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();

        $data = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->select('bang_diem_mon_hoc.ma_sinh_vien', 
                     'thuoc_nhom_mon.ma_nhom_mon',
                     'bang_diem_mon_hoc.ma_mon_hoc',
                     'bang_diem_mon_hoc.diem_he_4'
                     )
            ->where('ten_nhom_mon', '!=', 'cntt20-Kỹ năng mềm')
            ->get();

        $hk_hien_tai = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->max('thu_tu_hoc_ky');

        $hk_moi = $hk_hien_tai + 1;

        $ma_chuong_trinh = DB::table('lop')
            ->where('ma_lop', $ma_lop)
            ->value('ma_chuong_trinh');

        $mon_hoc = DB::table('mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc','=', 'mon_hoc.ma_mon_hoc')
            ->join('loai_hoc_phan', 'loai_hoc_phan.ma_loai_hoc_phan','=', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh','=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->select('thuoc_nhom_mon.ma_mon_hoc', 'thuoc_nhom_mon.ma_nhom_mon', 'ten_nhom_mon', 'ten_mon_hoc')
            ->where('lop.ma_lop', $ma_lop)
            ->where('ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
            ->where('ten_nhom_mon', '!=', 'cntt20-Kỹ năng mềm')
            ->where('thu_tu_hoc_ky', $hk_moi)
            ->where('ten_loai_hoc_phan', '=', 'Tự chọn')
            ->get();  
            
        $mon_bat_buoc = DB::table('mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('loai_hoc_phan', 'loai_hoc_phan.ma_loai_hoc_phan', '=', 'thuoc_chuong_trinh_dao_tao.ma_loai_hoc_phan')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->select(
                'thuoc_nhom_mon.ma_mon_hoc',
                'ten_mon_hoc',
                'ten_nhom_mon'
            )
            ->where('lop.ma_lop', $ma_lop)
            ->where('ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
            ->where('thu_tu_hoc_ky', $hk_moi)
            ->where('ten_loai_hoc_phan', '=', 'Bắt buộc')
            ->get();
        
        $bat_buoc = [];
        foreach ($mon_bat_buoc as $mon) {
            if (!isset($bat_buoc[$mon->ma_mon_hoc])) {
                $bat_buoc[$mon->ma_mon_hoc] = [
                    'ma_mon_hoc' => $mon->ma_mon_hoc,
                    'ten_mon_hoc' => $mon->ten_mon_hoc,
                    'nhom_mon' => []
                ];
            }
            $bat_buoc[$mon->ma_mon_hoc]['nhom_mon'][] = substr($mon->ten_nhom_mon, strpos($mon->ten_nhom_mon, '-') + 1);
        }
        $mon_bat_buoc = array_values($bat_buoc);        
               

        return view('sinhvien.goiy', compact('nganh', 'data', 'mon_hoc', 'nguoi_dung', 'mon_bat_buoc'));
    }

    function goiYCaiThien(Request $rq)
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');
        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();      
        $khoa = substr($ma_lop, 2, 2);

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

        $sinh_vien = DB::table('bang_diem_mon_hoc')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_mon_hoc.ma_sinh_vien')
            ->select('bang_diem_mon_hoc.ma_sinh_vien')
            ->where('sinh_vien.ma_lop', 'like', '%'. $khoa .'%')
            ->distinct()
            ->get()
            ->pluck('ma_sinh_vien')
            ->toArray();
        $vi_tri = array_search($nguoi_dung->ten_dang_nhap, $sinh_vien);

        return view('sinhvien.goiycaithien', compact('nguoi_dung', 'nganh', 'mon_cai_thien', 'diem', 'nhom_mon', 'vi_tri'));
    }

    public function ttMonCaiThien(Request $rq)
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');
        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();
        $ctdt = Db::table('lop')->where('ma_lop', $ma_lop)->value('ma_chuong_trinh');

        $tich_luy = DB::table('bang_diem_hoc_ky')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->orderByRaw("SUBSTRING(ma_hoc_ky_nien_khoa, -4) DESC")
            ->orderByRaw("SUBSTRING(ma_hoc_ky_nien_khoa, 1, 1) DESC")
            ->first();

        $mon_cai_thien = $rq->ma_mon_cai_thien;

        $mon_goi_y = DB::table('bang_diem_mon_hoc')
            ->select('bang_diem_mon_hoc.*', 'mon_hoc.*', 'nhom_mon.*') 
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->whereIn('bang_diem_mon_hoc.ma_mon_hoc', $mon_cai_thien)
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->where('bang_diem_mon_hoc.diem_he_4', '<', 2)
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
                        'diem_lan_1' => $item->diem_lan_1,
                        'diem_lan_2' => $item->diem_lan_2,
                        'diem_he_4' => $item->diem_he_4,
                        'diem_chu' => $item->diem_chu,
                        'nhom_mon' => []
                    ];
                }

                $ds_mon_goi_y[$ma_mon_hoc]['nhom_mon'][] = [
                    'ma_nhom_mon' => $item->ma_nhom_mon,
                    'ten_nhom_mon' => $item->ten_nhom_mon
                ];
            }
        }

        usort($ds_mon_goi_y, function($a, $b) {
            return $a['diem_he_4'] <=> $b['diem_he_4'];
        });

        if($tich_luy->trung_binh_tich_luy < 2){
            $diem_k_goi_y = DB::table('bang_diem_mon_hoc')
                ->select('bang_diem_mon_hoc.*', 'mon_hoc.*', 'nhom_mon.*')
                ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
                ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
                ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
                ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
                ->where('bang_diem_mon_hoc.diem_he_4', '!=', '')
                ->where('bang_diem_mon_hoc.diem_he_4', '<', 2)
                ->where('bang_diem_mon_hoc.diem_chu', '!=', 'F')
                ->where('ten_nhom_mon', 'not like', '%Kỹ năng mềm')
                ->where('ten_nhom_mon', 'not like', '%Quốc phòng%')
                ->whereNotIn('bang_diem_mon_hoc.ma_mon_hoc', $mon_cai_thien)
                ->orderby('diem_he_4')
                ->get();
        } else{
            $diem_k_goi_y = DB::table('bang_diem_mon_hoc')
                ->select('bang_diem_mon_hoc.*', 'mon_hoc.*', 'nhom_mon.*')
                ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
                ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
                ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
                ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
                ->where('bang_diem_mon_hoc.diem_he_4', '!=', '')
                ->where('bang_diem_mon_hoc.diem_he_4', '<', 3)
                ->where('bang_diem_mon_hoc.diem_chu', '!=', 'F')
                ->where('ten_nhom_mon', 'not like', '%Kỹ năng mềm')
                ->where('ten_nhom_mon', 'not like', '%Quốc phòng%')
                ->whereNotIn('bang_diem_mon_hoc.ma_mon_hoc', $mon_cai_thien)
                ->orderby('diem_he_4')
                ->get();
        }        
        
        $ds_k_goi_y = [];

        if($diem_k_goi_y){
            foreach ($diem_k_goi_y as $item) {
                $ma_mon_hoc = $item->ma_mon_hoc;
        
                if (!isset($ds_k_goi_y[$ma_mon_hoc])) {
                    $ds_k_goi_y[$ma_mon_hoc] = [
                    'ma_mon_hoc' => $item->ma_mon_hoc,
                    'ten_mon_hoc' => $item->ten_mon_hoc,
                    'so_tin_chi' => $item->so_tin_chi,
                    'diem_lan_1' => $item->diem_lan_1,
                    'diem_lan_2' => $item->diem_lan_2,
                    'diem_he_4' => $item->diem_he_4,
                    'diem_chu' => $item->diem_chu,
                    'nhom_mon' => []
                    ];
                }
        
                $ds_k_goi_y[$ma_mon_hoc]['nhom_mon'][] = [
                    'ma_nhom_mon' => $item->ma_nhom_mon,
                    'ten_nhom_mon' => $item->ten_nhom_mon
                ];
            }
        }

        usort($ds_k_goi_y, function($a, $b) {
            return $a['diem_he_4'] <=> $b['diem_he_4'];
        });
       
        return ['mon_goi_y'=>$ds_mon_goi_y, 'mon_k_goi_y'=>$ds_k_goi_y];

    }
}
