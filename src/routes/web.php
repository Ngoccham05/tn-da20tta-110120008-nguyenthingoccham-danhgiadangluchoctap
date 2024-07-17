<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'App\Http\Controllers\LoginController@index')->name('loginpage');
Route::get('/quydinh', 'App\Http\Controllers\LoginController@quydinh');
Route::post('/dangnhap', 'App\Http\Controllers\LoginController@dangNhap');
Route::get('/dangxuat', 'App\Http\Controllers\LoginController@dangXuat');

Route::middleware('svlogin')->group(function () {
    Route::prefix('sv')->group(function () {
        Route::get('/trangchu', 'App\Http\Controllers\LoginController@trangChuSV');

        Route::get('/bieudotrungbinh', 'App\Http\Controllers\SinhvienController@BDTrungBinhNhom');
        Route::post('/ttmoncaithien', 'App\Http\Controllers\SinhvienController@ttMonCaiThien');

        Route::get('/ctdaotao', 'App\Http\Controllers\SinhvienController@SVCTDaoTao');
        Route::get('/ttcanhan', 'App\Http\Controllers\SinhvienController@ttCaNhan');
        Route::post('/suatt', 'App\Http\Controllers\SinhvienController@suaTTCaNhan');
        Route::post('/doimk', 'App\Http\Controllers\SinhvienController@doiMK');
        Route::get('/xemdiem', 'App\Http\Controllers\SinhvienController@xemDiem');

        // Route::get('/goiy', 'App\Http\Controllers\SinhvienController@trangGoiY');
        Route::get('/goiycaithien', 'App\Http\Controllers\SinhvienController@goiYCaiThien');
        Route::get('/manhyeu', 'App\Http\Controllers\SinhvienController@bdDiemManhYeu');
    });

});

Route::middleware('gvlogin')->group(function () {
    Route::prefix('gv')->group(function () {
        Route::get('/trangchu', 'App\Http\Controllers\LoginController@trangChuGV');
        Route::get('/ttcanhan', 'App\Http\Controllers\GiangvienController@ttCaNhan');
        Route::post('/suatt', 'App\Http\Controllers\GiangvienController@suaTTCaNhan');
        Route::post('/doimk', 'App\Http\Controllers\GiangvienController@doiMK');

        Route::get('/goiycaithien', 'App\Http\Controllers\GiangvienController@trangGoiY');
        Route::get('/mongoiy', 'App\Http\Controllers\GiangvienController@monGoiY');
        Route::post('/ttmoncaithien', 'App\Http\Controllers\GiangvienController@ttMonCaiThien');

        Route::get('/manhyeu', 'App\Http\Controllers\GiangvienController@trangDiemManhYeuSV');
        Route::get('/phantichmanhyeu', 'App\Http\Controllers\GiangvienController@bdDiemManhYeuSV');

        Route::get('/dslop/{maLop}', 'App\Http\Controllers\GiangvienController@dsLop');
        Route::get('/ttsv', 'App\Http\Controllers\GiangvienController@thongTinSinhVien');

        Route::get('/nhapdiem', 'App\Http\Controllers\GiangvienController@nhapDiem');
        Route::post('/nhapdfile', 'App\Http\Controllers\GiangvienController@nhapDiemFile');
        Route::post('/nhapdiem1sv', 'App\Http\Controllers\GiangvienController@nhapDiemSV');

        Route::get('/diemlop', 'App\Http\Controllers\GiangvienController@diemLop');
        Route::get('/xemdiemlop', 'App\Http\Controllers\GiangvienController@xemDiemLop');
        Route::get('/slhocky', 'App\Http\Controllers\GiangvienController@slHocKy');

        Route::get('/diemsv', 'App\Http\Controllers\GiangvienController@diemSinhVien');
        Route::get('/xemdiemsv', 'App\Http\Controllers\GiangvienController@xemDiemSV');
        Route::get('/slsinhvien', 'App\Http\Controllers\GiangvienController@slSinhVien');

    });

});

