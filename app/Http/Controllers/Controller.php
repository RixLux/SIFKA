<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    /**
     * Build a database-specific spatial point value.
     */
    protected function spatialPoint(float $longitude, float $latitude): ?Expression
    {
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return DB::raw("ST_GeomFromText('POINT({$longitude} {$latitude})', 4326)");
        }

        return null;
    }

    /**
     * Build coordinate attributes that match the active database schema.
     *
     * @return array<string, mixed>
     */
    protected function coordinateAttributes(float $longitude, float $latitude): array
    {
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return [
                'location' => $this->spatialPoint($longitude, $latitude),
            ];
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
}
