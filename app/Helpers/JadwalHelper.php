<?php

namespace App\Helpers;

use App\Models\JamKerja;
use Illuminate\Support\Facades\Cache;

class JadwalHelper
{
    protected static ?array $jamKerjaCache = null;

    public static function getJamKerja(string $hari): ?JamKerja
    {
        if (static::$jamKerjaCache === null) {
            static::$jamKerjaCache = JamKerja::all()->keyBy('hari')->toArray();
        }
        $data = static::$jamKerjaCache[$hari] ?? null;
        if (!$data) return null;
        $model = new JamKerja();
        $model->forceFill($data);
        return $model;
    }
}
