<?php

if (!defined('ABSPATH')) {
    exit;
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

add_shortcode(
    'rm_logout',
    'rm_logout_shortcode'
);

function rm_logout_shortcode()
{
    return sprintf(
        '<a class="rm-logout-button" href="%s">Cerrar sesión</a>',
        esc_url(
            wp_logout_url(
                home_url('/cliente/login')
            )
        )
    );
}