<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model {
    use HasFactory;

    public function user() {
        return $this->belongsTo( \App\Models\User::class );
    }

    protected $fillable = [ 'user_id', 'car_id', 'start_time', 'end_time' ];
}
