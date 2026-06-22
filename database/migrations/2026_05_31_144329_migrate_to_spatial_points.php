<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // 1. Add 'location' column to all tables
        Schema::table('buildings', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->after('description')->nullable();
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->after('description')->nullable();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->after('description')->nullable();
        });

        // 2. Migrate data
        DB::statement("UPDATE buildings SET location = ST_GeomFromText(CONCAT('POINT(', longitude, ' ', latitude, ')'), 4326)");
        DB::statement("UPDATE facilities SET location = ST_GeomFromText(CONCAT('POINT(', longitude, ' ', latitude, ')'), 4326)");
        DB::statement("UPDATE reports SET location = ST_GeomFromText(CONCAT('POINT(', long_report, ' ', lat_report, ')'), 4326)");

        // 3. Make 'location' NOT NULL and add spatial index, then drop old columns
        Schema::table('buildings', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->nullable(false)->change();
            $table->spatialIndex('location');
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->nullable(false)->change();
            $table->spatialIndex('location');
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->geometry('location', 'point', 4326)->nullable(false)->change();
            $table->spatialIndex('location');
            $table->dropColumn(['lat_report', 'long_report']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('buildings', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->after('description')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->after('description')->nullable();
            $table->decimal('longitude', 11, 8)->after('latitude')->nullable();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->decimal('lat_report', 10, 8)->after('description')->nullable();
            $table->decimal('long_report', 11, 8)->after('lat_report')->nullable();
        });

        DB::statement('UPDATE buildings SET latitude = ST_Y(location), longitude = ST_X(location)');
        DB::statement('UPDATE facilities SET latitude = ST_Y(location), longitude = ST_X(location)');
        DB::statement('UPDATE reports SET lat_report = ST_Y(location), long_report = ST_X(location)');

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
            $table->dropColumn('location');
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
            $table->dropColumn('location');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']);
            $table->dropColumn('location');
        });
    }
};
