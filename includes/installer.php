<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_activate()
{
    rm_create_role();
    rm_create_tables();
    rm_create_pages();
    rm_schedule_daily_stats();

    flush_rewrite_rules();
}

function rm_create_tables()
{
    global $wpdb;

    $charset = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    /*
     * Tabla de sesiones
     */
    $table_sessions = $wpdb->prefix . 'rm_sessions';

    $sql = "
    CREATE TABLE $table_sessions (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id VARCHAR(64) NOT NULL,
        ip_hash VARCHAR(255) NULL,
        pais VARCHAR(100) NULL,
        ciudad VARCHAR(100) NULL,
        dispositivo VARCHAR(50) NULL,
        navegador VARCHAR(100) NULL,
        source VARCHAR(100) NULL,
        referrer TEXT NULL,
        first_visit DATETIME NULL,
        last_activity DATETIME NULL,
        PRIMARY KEY (id),
        UNIQUE KEY session_id (session_id)
    ) $charset;
    ";

    dbDelta($sql);

    /*
     * Tabla de páginas vistas
     */
    $table_views = $wpdb->prefix . 'rm_page_views';

    $sql = "
    CREATE TABLE $table_views (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id VARCHAR(64) NOT NULL,
        pagina TEXT NOT NULL,
        fecha DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY session_id (session_id)
    ) $charset;
    ";

    dbDelta($sql);

    /*
     * Tabla de eventos
     */
    $table_events = $wpdb->prefix . 'rm_events';

    $sql = "
    CREATE TABLE $table_events (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id VARCHAR(64) NOT NULL,
        tipo VARCHAR(50) NOT NULL,
        recurso TEXT NULL,
        fecha DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY session_id (session_id),
        KEY tipo (tipo)
    ) $charset;
    ";

    dbDelta($sql);

    /*
     * Tabla de tiempo de permanencia
     */
    $table_time = $wpdb->prefix . 'rm_page_time';

    $sql = "
    CREATE TABLE $table_time (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id VARCHAR(64) NOT NULL,
        pagina TEXT NOT NULL,
        segundos INT UNSIGNED NOT NULL DEFAULT 0,
        fecha DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY session_id (session_id)
    ) $charset;
    ";

    dbDelta($sql);

    /*
     * Resumen diario
     */
    $table_daily = $wpdb->prefix . 'rm_stats_daily';

    $sql = "
    CREATE TABLE $table_daily (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        fecha DATE NOT NULL,
        visitas INT UNSIGNED DEFAULT 0,
        usuarios_unicos INT UNSIGNED DEFAULT 0,
        pdf_opens INT UNSIGNED DEFAULT 0,
        pdf_downloads INT UNSIGNED DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY fecha (fecha)
    ) $charset;
    ";

    dbDelta($sql);

    /*
     * Resumen mensual
     */
    $table_monthly = $wpdb->prefix . 'rm_stats_monthly';

    $sql = "
    CREATE TABLE $table_monthly (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        anio INT UNSIGNED NOT NULL,
        mes INT UNSIGNED NOT NULL,
        visitas INT UNSIGNED DEFAULT 0,
        usuarios_unicos INT UNSIGNED DEFAULT 0,
        pdf_opens INT UNSIGNED DEFAULT 0,
        pdf_downloads INT UNSIGNED DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY periodo (anio, mes)
    ) $charset;
    ";

    dbDelta($sql);

    /*
     * Resumen PDFs
     */

    $table_pdf = $wpdb->prefix . 'rm_pdf_stats';

    $sql = "
    CREATE TABLE $table_pdf (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        slug VARCHAR(100) NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        url TEXT NULL,
        opens INT UNSIGNED DEFAULT 0,
        downloads INT UNSIGNED DEFAULT 0,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        PRIMARY KEY (id),
        UNIQUE KEY slug (slug)
    ) $charset;
    ";

    dbDelta($sql);
}

function rm_create_pages()
{
    $cliente_id = rm_create_cliente_page();

    if (!get_page_by_path('cliente/login')) {
        wp_insert_post([
            'post_title'   => 'Login Cliente',
            'post_name'    => 'login',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_parent'  => $cliente_id,
            'post_content' => '[rm_login]'
        ]);
    }

    if (!get_page_by_path('cliente/dashboard')) {
        wp_insert_post([
            'post_title'   => 'Dashboard Cliente',
            'post_name'    => 'dashboard',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_parent'  => $cliente_id,
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
        'post_title'  => 'Cliente',
        'post_name'   => 'cliente',
        'post_status' => 'publish',
        'post_type'   => 'page'
    ]);
}