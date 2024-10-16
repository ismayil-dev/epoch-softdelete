<?php

namespace IsmayilDev\EpochSoftDelete;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Carbon;

class EpochCast implements Castable
{
    /**
     * @return CastsAttributes<\Carbon\Carbon, int>
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                if (is_null($value) || $value === 0) {
                    return null;
                }

                return Carbon::parse($value);
            }

            public function set($model, $key, $value, $attributes)
            {
                if ($value === null || $value === 0) {
                    return 0;
                }

                return Carbon::parse($value)->timestamp;
            }
        };
    }
}
