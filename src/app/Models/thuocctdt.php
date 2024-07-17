<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class thuocctdt extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'thuoc_chuong_trinh_dao_tao';
    protected $primaryKey = ['ma_chuong_trinh', 'ma_mon_hoc', 'ma_loai_hoc_phan', 'ma_khoi_kien_thuc'];

    protected $fillable = [
      'ma_chuong_trinh',
      'ma_mon_hoc',
      'ma_loai_hoc_phan',
      'ma_khoi_kien_thuc',
      'thu_tu_hoc_ky',
    ];    

    public function chuongtrinhdaotao()
    {
      return $this->belongsTo(chuongtrinhdaotao::class, 'ma_chuong_trinh', 'ma_chuong_trinh'); 
    }

    public function monhoc()
    {
      return $this->belongsTo(monhoc::class, 'ma_mon_hoc', 'ma_mon_hoc'); 
    }  

    public function loaihocphan()
    {
      return $this->belongsTo(loaihocphan::class, 'ma_loai_hoc_phan', 'ma_loai_hoc_phan'); 
    } 

    public function khoikienthuc()
    {
      return $this->belongsTo(khoikienthuc::class, 'ma_khoi_kien_thuc', 'ma_khoi_kien_thuc'); 
    } 
}