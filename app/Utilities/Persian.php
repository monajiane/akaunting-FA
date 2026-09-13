<?php

namespace App\Utilities;

/**
 * Helper utilities for Iranian (fa-IR) localization:
 *  - Convert Arabic/Indic/Latin digits to Persian digits
 *  - Convert Gregorian DateTime to Jalali (Shamsi) date
 *
 * Conversions are implemented natively so the package requires no extra
 * Composer dependencies.
 */
class Persian
{
    /**
     * Convert any digit sequence inside a string to Persian (Farsi) digits.
     * Also normalizes Arabic-Yeh / Arabic-Kaf to their Persian equivalents.
     */
    public static function digits($value): string
    {
        $value = (string) $value;

        // Latin digits -> Persian
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $value = str_replace($latin, $persian, $value);

        // Arabic-Indic (Eastern Arabic) digits -> Persian
        $arabicIndic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $value = str_replace($arabicIndic, $persian, $value);

        // N’Ko / other indic forms that sometimes appear (٠-٩ already covered)
        return $value;
    }

    /**
     * Convert a Gregorian date (Y-m-d or timestamp) to Jalali (Shamsi).
     * Algorithm based on Kazimierz M. Borkowski's implementation.
     */
    public static function toJalali(int $gy, int $gm, int $gd): array
    {
        $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $jy = ($gy <= 1600) ? 0 : 979;
        $gy -= ($gy <= 1600) ? 621 : 1600;
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 9) / 100))
              + ((int)(($gy2 + 369) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
        $jy += 33 * ((int)($days / 12053));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $jm = ($days < 186) ? 1 + (int)($days / 31) : 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
        return [$jy, $jm, $jd];
    }

    /**
     * Convert Jalali (Shamsi) back to Gregorian.
     */
    public static function toGregorian(int $jy, int $jm, int $jd): array
    {
        $gy = ($jy <= 979) ? 621 : 1600;
        $jy -= ($jy <= 979) ? 0 : 979;
        $days = (365 * $jy) + ((int)($jy / 33)) * 8 + ((int)((($jy % 33) + 3) / 4))
              + 78 + $jd + (($jm < 7) ? ($jm - 1) * 31 : 177 + ($jm - 7) * 30);
        $gy += 400 * ((int)($days / 146097));
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * ((int)(--$days / 36524));
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            }
        }
        $gy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $gy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;
        foreach ([0, 31, (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0)) ? 29 : 28,
                 31, 30, 31, 30, 31, 31, 30, 31, 30, 31] as $gm => $daysInMonth) {
            if ($gd <= $daysInMonth) {
                break;
            }
            $gd -= $daysInMonth;
        }
        return [$gy, $gm, $gd];
    }

    /**
     * Format a Gregorian date as Jalali (e.g. ۱۴۰۳/۰۶/۲۳).
     */
    public static function jdate(string $format = 'Y/m/d', $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        } elseif (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        [$jy, $jm, $jd] = self::toJalali(date('Y', $timestamp), (int)date('m', $timestamp), (int)date('j', $timestamp));

        $map = [
            'Y' => sprintf('%04d', $jy),
            'y' => sprintf('%02d', $jy % 100),
            'm' => sprintf('%02d', $jm),
            'n' => (string)$jm,
            'd' => sprintf('%02d', $jd),
            'j' => (string)$jd,
            'F' => self::persianMonthName($jm),
            'M' => mb_substr(self::persianMonthName($jm), 0, 3),
            'H' => date('H', $timestamp),
            'i' => date('i', $timestamp),
            's' => date('s', $timestamp),
            'A' => 'بعد از ظهر',
            'a' => 'ب.ظ',
        ];
        if ((int)date('H', $timestamp) < 12) {
            $map['A'] = 'قبل از ظهر';
            $map['a'] = 'ق.ظ';
        }

        $out = '';
        for ($i = 0; $i < strlen($format); $i++) {
            $c = $format[$i];
            $out .= $map[$c] ?? $c;
        }

        return self::digits($out);
    }

    public static function persianMonthName(int $m): string
    {
        $months = [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
            4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
            7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
            10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
        ];
        return $months[$m] ?? '';
    }
}
