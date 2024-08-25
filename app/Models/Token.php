<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $table = 'tokens';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function lokasi() {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function absens() {
        return $this->hasMany(Absen::class);
    }
}
