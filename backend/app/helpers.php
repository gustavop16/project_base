<?php

use Illuminate\Support\Carbon;

// Polyfill para array_all() — disponível nativamente apenas no PHP 8.4+
if (!function_exists('array_all')) {
    function array_all(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('primeiraMaiuscula')) {
    function primeiraMaiuscula($string) {
        return ucfirst(strtolower($string));
    }
}

if (!function_exists('getDayWeek')) {
    function getDayWeek($date) {
       
    }
}

if (!function_exists('addMonthDate')) {
    function addMonthDate($date, $month) {
       return  Carbon::parse($date)->addMonths($month)->format('Y-m-d');
    }
}

if (!function_exists('addDayDate')) {
    function addDayDate($date, $days) {
       return  Carbon::parse($date)->addDays($days)->format('Y-m-d');
    }
}

if (!function_exists('subDayDate')) {
    function subDayDate($date, $days) {
       return  Carbon::parse($date)->subDays($days)->format('Y-m-d');
    }
}


if (!function_exists('currentDay')) {
    function currentDay() {
       return  Carbon::now()->format('Y-m-d');
    }
}

if (!function_exists('getDateFormatBR')) {
    function getDateFormatBR($date) {
       if(empty($date)) return null;
       return  Carbon::parse($date)->format('d/m/Y');
    }
}

if (!function_exists('dateFormatDB')) {
    function dateFormatDB($date) {
       if(empty($date)) return null;
       $date = Carbon::createFromFormat('d/m/Y', $date);
       return  $date->format('Y-m-d');
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney($amount) {
       if(empty($amount)) return null;
       return 'R$ ' . number_format($amount, 2, ',', '.');
    }
}

if (!function_exists('formatMoneyToDB')) {
    function formatMoneyToDB($amount) {
       if(empty($amount)) return null;
        // Remove o símbolo de R$ e os pontos de milhar
        $amount = str_replace(['.', ' '], '', $amount);
        // Substitui a vírgula decimal por um ponto decimal
        $amount = str_replace(',', '.', $amount);
        // Converte a string para float
        $amount = (float)$amount;
        return $amount;
    }
}

if (!function_exists('formatDecimalToDB')) {
    function formatDecimalToDB($value) {
       if(empty($value)) return null;
        // Substitui a vírgula decimal por um ponto decimal
        $value = str_replace(',', '.', $value);
        // Converte a string para float
        $value = (float)$value;
        return $value;
    }
}

if (!function_exists('formatDecimal')) {
    function formatDecimal($value) {
       if(empty($value)) return null;
        // Substitui ponto decimal por vírgula decimal 
        $value = str_replace('.', ',', $value);
    
        return $value;
    }
}
