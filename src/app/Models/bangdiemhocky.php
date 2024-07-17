<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bangdiemhocky extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'bang_diem_hoc_ky';
    protected $primaryKey = 'id';

    protected $fillable = [
      'id',
      'ma_sinh_vien',
      'ma_hoc_ky_nien_khoa',
      'diem_he_10',
      'diem_he_4',
      'trung_binh_hoc_ky',
      'trung_binh_nam_hoc',
      'trung_binh_tich_luy',
    ];

    public function sinhvien()
    {
      return $this->belongsTo(sinhvien::class, 'ma_sinh_vien', 'ma_sinh_vien'); 
    }

    public function hockynienkhoa()
    {
      return $this->belongsTo(hockynienkhoa::class, 'ma_hoc_ky_nien_khoa', 'ma_hoc_ky_nien_khoa'); 
    }
}
