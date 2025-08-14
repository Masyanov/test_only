<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create( 'trips', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( 'user_id' )->constrained( 'users' );
            $table->foreignId( 'car_id' )->constrained( 'cars' );
            $table->timestamp( 'start_time' );
            $table->timestamp( 'end_time' );
            $table->timestamps();
        } );
    }
    public function down(): void {
        Schema::dropIfExists( 'trips' );
    }
};
