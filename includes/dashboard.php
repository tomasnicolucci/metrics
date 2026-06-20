<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode(
    'rm_dashboard',
    'rm_dashboard_shortcode'
);

// function rm_dashboard_shortcode()
// {
//     if (!is_user_logged_in()) {

//         wp_redirect(
//             home_url('/cliente/login')
//         );

//         exit;
//     }

//     ob_start();

//     include RM_PATH .
//         'templates/dashboard.php';

//     return ob_get_clean();
// }

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

    ob_start();

    include RM_PATH .
        'templates/dashboard.php';

    return ob_get_clean();
}