Route::middleware('adminlogin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/trangchu', 'App\Http\Controllers\LoginController@trangChuAdmin');
        Route::get('/bieudoxeploai', 'App\Http\Controllers\LoginController@bieuDoXepLoai');
        Route::get('/bdxlnganhkhoa', 'App\Http\Controllers\LoginController@bieuDoXepLoaiNganhKhoa');


        // danh mục
        Route::get('/khoa', 'App\Http\Controllers\DanhmucController@khoa')->name('khoa');
        Route::post('/themkhoa', 'App\Http\Controllers\DanhmucController@themKhoa');
        Route::post('/suakhoa', 'App\Http\Controllers\DanhmucController@suaKhoa');
        Route::post('/xoakhoa', 'App\Http\Controllers\DanhmucController@xoaKhoa');

        Route::get('/bomon', 'App\Http\Controllers\DanhmucController@boMon');
        Route::post('/thembomon', 'App\Http\Controllers\DanhmucController@themBoMon');
        Route::post('/suabomon', 'App\Http\Controllers\DanhmucController@suaBoMon');
        Route::post('/xoabomon', 'App\Http\Controllers\DanhmucController@xoaBoMon');

        Route::get('/nganh', 'App\Http\Controllers\DanhmucController@nganh');
        Route::post('/themnganh', 'App\Http\Controllers\DanhmucController@themNganh');
        Route::post('/suanganh', 'App\Http\Controllers\DanhmucController@suaNganh');
        Route::post('/xoanganh', 'App\Http\Controllers\DanhmucController@xoaNganh');

        Route::get('/loaihocphan', 'App\Http\Controllers\DanhmucController@loaihocphan');
        Route::post('/themloaihocphan', 'App\Http\Controllers\DanhmucController@themLoaiHocPhan');
        Route::post('/sualoaihocphan', 'App\Http\Controllers\DanhmucController@suaLoaiHocPhan');
        Route::post('/xoaloaihocphan', 'App\Http\Controllers\DanhmucController@xoaLoaiHocPhan');

        Route::get('/khoikienthuc', 'App\Http\Controllers\DanhmucController@khoiKienThuc');
        Route::post('/themkhoikienthuc', 'App\Http\Controllers\DanhmucController@themKhoiKienThuc');
        Route::post('/suakhoikienthuc', 'App\Http\Controllers\DanhmucController@suaKhoiKienThuc');
        Route::post('/xoakhoikienthuc', 'App\Http\Controllers\DanhmucController@xoaKhoiKienThuc');

        Route::get('/hockynienkhoa', 'App\Http\Controllers\DanhmucController@hocKyNienKhoa');
        Route::post('/themhockynienkhoa', 'App\Http\Controllers\DanhmucController@themHocKyNienKhoa');
        Route::post('/xoahockynienkhoa', 'App\Http\Controllers\DanhmucController@xoaHocKyNienKhoa');

        // chương trình đào tạo
        Route::get('/ctdaotao', 'App\Http\Controllers\CTdaotaoController@CTDaoTao');
        Route::get('/ctdt/{mact}', 'App\Http\Controllers\CTdaotaoController@chiTietChuongTrinh');
        Route::post('/themmonhocvaoctdt', 'App\Http\Controllers\CTdaotaoController@themMonHocCTDT');
        Route::post('/themctdaotao', 'App\Http\Controllers\CTdaotaoController@themCTDaoTao');
        Route::post('/suactdaotao', 'App\Http\Controllers\CTdaotaoController@suaCTDaoTao');
        Route::post('/xoactdaotao', 'App\Http\Controllers\CTdaotaoController@xoaCTDaoTao');
        Route::post('/xoamonhoctrongct', 'App\Http\Controllers\CTdaotaoController@xoaMonCTDT');

        Route::get('/monhoc', 'App\Http\Controllers\CTdaotaoController@monHoc');
        Route::get('/ttmon', 'App\Http\Controllers\CTdaotaoController@thongTinMonHoc');
        Route::post('/themmonhoc', 'App\Http\Controllers\CTdaotaoController@themMonHoc');
        Route::post('/importmonhoc', 'App\Http\Controllers\CTdaotaoController@importMonHoc');
        Route::post('/suamonhoc', 'App\Http\Controllers\CTdaotaoController@suaMonHoc');
        Route::post('/xoamonhoc', 'App\Http\Controllers\CTdaotaoController@xoaMonHoc');

        Route::get('/nhommon/{mact}', 'App\Http\Controllers\CTdaotaoController@nhomMonCT');
        Route::post('/themnhommon', 'App\Http\Controllers\CTdaotaoController@themNhomMon');
        // Route::post('/suanhommon', 'App\Http\Controllers\CTdaotaoController@suaNhomMon');
        // Route::post('/xoanhommon', 'App\Http\Controllers\CTdaotaoController@xoaNhomMon');

        // quản lý lớp
        Route::get('/lop', 'App\Http\Controllers\QuanlylopController@lop');
        Route::get('/dslop/{malop}', 'App\Http\Controllers\QuanlylopController@dsLop');
        Route::post('/themlop', 'App\Http\Controllers\QuanlylopController@themLop');
        Route::post('/sualop', 'App\Http\Controllers\QuanlylopController@suaLop');
        Route::post('/xoalop', 'App\Http\Controllers\QuanlylopController@xoaLop');

        Route::get('/sinhvien', 'App\Http\Controllers\QuanlylopController@sinhVien');
        Route::get('/ttsv', 'App\Http\Controllers\QuanlylopController@thongTinSinhVien');
        Route::post('/cntrangthai', 'App\Http\Controllers\QuanlylopController@capNhatTrangThai');
        Route::post('/cntrangthaitk', 'App\Http\Controllers\QuanlylopController@capNhatTrangThaiTK');
        Route::post('/doimatkhau', 'App\Http\Controllers\QuanlylopController@doiMatKhau');
        Route::post('/themsinhvien', 'App\Http\Controllers\QuanlylopController@themSinhVien');
        Route::post('/importsinhvien', 'App\Http\Controllers\QuanlylopController@importSinhVien');
        Route::post('/suasinhvien', 'App\Http\Controllers\QuanlylopController@suaSinhVien');
        Route::post('/xoasinhvien', 'App\Http\Controllers\QuanlylopController@xoaSinhVien');
        Route::post('/xoanhieusv', 'App\Http\Controllers\QuanlylopController@xoaNhieuSinhVien');

        Route::get('/giangvien', 'App\Http\Controllers\QuanlylopController@giangVien');
        Route::post('/themgiangvien', 'App\Http\Controllers\QuanlylopController@themGiangVien');
        Route::post('/suagiangvien', 'App\Http\Controllers\QuanlylopController@suaGiangVien');
        Route::get('/ttgv', 'App\Http\Controllers\QuanlylopController@thongTinGiangVien');
        Route::post('/xoagiangvien', 'App\Http\Controllers\QuanlylopController@xoaGiangVien');

        // điểm
        Route::get('/nhapdiem', 'App\Http\Controllers\QuanlydiemController@nhapDiem');
        // Route::post('/nhapdiemfile', 'App\Http\Controllers\QuanlydiemController@nhapDiemFile'); //Mẫu cũ
        Route::post('/nhapdiemfile', 'App\Http\Controllers\QuanlydiemController@nhapDiemNhieuSV');
        Route::post('/nhapdiem1sv', 'App\Http\Controllers\QuanlydiemController@nhapDiemSV');

        Route::get('/diemlop', 'App\Http\Controllers\QuanlydiemController@diemLop');
        Route::get('/xemdiemlop', 'App\Http\Controllers\QuanlydiemController@xemDiemLop');

        Route::get('/diemsv', 'App\Http\Controllers\QuanlydiemController@diemSinhVien');
        Route::get('/xemdiemsv', 'App\Http\Controllers\QuanlydiemController@xemDiemSV');
        Route::get('/slhocky', 'App\Http\Controllers\QuanlydiemController@slHocKy');

    });

});