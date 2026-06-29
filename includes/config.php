<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_get_config()
{
    return [

        /*
        |--------------------------------------
        | Etiquetas del dashboard
        |--------------------------------------
        */
        'labels' => [

            'resource' =>
                get_option(
                    'rm_resource_label',
                    'Recursos'
                ),

            'event' =>
                get_option(
                    'rm_event_label',
                    'Eventos'
                ),

            'dashboard_title' =>
                get_option(
                    'rm_dashboard_title',
                    'Dashboard de Métricas'
                )
        ],

        /*
        |--------------------------------------
        | Páginas excluidas
        |--------------------------------------
        */
        'excluded_pages' => [

            '/cliente/login/',
            '/cliente/dashboard/',
            '/cliente/',
            '/wp-login.php',
            '/wp-admin/'
        ],

        'excluded_patterns' => [

            '/wp-content/',
            '/wp-json/',
            '/feed/',
            '.css',
            '.js',
            '.png',
            '.jpg',
            '.jpeg',
            '.svg',
            '.ico'
        ],

        'excluded_query_params' => [
            'elementor-preview',
            'preview',
            'customize_changeset_uuid'
        ],

        /*
        |--------------------------------------
        | Tiempo de visitante activo
        |--------------------------------------
        */
        'active_minutes' => 1,

        'default_event_type' => 'click'
    ];
}