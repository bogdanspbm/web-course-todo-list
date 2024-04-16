<?php

function generateUID() {
    $timestamp = base_convert(strval(microtime(true)), 10, 36);
    $randomPart = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 13);
    return $timestamp . '-' . $randomPart;
}