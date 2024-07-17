<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class khoikienthuc extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'khoi_kien_thuc';
    protected $primaryKey = 'ma_khoi_kien_thuc';

    protected $fillable = [
      'ma_khoi_kien_thuc',
      'ten_khoi_kien_thuc',
    ];

    public function thuocctdt()
    {
      return $this->hasMany(thuocctdt::class, 'ma_khoi_kien_thuc', 'ma_khoi_kien_thuc');
    }
}
