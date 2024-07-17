<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\taikhoan;

class LoginController extends Controller
{
    public function index(Request $rq)
    {
        if(Auth::guard('admin')->check()){
            return redirect('admin/nganh');
        }
        else if(Auth::guard('gv')->check()){
            return redirect('admin/bomon');
        }
        else if(Auth::guard('sv')->check()){
            return redirect('admin/khoa');
        }
        else{
            return view('index');
        }
    }

    public function dangXuat()
    {
        Auth::guard('admin')->logout();
        Auth::guard('gv')->logout();
        Auth::guard('sv')->logout();
        return redirect()->route('loginpage');
    }

    public function dangNhap(Request $rq)
    {
        $ten = $rq->input('txtTenDN');
        $mk = $rq->input('txtMatKhau');        
        $mk_ma_hoa = DB::table('tai_khoan')->where('ten_dang_nhap', $ten)->value('mat_khau');
        $nguoi_dung = taikhoan::where('ten_dang_nhap', $ten)->where('trang_thai', '=', '0')->first();

        if($ten == "ngoccham" && $mk == "ngoccham" && $nguoi_dung->quyen_truy_cap == 0){
            Auth::guard('admin')->login($nguoi_dung);
            return redirect('admin/trangchu');
        }

        if(Hash::check($mk, $mk_ma_hoa)){
            if($nguoi_dung->quyen_truy_cap == 1){
                Auth::guard('gv')->login($nguoi_dung);
                return redirect('gv/trangchu');

            } else if($nguoi_dung->quyen_truy_cap == 2){
                Auth::guard('sv')->login($nguoi_dung);
                return redirect('sv/trangchu');
            }
        } else{ 
            Session::flash('error', 'Thông tin đăng nhập không chính xác');
            return view('index');
        }

    }

    public function trangChuAdmin()
    {
        $nganh = DB::table('nganh')->count();
        $sv = DB::table('sinh_vien')->count();        
        $gv = DB::table('giang_vien')->count();
        $mon = DB::table('mon_hoc')->count();

        $ds_lop = DB::table('lop')->get();
        // biểu đồ cột
        $dataSV = DB::table('bang_diem_hoc_ky')
            ->join('sinh_vien', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->select('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa', DB::raw('COUNT(*) as total_students'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky < 2 THEN 1 ELSE 0 END) AS kem'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 2.6 THEN 1 ELSE 0 END) AS trung_binh'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2.6 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.2 THEN 1 ELSE 0 END) AS kha'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.6 THEN 1 ELSE 0 END) AS gioi'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.6 THEN 1 ELSE 0 END) AS xuat_sac'))
            ->where('sinh_vien.ma_lop', '=', 'DA20TTA')
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
            ->havingRaw('total_students > 5') 
            ->get();

        $ds_nganh = DB::table('nganh')
        ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
        ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
        ->select('nganh.ten_nganh', 'lop.ma_lop')
        ->get()
        ->map(function ($item) {
            $yearPrefix = substr($item->ma_lop, 2, 2);
            $khoa = '20' . $yearPrefix;
            return [
                'ten_nganh' => $item->ten_nganh,
                'khoa' => $khoa
            ];
        })
        ->unique()
        ->values();

        $bd_mien = DB::table('bang_diem_hoc_ky')
            ->join('sinh_vien', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->join('lop','lop.ma_lop','=', 'sinh_vien.ma_lop')
            ->join('chuong_trinh_dao_tao','chuong_trinh_dao_tao.ma_chuong_trinh','=', 'lop.ma_chuong_trinh')
            ->join('nganh','nganh.ma_nganh','=','chuong_trinh_dao_tao.ma_nganh')
            ->select(
                'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa',
                'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa',
                DB::raw('COUNT(*) as total_students'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky < 2 THEN 1 ELSE 0 END) AS kem'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 2.6 THEN 1 ELSE 0 END) AS trung_binh'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2.6 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.2 THEN 1 ELSE 0 END) AS kha'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.6 THEN 1 ELSE 0 END) AS gioi'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.6 THEN 1 ELSE 0 END) AS xuat_sac'))
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->where('nganh.ten_nganh', '=', 'Công nghệ thông tin')
            ->where('sinh_vien.ma_lop', 'like', '%20%')
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
            ->havingRaw('total_students > 10') 
            ->get();

        $bd_mien_data = $bd_mien->map(function($item) {
            return [
                'ma_hoc_ky_nien_khoa' => $item->ma_hoc_ky_nien_khoa,
                'ten_hoc_ky_nien_khoa' => $item->ten_hoc_ky_nien_khoa,
                'kem' => number_format($item->kem / $item->total_students * 100, 2),
                'trung_binh' => number_format($item->trung_binh / $item->total_students * 100, 2),
                'kha' => number_format($item->kha / $item->total_students * 100, 2),
                'gioi' => number_format($item->gioi / $item->total_students * 100, 2),
                'xuat_sac' => number_format($item->xuat_sac / $item->total_students * 100, 2)
            ];
        }); 
    
    
        return view('admin.trangchu', compact('sv', 'nganh', 'gv', 'mon', 'ds_lop', 'dataSV', 'bd_mien_data', 'ds_nganh'));
    }

