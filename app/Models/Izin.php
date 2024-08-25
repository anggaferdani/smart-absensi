<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;
    
    protected $table = 'izins';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
