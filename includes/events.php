<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_save_event(
    $session_id,
    $tipo,
    $recurso = null
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_events';

    $wpdb->insert(
        $table,
        [
            'session_id' => $session_id,
            'tipo' => $tipo,
            'recurso' => $recurso,
            'fecha' => current_time('mysql')
        ]
    );
}