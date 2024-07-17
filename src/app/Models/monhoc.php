<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class monhoc extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'mon_hoc';
    protected $primaryKey = 'ma_mon_hoc';

    protected $fillable = [
      'ma_mon_hoc',
      'ten_mon_hoc',
      'so_tin_chi',
    ];

    public function thuocctdt()
    {
      return $this->hasMany(thuocctdt::class, 'ma_mon_hoc', 'ma_mon_hoc');
    }

    public function thuocnhommon()
    {
      return $this->hasMany(thuocnhommon::class, 'ma_mon_hoc', 'ma_mon_hoc');
    }

    public function bangdiemmonhoc()
    {
      return $this->hasMany(bangdiemmonhoc::class, 'ma_mon_hoc', 'ma_mon_hoc');
    }
}
