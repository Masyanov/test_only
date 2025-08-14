<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model {
    use HasFactory;

    protected $fillable = [ 'full_name' ];

    public function cars() {
        return $this->hasMany( Car::class );
    }
}
