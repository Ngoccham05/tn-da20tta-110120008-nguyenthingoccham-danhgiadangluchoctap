<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bangdiemmonhoc extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'bang_diem_hoc_ky';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'ma_sinh_vien',
      'ma_mon_hoc',
      'ma_hoc_ky_nien_khoa',
      'diem_lan_1',
      'diem_lan_2',
      'diem_he_4',
      'diem_chu',
    ];

    public function sinhvien()
    {
      return $this->belongsTo(sinhvien::class, 'ma_sinh_vien', 'ma_sinh_vien'); 
    }

    public function monhoc()
    {
      return $this->belongsTo(monhoc::class, 'ma_mon_hoc', 'ma_mon_hoc'); 
    }

    public function hockynienkhoa()
    {
      return $this->belongsTo(hockynienkhoa::class, 'ma_hoc_ky_nien_khoa', 'ma_hoc_ky_nien_khoa'); 
    }
}
