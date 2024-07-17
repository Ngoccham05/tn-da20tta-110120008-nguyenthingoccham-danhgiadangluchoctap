<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class taikhoansinhvien extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'tai_khoan_sinh_vien';
    protected $primaryKey = ['ten_dang_nhap', 'ma_sinh_vien'];

    protected $fillable = [
      'ten_dang_nhap',
      'ma_sinh_vien',
    ];

    public function taikhoan()
    {
      return $this->belongsTo(taikhoan::class, 'ten_dang_nhap', 'ten_dang_nhap'); 
    }

    public function sinhvien()
    {
      return $this->belongsTo(sinhvien::class, 'ma_sinh_vien', 'ma_sinh_vien'); 
    }  
}
