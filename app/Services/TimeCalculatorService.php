<?php

namespace App\Services;

use DateTime;

class TimeCalculatorService
{
    public static function getDiffMinute(string $start, string $end): int
    {
        // 入力チェック（H:i形式）
        if (
            !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $start) ||
            !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $end)
        ) {
            return 0;
        }

        $startTime = DateTime::createFromFormat('H:i', $start);
        $endTime = DateTime::createFromFormat('H:i', $end);

        if (!$startTime || !$endTime) {
            return 0;
        }

        // 同日か翌日か判定
        $startMinutes = (int) $startTime->format('H') * 60 + (int) $startTime->format('i');
        $endMinutes = (int) $endTime->format('H') * 60 + (int) $endTime->format('i');

        if ($endMinutes >= $startMinutes) {
            return $endMinutes - $startMinutes;
        } else {
            // 翌日まで跨ぐ場合（24時間分加算）
            return ($endMinutes + 1440) - $startMinutes;
        }
    }
}
