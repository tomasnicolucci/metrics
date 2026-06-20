<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_activate()
{
    rm_create_role();

    rm_create_tables();

    rm_create_pages();

    flush_rewrite_rules();
}

function rm_create_tables()
{
    global $wpdb;

    $table = $wpdb->prefix . 'rm_sessions';

    $charset = $wpdb->get_charset_collate();

    require_once ABSPATH .
        'wp-admin/includes/upgrade.php';

    $sql = "
        CREATE TABLE $table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(64) NOT NULL,
            first_visit DATETIME NULL,
            last_activity DATETIME NULL,
            INDEX(session_id)
        )
        $charset;
    ";

    dbDelta($sql);
}

function rm_create_pages()
{
    $cliente_id = rm_create_cliente_page();

    if (!get_page_by_path('cliente/login')) {

        $login = wp_insert_post([
            'post_title' => 'Login Cliente',
            'post_name' => 'login',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $cliente_id,
            'post_content' => '[rm_login]'
        ]);
    }

    if (!get_page_by_path('cliente/dashboard')) {

        $dashboard = wp_insert_post([
            'post_title' => 'Dashboard Cliente',
            'post_name' => 'dashboard',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $cliente_id,
            'post_content' => '[rm_dashboard]'
        ]);
    }
}

function rm_create_cliente_page()
{
    $page = get_page_by_path('cliente');

    if ($page) {
        return $page->ID;
    }

    return wp_insert_post([
        'post_title' => 'Cliente',
        'post_name' => 'cliente',
        'post_status' => 'publish',
        'post_type' => 'page'
    ]);
}

