<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quanlylop extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'quan_ly_lop';
    protected $primaryKey = ['ma_lop', 'ma_giang_vien'];

    protected $fillable = [
      'ma_lop',
      'ma_giang_vien',
      'trang_thai',
    ];    

    public function lop()
    {
      return $this->belongsTo(lóp::class, 'ma_lop', 'ma_lop'); 
    }

    public function giangvien()
    {
      return $this->belongsTo(giangvien::class, 'ma_giang_vien', 'ma_giang_vien'); 
    }
}