<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nhommon extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'nhom_mon';
    protected $primaryKey = 'ma_nhom_mon';

    protected $fillable = [
      'ma_nhom_mon',
      'ten_nhom_mon',
    ];    

    public function thuocnhommon()
    {
      return $this->hasMany(thuocnhommon::class, 'ma_nhom_mon', 'ma_nhom_mon');
    }

}