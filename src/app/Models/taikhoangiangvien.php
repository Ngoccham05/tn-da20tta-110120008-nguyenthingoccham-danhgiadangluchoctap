<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class taikhoangiangvien extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'tai_khoan_giang_vien';
    protected $primaryKey = ['ten_dang_nhap', 'ma_giang_vien'];

    protected $fillable = [
      'ten_dang_nhap',
      'ma_giang_vien',
    ];

    public function taikhoan()
    {
      return $this->belongsTo(taikhoan::class, 'ten_dang_nhap', 'ten_dang_nhap'); 
    }

    public function giangvien()
    {
      return $this->belongsTo(giangvien::class, 'ma_giang_vien', 'ma_giang_vien'); 
    }    
}
