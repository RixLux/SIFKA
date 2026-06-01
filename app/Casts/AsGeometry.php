<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class AsGeometry implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value) {
            return null;
        }

        // MySQL/MariaDB spatial data is prefixed with a 4-byte SRID
        // Followed by WKB (Well-Known Binary)
        // WKB for Point: Byte order (1), Type (4), X (8), Y (8) = 21 bytes
        // Total: 25 bytes

        // If it's a string and has 25 bytes, it's likely a binary POINT
        if (is_string($value) && strlen($value) >= 21) {
            // Check if it has SRID prefix (4 bytes)
            $hasSrid = strlen($value) == 25;
            $data = $hasSrid ? substr($value, 4) : $value;

            $res = unpack('Corder/Ltype/dlng/dlat', $data);

            if ($res) {
                return (object) [
                    'latitude' => $res['lat'],
                    'longitude' => $res['lng'],
                ];
            }
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof Expression) {
            return $value;
        }

        if (is_array($value) && isset($value['lat'], $value['lng'])) {
            return DB::raw("ST_GeomFromText('POINT({$value['lng']} {$value['lat']})', 4326)");
        }

        // If it's an object with latitude/longitude
        if (is_object($value) && isset($value->latitude, $value->longitude)) {
            return DB::raw("ST_GeomFromText('POINT({$value->longitude} {$value->latitude})', 4326)");
        }

        return $value;
    }
}
