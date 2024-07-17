<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class giangvien extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'giang_vien';
    protected $primaryKey = 'ma_giang_vien';

    protected $fillable = [
      'ma_giang_vien',
      'ho_ten',
      'gioi_tinh',
      'ngay_sinh',
      'dia_chi',
      'so_dien_thoai',
      'email',
    ];    
    
    public function quanlylop()
    {
      return $this->hasMany(quanlylop::class, 'ma_giang_vien', 'ma_giang_vien');
    }

    public function taikhoangiangvien()
    {
      return $this->hasOne(taikhoangiangvien::class, 'ma_giang_vien', 'ma_giang_vien');
    }
}
