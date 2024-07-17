<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hockynienkhoa extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'hoc_ky_nien_khoa';
    protected $primaryKey = 'ma_hoc_ky_nien_khoa';

    protected $fillable = [
      'ma_hoc_ky_nien_khoa',
      'ten_hoc_ky_nien_khoa',
    ];

    public function bangdiemhocky()
    {
      return $this->hasMany(bangdiemhocky::class, 'ma_hoc_ky_nien_khoa', 'ma_hoc_ky_nien_khoa');
    }

    public function bangdiemmonhoc()
    {
      return $this->hasMany(bangdiemmonhoc::class, 'ma_hoc_ky_nien_khoa', 'ma_hoc_ky_nien_khoa');
    }
}
