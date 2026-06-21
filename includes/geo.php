<?php

if (!defined('ABSPATH')) {
    exit;
}

use GeoIp2\Database\Reader;

function rm_get_geo_data()
{
    try {

        $reader = new Reader(
            RM_PATH . 'geo/GeoLite2-City.mmdb'
        );

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        $record = $reader->city($ip);

        return [
            'pais' => $record->country->name ?? null,
            'ciudad' => $record->city->name ?? null
        ];

    } catch (Exception $e) {

        return [
            'pais' => null,
            'ciudad' => null
        ];
    }
}