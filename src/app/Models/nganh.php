<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nganh extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'nganh';
    protected $primaryKey = 'ma_nganh';

    protected $fillable = [
      'ma_nganh',
      'ten_nganh',
      'ma_bo_mon',
    ];

    public function chuongtrinhdaotao()
    {
      return $this->hasMany(chuongtrinhdaotao::class, 'ma_nganh', 'ma_nganh');
    }
    
    public function bomon()
    {
      return $this->belongsTo(bomon::class, 'ma_bo_mon', 'ma_bo_mon'); 
    }  
}
