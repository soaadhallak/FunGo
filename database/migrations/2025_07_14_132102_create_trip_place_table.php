<?php

use App\Models\Place;
use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trip_place', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Trip::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Place::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_place');
    }
};
