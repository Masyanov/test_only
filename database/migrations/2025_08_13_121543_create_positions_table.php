<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create( 'positions', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name' );
            $table->timestamps();
        } );

        Schema::table( 'users', function ( Blueprint $table ) {
            $table->foreignId( 'position_id' )->nullable()->constrained( 'positions' );
        } );
    }
    public function down(): void {
        Schema::table( 'users', function ( Blueprint $table ) {
            $table->dropForeign( [ 'position_id' ] );
            $table->dropColumn( 'position_id' );
        } );
        Schema::dropIfExists( 'positions' );
    }
};
