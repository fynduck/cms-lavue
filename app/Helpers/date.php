<?php

use Illuminate\Support\Carbon;

/**
 * @param $date
 * @param string $format
 * @return string
 */
function parseShow($date, $format = 'd.m.Y')
{
    return Carbon::parse($date, config('app.timezone'))->format($format);
}

function diff($date, $diff = 'days')
{
    switch ($diff) {
        case 'minutes':
            return now()->diffInMinutes(Carbon::parse($date));
            break;
        case 'hours':
            return now()->diffInHours(Carbon::parse($date));
            break;
        case 'days':
            return now()->diffInDays(Carbon::parse($date));
            break;
        case 'months':
            return now()->diffInMonths(Carbon::parse($date));
            break;
        default:
            return $date;
    }
}

function localeDate($date, $format = '%d %h %G')
{
    Carbon::setLocale('ru');

    return Carbon::parse($date)->formatLocalized($format);
}

function listOfMonth()
{
    $months = [];
    $startDate = new Carbon('first day of January 2019');
    for ($month = 1; $month <= 12; $month++) {
        $months[$startDate->month] = $startDate->monthName;
        $startDate->addMonth();
    }

    return $months;
}

function listYears($startYear = null, $endYear = null)
{
    if (is_null($startYear)) {
        $startYear = now()->subYears(18)->year;
    }

    if (is_null($endYear)) {
        $endYear = now()->subYears(100)->year;
    }

    return range($startYear, $endYear);
}
