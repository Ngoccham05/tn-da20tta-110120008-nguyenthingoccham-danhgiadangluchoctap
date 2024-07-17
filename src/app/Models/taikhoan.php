<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;   

class taikhoan extends Authenticatable
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'tai_khoan';
    protected $primaryKey = 'ten_dang_nhap';
    public $incrementing = false; // tắt kiểu số nguyên ???

    protected $fillable = [
      'ten_dang_nhap',
      'mat_khau',
      'quyen_truy_cap',
      'trang_thai',
    ];
    
    public function taikhoansinhvien()
    {
      return $this->hasOne(taikhoansinhvien::class, 'ten_dang_nhap', 'ten_dang_nhap');
    }

    public function taikhoangiangvien()
    {
      return $this->hasOne(taikhoangiangvien::class, 'ten_dang_nhap', 'ten_dang_nhap');
    }
}