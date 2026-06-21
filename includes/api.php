<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {

    register_rest_route(
        'rm/v1',
        '/heartbeat',
        [
            'methods' => 'POST',
            'callback' => 'rm_api_heartbeat',
            'permission_callback' => '__return_true'
        ]
    );

    register_rest_route(
        'rm/v1',
        '/page-time',
        [
            'methods' => 'POST',
            'callback' => 'rm_api_page_time',
            'permission_callback' => '__return_true'
        ]
    );

    register_rest_route(
        'rm/v1',
        '/event',
        [
            'methods' => 'POST',
            'callback' => 'rm_api_event',
            'permission_callback' => '__return_true'
        ]
    );

});

function rm_api_heartbeat()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $session_id =
        rm_get_session_id();

    $wpdb->update(
        $table,
        [
            'last_activity' =>
                current_time(
                    'mysql'
                )
        ],
        [
            'session_id' =>
                $session_id
        ]
    );

    return [
        'success' => true
    ];
}

function rm_api_page_time(
    WP_REST_Request $request
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_time';

    $wpdb->insert(
        $table,
        [
            'session_id' =>
                rm_get_session_id(),

            'pagina' =>
                sanitize_text_field(
                    $request['page']
                ),

            'segundos' =>
                intval(
                    $request['seconds']
                ),

            'fecha' =>
                current_time(
                    'mysql'
                )
        ]
    );

    return [
        'success' => true
    ];
}

function rm_api_event(
    WP_REST_Request $request
)
{
    rm_track_event(
        rm_get_session_id(),
        sanitize_text_field(
            $request['type']
        ),
        sanitize_text_field(
            $request['resource']
        )
    );

    return [
        'success' => true
    ];
}