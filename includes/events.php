<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_track_event(
    $session_id,
    $tipo,
    $recurso = null
) {
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_events';

    $wpdb->insert(
        $table,
        [
            'session_id' =>
                $session_id,

            'tipo' =>
                sanitize_text_field(
                    $tipo
                ),

            'recurso' =>
                $recurso
                    ? sanitize_text_field(
                        $recurso
                    )
                    : null,

            'fecha' =>
                current_time(
                    'mysql'
                )
        ]
    );
}