<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class khoa extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table  = 'khoa';
    protected $primaryKey = 'ma_khoa';

    protected $fillable = [
      'ma_khoa',
      'ten_khoa',
    ];

    public function bomon()
    {
      return $this->hasMany(bomon::class, 'ma_khoa', 'ma_khoa');
    }
}
