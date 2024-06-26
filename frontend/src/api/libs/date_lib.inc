<?php
function format_date_lite($timestamp) {
    $today = strtotime('today');
    $tomorrow = strtotime('tomorrow');

    // Русские названия месяцев
    $months = array(
        1 => 'Января',
        2 => 'Февраля',
        3 => 'Марта',
        4 => 'Апреля',
        5 => 'Мая',
        6 => 'Июня',
        7 => 'Июля',
        8 => 'Августа',
        9 => 'Сентября',
        10 => 'Октября',
        11 => 'Ноября',
        12 => 'Декабря'
    );

    if ($timestamp >= $today && $timestamp < $tomorrow) {
        return 'Сегодня';
    } elseif ($timestamp >= $tomorrow && $timestamp < ($tomorrow + 86400)) {
        return 'Завтра';
    } else {
        return date('j', $timestamp) . ' ' . $months[date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }
}