    public function bieuDoXepLoaiNganhKhoa(Request $rq)
    {
        $nganhKhoa = $rq->nganhKhoa;
        $phan = explode(' - Khóa ', $nganhKhoa);

        $ten_nganh = trim($phan[0]);
        $khoa = substr(trim($phan[1]), -2);

        $bd_mien = DB::table('bang_diem_hoc_ky')
            ->join('sinh_vien', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->join('lop','lop.ma_lop','=', 'sinh_vien.ma_lop')
            ->join('chuong_trinh_dao_tao','chuong_trinh_dao_tao.ma_chuong_trinh','=', 'lop.ma_chuong_trinh')
            ->join('nganh','nganh.ma_nganh','=','chuong_trinh_dao_tao.ma_nganh')
            ->select(
                'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa',
                'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa',
                DB::raw('COUNT(*) as total_students'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky < 2 THEN 1 ELSE 0 END) AS kem'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 2.6 THEN 1 ELSE 0 END) AS trung_binh'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2.6 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.2 THEN 1 ELSE 0 END) AS kha'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.6 THEN 1 ELSE 0 END) AS gioi'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.6 THEN 1 ELSE 0 END) AS xuat_sac'))
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->where('nganh.ten_nganh', '=', $ten_nganh)
            ->where('sinh_vien.ma_lop', 'like', '%' . $khoa . '%')
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
            ->havingRaw('total_students > 10') 
            ->get();

        $bd_mien_data = $bd_mien->map(function($item) {
            return [
                'ma_hoc_ky_nien_khoa' => $item->ma_hoc_ky_nien_khoa,
                'ten_hoc_ky_nien_khoa' => $item->ten_hoc_ky_nien_khoa,
                'kem' => number_format($item->kem / $item->total_students * 100, 2),
                'trung_binh' => number_format($item->trung_binh / $item->total_students * 100, 2),
                'kha' => number_format($item->kha / $item->total_students * 100, 2),
                'gioi' => number_format($item->gioi / $item->total_students * 100, 2),
                'xuat_sac' => number_format($item->xuat_sac / $item->total_students * 100, 2)
            ];
        }); 

        return $bd_mien_data;
    }
    
    public function bieuDoXepLoai(Request $rq)
    {
        $maLop = $rq->maLop;

        $dataSV = DB::table('bang_diem_hoc_ky')
            ->join('sinh_vien', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
            ->select('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', DB::raw('COUNT(*) as total_students'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky < 2 THEN 1 ELSE 0 END) AS kem'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 2.6 THEN 1 ELSE 0 END) AS trung_binh'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2.6 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.2 THEN 1 ELSE 0 END) AS kha'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.6 THEN 1 ELSE 0 END) AS gioi'),
                DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.6 THEN 1 ELSE 0 END) AS xuat_sac'))
            ->where('sinh_vien.ma_lop', '=', $maLop)
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
            ->havingRaw('total_students > 5') 
            ->get();

