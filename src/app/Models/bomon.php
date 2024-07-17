<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bomon extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'bo_mon';
    protected $primaryKey = 'ma_bo_mon';

    protected $fillable = [
      'ma_bo_mon',
      'ten_bo_mon',
      'ma_khoa',
    ];

    public function nganh()
    {
      return $this->hasMany(nganh::class, 'ma_bo_mon');
    }

    public function khoa()
    {
      return $this->belongsTo(khoa::class, 'ma_khoa', 'ma_khoa'); 
    }
}
