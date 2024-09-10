<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasis';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function tokens() {
        return $this->hasMany(Token::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
