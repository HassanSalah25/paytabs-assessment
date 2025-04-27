<?php

function config($key)
{
    static $settings = null;

    if ($settings === null) {
        $settings = require __DIR__ . '/../config/config.php';
    }

    return $settings[$key] ?? null;
}
