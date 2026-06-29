<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'template_redirect',
    'rm_track_visit'
);

function rm_track_visit()
{
    if (is_admin()) {
        return;
    }

    $session_id = rm_get_session_id();

    rm_save_session(
        $session_id
    );

    rm_save_page_view(
        $session_id
    );
}

function rm_save_session(
    $session_id
) {
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $existing =
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT id
                FROM $table
                WHERE session_id=%s
                ",
                $session_id
            )
        );

    if ($existing) {

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

        return;
    }

    $geo = rm_get_geo_data();
    $referrer = rm_get_referrer();

    $wpdb->insert(
        $table,
        [
            'session_id' => $session_id,
            'ip_hash' => rm_get_ip_hash(),
            'pais' => $geo['pais'],
            'ciudad' => $geo['ciudad'],
            'dispositivo' => rm_get_device(),
            'navegador' => rm_get_browser(),
            'source' => rm_get_source($referrer),
            'referrer' => $referrer,
            'first_visit' => current_time('mysql'),
            'last_activity' => current_time('mysql')
        ]
    );
}

function rm_save_page_view(
    $session_id
) {
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_views';

    $current_url =
        esc_url_raw(
            home_url(
                add_query_arg(
                    [],
                    $_SERVER['REQUEST_URI']
                )
            )
        );

    $path = wp_parse_url(
        $current_url,
        PHP_URL_PATH
    );

    $path = trailingslashit($path);

    /*
    |--------------------------------------------------------------------------
    | Ignorar recursos y rutas técnicas
    |--------------------------------------------------------------------------
    */
    if (

        str_ends_with($path, '.css/') ||
        str_ends_with($path, '.js/') ||
        str_ends_with($path, '.png/') ||
        str_ends_with($path, '.jpg/') ||
        str_ends_with($path, '.jpeg/') ||
        str_ends_with($path, '.svg/') ||
        str_ends_with($path, '.ico/') ||

        str_contains($path, '/wp-content/') ||
        str_contains($path, '/wp-json/') ||
        str_contains($path, '/feed/') ||
        str_contains($path, '/wp-admin/') ||
        str_contains($path, '/wp-login.php') ||
        str_contains($path, '/cliente/')

    ) {
        return;
    }

    if (
        isset($_GET['elementor-preview'])
    ) {
        return;
    }

    $config =
        rm_get_config();

    $excluded_pages =
        $config['excluded_pages'];

    if (
        in_array(
            trailingslashit($path),
            $excluded_pages,
            true
        )
    ) {
        return;
    }

    $wpdb->insert(
        $table,
        [
            'session_id' =>
                $session_id,

            'pagina' =>
                $current_url,

            'fecha' =>
                current_time(
                    'mysql'
                )
        ]
    );
}

function rm_get_ip_hash()
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    return hash(
        'sha256',
        wp_salt() . $ip
    );
}

function rm_get_referrer()
{
    return sanitize_text_field(
        $_SERVER['HTTP_REFERER'] ?? ''
    );
}

function rm_get_source($referrer)
{
    if (!$referrer) {
        return 'Direct';
    }

    $host = wp_parse_url(
        $referrer,
        PHP_URL_HOST
    );

    if (!$host) {
        return 'Referral';
    }

    if (stripos($host, 'google') !== false) {
        return 'Google';
    }

    if (stripos($host, 'facebook') !== false) {
        return 'Facebook';
    }

    if (stripos($host, 'instagram') !== false) {
        return 'Instagram';
    }

    if (stripos($host, 'linkedin') !== false) {
        return 'LinkedIn';
    }

    return 'Referral';
}