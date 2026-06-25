<?php

namespace App\Helpers;

use App\Models\CustomHoliday;
use Illuminate\Support\Facades\Cache;

class HolidayHelper
{
    public static function getFromApi(int $year): array
    {
        return Cache::remember("holidays_api_{$year}", 86400, function () use ($year) {
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

    public static function syncFromApi(int $year): int
    {
        $apiHolidays = static::getFromApi($year);
        $count = 0;
        foreach ($apiHolidays as $date => $name) {
            $existing = CustomHoliday::where('date', $date)->first();
            if (!$existing) {
                CustomHoliday::create([
                    'date' => $date,
                    'name' => $name,
                    'source' => 'api',
                    'is_active' => true,
                ]);
                $count++;
            } elseif ($existing->source === 'api') {
                $existing->update(['name' => $name]);
            }
        }
        return $count;
    }

    public static function get(int $year): array
    {
        $holidays = [];
        $customs = CustomHoliday::whereYear('date', $year)
            ->where('is_active', true)
            ->orderBy('date')
            ->get();

        if ($customs->isEmpty()) {
            static::syncFromApi($year);
            $customs = CustomHoliday::whereYear('date', $year)
                ->where('is_active', true)
                ->orderBy('date')
                ->get();
        }

        foreach ($customs as $h) {
            $holidays[$h->date->format('Y-m-d')] = $h->name;
        }

        return $holidays;
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
