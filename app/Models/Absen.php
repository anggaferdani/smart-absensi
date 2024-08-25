<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;

    protected $table = 'absens';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function token() {
        return $this->belongsTo(Token::class, 'token_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
