<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_get_active_visitors_count()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $config =
        rm_get_config();

    $minutes =
        intval(
            $config['active_minutes']
        );

    return (int)
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM $table
                WHERE last_activity >=
                DATE_SUB(
                    %s,
                    INTERVAL %d MINUTE
                )
                ",
                current_time('mysql'),
                $minutes
            )
        );
}

function rm_get_active_countries()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $config =
        rm_get_config();

    $minutes =
        intval(
            $config['active_minutes']
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                pais,
                COUNT(*) total
            FROM $table
            WHERE
                last_activity >=
                DATE_SUB(
                    %s,
                    INTERVAL %d MINUTE
                )
                AND pais IS NOT NULL
                AND pais != ''
            GROUP BY pais
            ORDER BY total DESC
            ",
            current_time('mysql'),
            $minutes
        )
    );
}

function rm_get_active_devices()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $config =
        rm_get_config();

    $minutes =
        intval(
            $config['active_minutes']
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                dispositivo,
                COUNT(*) total
            FROM $table
            WHERE
                last_activity >=
                DATE_SUB(
                    %s,
                    INTERVAL %d MINUTE
                )
                AND dispositivo IS NOT NULL
                AND dispositivo != ''
            GROUP BY dispositivo
            ORDER BY total DESC
            ",
            current_time('mysql'),
            $minutes
        )
    );
}