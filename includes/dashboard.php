<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode(
    'rm_dashboard',
    'rm_dashboard_shortcode'
);

function rm_dashboard_shortcode()
{
    if (
        !is_user_logged_in()
        ||
        (
            !current_user_can('cliente_metricas')
            &&
            !current_user_can('administrator')
        )
    ) {

        wp_redirect(
            home_url('/cliente/login')
        );

        exit;
    }

    $summary =
    rm_get_summary();

    $top_pages =
        rm_get_top_pages();

    $top_pdfs =
        rm_get_top_pdfs();

    $countries =
        rm_get_top_countries();

    $devices =
        rm_get_devices();

    $sources =
        rm_get_sources();

    $visits_chart =
        rm_get_visits_last_30_days();

    ob_start();

    include RM_PATH .
        'templates/dashboard.php';

    return ob_get_clean();
}

add_action(
    'wp_enqueue_scripts',
    'rm_dashboard_assets'
);

function rm_dashboard_assets()
{
    if (
        !is_page('dashboard')
        &&
        !is_page('cliente/dashboard')
    ) {
        return;
    }

    wp_enqueue_style(
        'rm-dashboard',
        RM_URL .
        'assets/css/dashboard.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js',
        [],
        '4.4.0',
        true
    );

    wp_enqueue_script(
        'rm-dashboard',
        RM_URL .
        'assets/js/dashboard.js',
        [],
        '1.0.0',
        true
    );
    
    $chart_data = rm_get_visits_last_30_days();

    wp_localize_script(
        'rm-dashboard',
        'rmDashboard',
        [
            'visits' => $chart_data
        ]
    );
}