        return $dataSV;
    }

    public function trangChuSV()
    {
        $nguoi_dung = Auth::guard('sv')->user();
        $ma_lop = DB::table('sinh_vien')->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)->value('ma_lop');

        $nganh = Db::table('nganh')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_nganh', '=', 'nganh.ma_nganh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('ma_lop', $ma_lop)
            ->first();

        $so_hoc_ky = DB::table('thuoc_chuong_trinh_dao_tao')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->join('sinh_vien', 'sinh_vien.ma_lop', '=', 'lop.ma_lop')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->max('thu_tu_hoc_ky');

        $hk_hien_tai = DB::table('bang_diem_mon_hoc')
            ->join('sinh_vien', 'sinh_vien.ma_sinh_vien', '=', 'bang_diem_mon_hoc.ma_sinh_vien')
            ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'lop.ma_chuong_trinh')
            ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('sinh_vien.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->max('thu_tu_hoc_ky');

        $stc = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_chuong_trinh_dao_tao', 'thuoc_chuong_trinh_dao_tao.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('chuong_trinh_dao_tao', 'chuong_trinh_dao_tao.ma_chuong_trinh', '=', 'thuoc_chuong_trinh_dao_tao.ma_chuong_trinh')
            ->join('khoi_kien_thuc', 'khoi_kien_thuc.ma_khoi_kien_thuc', '=', 'thuoc_chuong_trinh_dao_tao.ma_khoi_kien_thuc')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('bang_diem_mon_hoc.diem_chu', '!=', 'F')
            ->where(function ($query) {
                $query->where('khoi_kien_thuc.ten_khoi_kien_thuc', 'NOT LIKE', '%thể chất%')
                      ->where('khoi_kien_thuc.ten_khoi_kien_thuc', 'NOT LIKE', '%quốc phòng%');
            })
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->where('lop.ma_lop', $ma_lop)
            ->sum('mon_hoc.so_tin_chi');

        $tong_tc = DB::table('chuong_trinh_dao_tao')
            ->join('lop', 'lop.ma_chuong_trinh', '=', 'chuong_trinh_dao_tao.ma_chuong_trinh')
            ->where('lop.ma_lop', $ma_lop)
            ->first();

        $tich_luy = DB::table('bang_diem_hoc_ky')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->orderByRaw("SUBSTRING(ma_hoc_ky_nien_khoa, -4) DESC")
            ->orderByRaw("SUBSTRING(ma_hoc_ky_nien_khoa, 1, 1) DESC")
            ->first();

        // biểu đồ đường
        $trung_binh = DB::table('bang_diem_hoc_ky')
            ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
            ->where('ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->where('trung_binh_hoc_ky', '!=', 0)
            ->where('trung_binh_tich_luy', '!=', 0)
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
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
            ->where('sinh_vien.ma_lop', $ma_lop)
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->where('bang_diem_hoc_ky.trung_binh_hoc_ky', '!=', 0)
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
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
            ->where('sinh_vien.ma_lop', 'like', '%20%')
            ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
            ->where('bang_diem_hoc_ky.trung_binh_hoc_ky', '!=', 0)
            ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
            ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
            ->get();
        
        $ma_chuong_trinh = DB::table('lop')
            ->where('ma_lop', $ma_lop)
            ->value('ma_chuong_trinh');

        $nhom_mon = DB::table('nhom_mon')
            ->where('ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
            ->where('ten_nhom_mon', 'not like', '%kỹ năng mềm%')
            ->get();

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
            ->where('nhom_mon.ma_nhom_mon', function($query) use ($ma_chuong_trinh) {
                $query->select('ma_nhom_mon')
                    ->from('nhom_mon')
                    ->where('ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
                    ->orderBy('ma_nhom_mon')
                    ->limit(1);
            })
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->whereNotNull('bang_diem_mon_hoc.diem_he_4')
            ->groupBy('nhom_mon.ma_nhom_mon', 'nhom_mon.ten_nhom_mon')
            ->get();
            
        $diem_cac_mon = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->select('mon_hoc.ma_mon_hoc', 'mon_hoc.ten_mon_hoc', 'bang_diem_mon_hoc.diem_he_4')
            ->where('nhom_mon.ma_nhom_mon', function($query) use ($ma_chuong_trinh) {
                $query->select('ma_nhom_mon')
                    ->from('nhom_mon')
                    ->where('ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
                    ->orderBy('ma_nhom_mon')
                    ->limit(1);
            })
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            // ->whereNotNull('bang_diem_mon_hoc.diem_he_4')
            // ->where('bang_diem_mon_hoc.diem_he_4', '!=', 0)
            ->get();


        $data = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->select('bang_diem_mon_hoc.ma_sinh_vien', 
                     'thuoc_nhom_mon.ma_nhom_mon',
                     'bang_diem_mon_hoc.ma_mon_hoc',
                     'bang_diem_mon_hoc.diem_he_4'
                     )
            ->get();

        // so sánh nhóm
        $diem_tb_nhom = DB::table('bang_diem_mon_hoc')
            ->join('mon_hoc', 'mon_hoc.ma_mon_hoc', '=', 'bang_diem_mon_hoc.ma_mon_hoc')
            ->join('thuoc_nhom_mon', 'thuoc_nhom_mon.ma_mon_hoc', '=', 'mon_hoc.ma_mon_hoc')
            ->join('nhom_mon', 'nhom_mon.ma_nhom_mon', '=', 'thuoc_nhom_mon.ma_nhom_mon')
            ->selectRaw('nhom_mon.ten_nhom_mon, 
                         nhom_mon.ma_nhom_mon, 
                         ROUND(AVG(bang_diem_mon_hoc.diem_he_4), 2) as trung_binh_nhom, 
                         COUNT(nhom_mon.ma_nhom_mon) as so_mon')
            ->where('ten_nhom_mon', 'like', $ma_chuong_trinh . '%')
            ->where('ten_nhom_mon', 'not like', '%Kỹ năng mềm')
            ->where('bang_diem_mon_hoc.ma_sinh_vien', $nguoi_dung->ten_dang_nhap)
            ->whereNotNull('bang_diem_mon_hoc.diem_he_4')
            ->where('bang_diem_mon_hoc.diem_lan_1', '!=', 'MT')
            ->groupBy('nhom_mon.ma_nhom_mon', 'nhom_mon.ten_nhom_mon')
            ->get();

        // dd($diem_tb_nhom);


        return view('sinhvien.trangchu', 
            compact('nganh', 'so_hoc_ky', 'hk_hien_tai', 'stc', 'tong_tc', 'tich_luy',
                    'trung_binh', 'trung_binh_lop', 'trung_binh_khoa',
                    'nhom_mon', 'trung_binh_nhom', 'diem_cac_mon',
                    'data',
                    'diem_tb_nhom'));
    }

    public function trangChuGV()
    {
        $nguoi_dung = Auth::guard('gv')->user();

        $lop = DB::table('quan_ly_lop')
            ->where('quan_ly_lop.ma_giang_vien', '=', $nguoi_dung->ten_dang_nhap)
            ->get();

        $lop_hl = DB::table('quan_ly_lop')
            ->where('quan_ly_lop.ma_giang_vien', '=', $nguoi_dung->ten_dang_nhap)
            ->where('trang_thai', '=', 'Hiệu lực')
            ->first();

        if($lop_hl){
            $data = DB::table('bang_diem_hoc_ky')
                ->join('sinh_vien', 'bang_diem_hoc_ky.ma_sinh_vien', '=', 'sinh_vien.ma_sinh_vien')
                ->join('hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ma_hoc_ky_nien_khoa', '=', 'bang_diem_hoc_ky.ma_hoc_ky_nien_khoa')
                ->join('lop', 'lop.ma_lop', '=', 'sinh_vien.ma_lop')
                ->join('quan_ly_lop', 'quan_ly_lop.ma_lop', '=', 'lop.ma_lop')
                ->select('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa', DB::raw('COUNT(*) as total_students'),
                    DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky < 2 THEN 1 ELSE 0 END) AS kem'),
                    DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 2.6 THEN 1 ELSE 0 END) AS trung_binh'),
                    DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 2.6 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.2 THEN 1 ELSE 0 END) AS kha'),
                    DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.2 AND bang_diem_hoc_ky.trung_binh_hoc_ky < 3.6 THEN 1 ELSE 0 END) AS gioi'),
                    DB::raw('SUM(CASE WHEN bang_diem_hoc_ky.trung_binh_hoc_ky >= 3.6 THEN 1 ELSE 0 END) AS xuat_sac'))
                ->where('quan_ly_lop.ma_lop', '=', $lop_hl->ma_lop)
                ->where('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'not like', '3%')
                ->groupBy('bang_diem_hoc_ky.ma_hoc_ky_nien_khoa', 'hoc_ky_nien_khoa.ten_hoc_ky_nien_khoa')
                ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, -4)")
                ->orderByRaw("SUBSTRING(bang_diem_hoc_ky.ma_hoc_ky_nien_khoa, 1, 1)")
                ->havingRaw('total_students > 15') 
                ->get();
        } else{
            $lop = 0;
            $lop_hl = 0;
            $data = 0;
        }

        return view('giangvien.trangchu', compact('lop', 'lop_hl', 'data'));
    }

}
