<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model {
    use HasFactory;

    protected $fillable = [ 'model', 'car_category_id', 'driver_id' ];

    public function carCategory() {
        return $this->belongsTo( CarCategory::class );
    }

    public function driver() {
        return $this->belongsTo( Driver::class );
    }

    public function trips() {
        return $this->hasMany( Trip::class );
    }
}
