<?php
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class JalaliToCarbon implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
      
        return $value ? Carbon::parse($value) : null;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value) return null;

        try {
            return Jalalian::fromFormat('Y/m/d H:i:s', $value)->toCarbon();
        } catch (\Exception $e) {
            try {
                return Jalalian::fromFormat('Y/m/d H:i', $value)->toCarbon();
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
