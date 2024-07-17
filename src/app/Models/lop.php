<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lop extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'lop';
    protected $primaryKey = 'ma_lop';

    protected $fillable = [
      'ma_lop',
      'ten_lop',
      'ma_chuong_trinh',
    ];

    public function sinhvien()
    {
      return $this->hasMany(sinhvien::class, 'ma_lop', 'ma_lop');
    }

    public function quanlylop()
    {
      return $this->hasMany(quanlylop::class, 'ma_lop', 'ma_lop');
    }

    public function chuongtrinhdaotao()
    {
      return $this->belongsTo(chuongtrinhdaotao::class, 'ma_chuong_trinh', 'ma_chuong_trinh'); 
    }
}
