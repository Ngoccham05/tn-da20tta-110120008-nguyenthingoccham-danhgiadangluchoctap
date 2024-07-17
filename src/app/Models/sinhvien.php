<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sinhvien extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'sinh_vien';
    protected $primaryKey = 'ma_sinh_vien';

    protected $fillable = [
      'ma_sinh_vien',
      'ho_ten',
      'gioi_tinh',
      'ngay_sinh',
      'dia_chi',
      'so_dien_thoai',
      'email',
      'ma_lop',
    ];
    
    public function bangdiemmonhoc()
    {
      return $this->hasMany(bangdiemmonhoc::class, 'ma_sinh_vien', 'ma_sinh_vien');
    }

    public function bangdiemhocky()
    {
      return $this->hasMany(bangdiemhocky::class, 'ma_sinh_vien', 'ma_sinh_vien');
    }

    public function taikhoansinhvien()
    {
      return $this->hasOne(taikhoansinhvien::class, 'ma_sinh_vien', 'ma_sinh_vien');
    }

    public function lop()
    {
      return $this->belongsTo(lop::class, 'ma_lop', 'ma_lop'); 
    }
}
