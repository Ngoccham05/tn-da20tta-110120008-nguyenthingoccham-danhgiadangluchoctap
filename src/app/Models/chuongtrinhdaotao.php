<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chuongtrinhdaotao extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'chuong_trinh_dao_tao';
    protected $primaryKey = 'ma_chuong_trinh';

    protected $fillable = [
      'ma_chuong_trinh',
      'ten_chuong_trinh',
      'so_quyet_dinh',
      'ma_nganh',
    ];

    public function lop()
    {
      return $this->hasMany(lop::class, 'ma_chuong_trinh');
    }

    public function thuocctdt()
    {
      return $this->hasMany(thuocctdt::class, 'ma_chuong_trinh');
    }

    public function nganh()
    {
      return $this->belongsTo(nganh::class, 'ma_nganh', 'ma_nganh'); 
    }
    
}
