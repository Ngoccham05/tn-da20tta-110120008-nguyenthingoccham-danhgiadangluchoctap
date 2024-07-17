<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loaihocphan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'loai_hoc_phan';
    protected $primaryKey = 'ma_loai_hoc_phan';

    protected $fillable = [
      'ma_loai_hoc_phan',
      'ten_loai_hoc_phan',
    ];

    public function thuocctdt()
    {
      return $this->hasMany(thuocctdt::class, 'ma_loai_hoc_phan', 'ma_loai_hoc_phan');
    }
}
