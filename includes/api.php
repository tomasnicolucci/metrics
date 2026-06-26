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

    register_rest_route(
        'rm/v1',
        '/active-users',
        [
            'methods' => 'GET',
            'callback' => 'rm_api_active_users',
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ]
    );

    register_rest_route(
        'rm/v1',
        '/dashboard-realtime',
        [
            'methods' => 'GET',
            'callback' => 'rm_api_dashboard_realtime',
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ]
    );

    register_rest_route(
        'rm/v1',
        '/dashboard-data',
        [
            'methods' => 'GET',
            'callback' => 'rm_api_dashboard_data',
            'permission_callback' => '__return_true'
            // function () {
            //     return
            //         is_user_logged_in()
            //         &&
            //         (
            //             current_user_can(
            //                 'administrator'
            //             )
            //             ||
            //             current_user_can(
            //                 'cliente_metricas'
            //             )
            //         );
            // }
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

    $page =
        sanitize_text_field(
            $request['page']
        );

    if (
        rm_is_internal_page(
            $page
        )
    ) {

        return [
            'success' => true
        ];

    }

    $wpdb->insert(
        $table,
        [
            'session_id' =>
                rm_get_session_id(),

            'pagina' =>
                $page,

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

function rm_api_active_users()
{
    return [
        'total' =>
            rm_get_active_visitors_count()
    ];
}

function rm_api_dashboard_realtime()
{
    $summary =
        rm_get_summary('today');

    return [

        'active_users' =>
            $summary[
                'visitantes_activos'
            ],

        'today_visits' =>
            $summary[
                'visitas_hoy'
            ],

        'countries' =>
            rm_get_active_countries()
    ];
}

function rm_api_dashboard_data(
    WP_REST_Request $request
)
{
    $period =
        sanitize_text_field(
            $request['period']
        );

    $pages =
        rm_get_top_pages(
            $period
        );

    foreach ($pages as $page) {

        $page->label =
            rm_get_page_label(
                $page->pagina
            );
    }

    $avg_page_time =
        rm_get_avg_page_time(
            $period
        );

    foreach (
        $avg_page_time
        as $page
    ) {

        $page->label =
            rm_get_page_label(
                $page->pagina
            );
    }

    $resources =
        rm_get_top_resources(
            $period
        );

    foreach (
        $resources as $resource
    ) {

        $resource->label =
            rm_get_resource_label(
                $resource->recurso
            );
    }

    $sources =
        rm_get_sources(
            $period
        );

    foreach (
        $sources as $source
    ) {

        $source->label =
            rm_get_source_label(
                $source->source
            );
    }

    $devices =
        rm_get_devices();

    foreach (
        $devices as $device
    ) {

        $device->label =
            rm_get_device_label(
                $device->dispositivo
            );
    }

    return [

        'summary' =>
            rm_get_summary(
                $period
            ),

        'today_summary' =>
            rm_get_summary(
                'today'
            ),

        'visits' =>
            rm_get_daily_visits(
                $period
            ),

        'avg_page_time' =>
            $avg_page_time,

        'countries' =>
            rm_get_top_countries(
                $period
            ),

        'sources' =>
            $sources,

        'top_resources' =>
            $resources,
                
        'top_pages' =>
            $pages,

        'total_time' =>
            rm_get_total_time_site(
                $period
            ),

        'devices' =>
            $devices,
    ];
}