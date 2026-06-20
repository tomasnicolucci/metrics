<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode(
    'rm_login',
    'rm_login_shortcode'
);

function rm_login_shortcode()
{
    if (is_user_logged_in()) {

        wp_redirect(
            home_url('/cliente/dashboard')
        );

        exit;
    }

    ob_start();

    include RM_PATH .
        'templates/login.php';

    return ob_get_clean();
}

add_action(
    'admin_init',
    'rm_block_admin'
);

function rm_block_admin()
{
    if (
        current_user_can('cliente_metricas')
        && !wp_doing_ajax()
    ) {

        wp_redirect(
            home_url('/cliente/dashboard')
        );

        exit;
    }
}