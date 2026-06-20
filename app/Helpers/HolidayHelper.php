<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class HolidayHelper
{
    public static function get(int $year): array
    {
        return Cache::remember("holidays_{$year}", 86400, function () use ($year) {
            try {
                $json = @file_get_contents("https://libur.deno.dev/api?year={$year}");
                if (!$json) return [];
                $data = @json_decode($json, true);
                if (!is_array($data)) return [];
                $holidays = [];
                foreach ($data as $item) {
                    if (!empty($item['date'])) {
                        $holidays[$item['date']] = $item['name'] ?? 'Libur Nasional';
                    }
                }
                return $holidays;
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public static function getDates(int $year): array
    {
        return array_keys(static::get($year));
    }

    public static function isHoliday(string $date): bool
    {
        $year = (int) substr($date, 0, 4);
        return isset(static::get($year)[$date]);
    }

    public static function getName(string $date): ?string
    {
        $year = (int) substr($date, 0, 4);
        return static::get($year)[$date] ?? null;
    }
}
