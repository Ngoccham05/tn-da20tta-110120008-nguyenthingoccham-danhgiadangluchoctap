<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class thuocnhommon extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'thuoc_nhom_mon';
    protected $primaryKey = ['ma_nhom_mon', 'ma_mon_hoc'];

    protected $fillable = [
      'ma_nhom_mon',
      'ma_mon_hoc',
    ];    

    public function monhoc()
    {
      return $this->belongsTo(monhoc::class, 'ma_mon_hoc', 'ma_mon_hoc'); 
    } 

    public function nhommon()
    {
      return $this->belongsTo(nhommon::class, 'ma_nhom_mon', 'ma_nhom_mon'); 
    }
}