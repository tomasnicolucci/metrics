<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_get_summary()
{
    global $wpdb;

    $sessions =
        $wpdb->prefix .
        'rm_sessions';

    $views =
        $wpdb->prefix .
        'rm_page_views';

    $events =
        $wpdb->prefix .
        'rm_events';

    $today =
        current_time('Y-m-d');

    return [

        'visitas_hoy' =>
            (int) $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT COUNT(*)
                    FROM $views
                    WHERE DATE(fecha) = %s
                    ",
                    $today
                )
            ),

        'usuarios_unicos' =>
            (int) $wpdb->get_var(
                "
                SELECT COUNT(DISTINCT ip_hash)
                FROM $sessions
                "
            ),

        'pdf_opens' =>
            (int) $wpdb->get_var(
                "
                SELECT COUNT(*)
                FROM $events
                WHERE tipo = 'pdf_open'
                "
            ),

        'visitantes_activos' =>
            (int) $wpdb->get_var(
                "
                SELECT COUNT(*)
                FROM $sessions
                WHERE last_activity >=
                DATE_SUB(
                    NOW(),
                    INTERVAL 2 MINUTE
                )
                "
            )
    ];
}

function rm_get_top_pages($limit = 10)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_views';

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                pagina,
                COUNT(*) AS total
            FROM $table
            GROUP BY pagina
            ORDER BY total DESC
            LIMIT %d
            ",
            $limit
        )
    );
}

function rm_get_top_resources($limit = 10)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_events';

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                recurso,
                COUNT(*) AS total
            FROM $table
            WHERE tipo = 'click'
            GROUP BY recurso
            ORDER BY total DESC
            LIMIT %d
            ",
            $limit
        )
    );
}

function rm_get_top_countries()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    return $wpdb->get_results(
        "
        SELECT
            pais,
            COUNT(*) AS total
        FROM $table
        WHERE pais IS NOT NULL
        GROUP BY pais
        ORDER BY total DESC
        "
    );
}

function rm_get_devices()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    return $wpdb->get_results(
        "
        SELECT
            dispositivo,
            COUNT(*) AS total
        FROM $table
        WHERE dispositivo IS NOT NULL
        GROUP BY dispositivo
        ORDER BY total DESC
        "
    );
}

function rm_get_sources()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    return $wpdb->get_results(
        "
        SELECT
            source,
            COUNT(*) AS total
        FROM $table
        WHERE source IS NOT NULL
        GROUP BY source
        ORDER BY total DESC
        "
    );
}

function rm_get_visits_last_30_days()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_views';

    return $wpdb->get_results(
        "
        SELECT
            DATE(fecha) AS dia,
            COUNT(*) AS total
        FROM $table
        WHERE fecha >=
            DATE_SUB(
                NOW(),
                INTERVAL 30 DAY
            )
        GROUP BY dia
        ORDER BY dia ASC
        "
    );
}

function rm_get_page_label(
    $url
)
{
    $path =
        wp_parse_url(
            $url,
            PHP_URL_PATH
        );

    $path =
        trim(
            $path,
            '/'
        );

    if (
        empty($path)
    ) {
        return 'Inicio';
    }

    $page =
        get_page_by_path(
            $path
        );

    if (
        $page
    ) {
        return $page->post_title;
    }

    return ucwords(
        str_replace(
            [
                '-',
                '_'
            ],
            ' ',
            $path
        )
    );
}