<?php

namespace IsmayilDev\EpochSoftDelete;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EpochCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Carbon
    {
        if (is_null($value) || $value === 0) {
            return null;
        }

        return Carbon::parse($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if ($value === null || $value === 0) {
            return 0;
        }

        return Carbon::parse($value)->timestamp;
    }
}
