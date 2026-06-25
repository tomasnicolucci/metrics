<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_get_summary($period = '30')
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

    $dates =
        rm_get_period_dates(
            $period
        );

    $from =
        $dates['from'];

    $to =
        $dates['to'];

    $config = rm_get_config();

    $minutes = intval(
        $config['active_minutes']
    );

    return [

        'visitas_hoy' =>
            (int) $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT COUNT(*)
                    FROM $views
                    WHERE DATE(fecha)
                    BETWEEN %s
                    AND %s
                    ",
                    $from,
                    $to
                )
            ),

        'usuarios_unicos' =>
            (int) $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT COUNT(
                        DISTINCT ip_hash
                    )
                    FROM $sessions
                    WHERE DATE(
                        first_visit
                    )
                    BETWEEN %s
                    AND %s
                    ",
                    $from,
                    $to
                )
            ),

        'pdf_opens' =>
            (int) $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT COUNT(*)
                    FROM $events
                    WHERE tipo = 'pdf_open'
                    AND DATE(fecha)
                    BETWEEN %s
                    AND %s
                    ",
                    $from,
                    $to
                )
            ),

        'visitantes_activos' =>
            (int) $wpdb->get_var(
                $wpdb->prepare(
                    "
                    SELECT COUNT(*)
                    FROM $sessions
                    WHERE last_activity >=
                    DATE_SUB(
                        NOW(),
                        INTERVAL %d MINUTE
                    )
                    ",
                    $minutes
                )
            )
    ];
}

function rm_get_top_pages(
    $period = '30',
    $limit = 10
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_views';

    $dates =
        rm_get_period_dates(
            $period
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                pagina,
                COUNT(*) AS total
            FROM $table
            WHERE DATE(fecha)
                BETWEEN %s
                AND %s
            GROUP BY pagina
            ORDER BY total DESC
            LIMIT %d
            ",
            $dates['from'],
            $dates['to'],
            $limit
        )
    );
}

function rm_get_top_resources(
    $period = '30',
    $limit = 10
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_events';

    $dates =
        rm_get_period_dates(
            $period
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                recurso,
                COUNT(*) AS total
            FROM $table
            WHERE tipo = 'click'
            AND recurso IS NOT NULL
            AND DATE(fecha)
                BETWEEN %s
                AND %s
            GROUP BY recurso
            ORDER BY total DESC
            LIMIT %d
            ",
            $dates['from'],
            $dates['to'],
            $limit
        )
    );
}

function rm_get_top_countries(
    $period = '30'
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $dates =
        rm_get_period_dates(
            $period
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                pais,
                COUNT(*) AS total
            FROM $table
            WHERE pais IS NOT NULL
            AND DATE(first_visit)
                BETWEEN %s
                AND %s
            GROUP BY pais
            ORDER BY total DESC
            ",
            $dates['from'],
            $dates['to']
        )
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

function rm_get_sources(
    $period = '30'
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_sessions';

    $dates =
        rm_get_period_dates(
            $period
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                source,
                COUNT(*) AS total
            FROM $table
            WHERE source IS NOT NULL
            AND DATE(first_visit)
                BETWEEN %s
                AND %s
            GROUP BY source
            ORDER BY total DESC
            ",
            $dates['from'],
            $dates['to']
        )
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

function rm_get_daily_visits(
    $period = '30'
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_stats_daily';

    $dates =
        rm_get_period_dates(
            $period
        );

    $from =
        $dates['from'];

    $to =
        $dates['to'];

    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    fecha,
                    visitas
                FROM $table
                WHERE fecha
                BETWEEN %s
                AND %s
                ORDER BY fecha ASC
                ",
                $from,
                $to
            )
        );

    $result = [];

    $current =
        strtotime($from);

    $end =
        strtotime($to);

    while (
        $current <= $end
    ) {

        $date =
            date(
                'Y-m-d',
                $current
            );

        $result[$date] = [
            'dia' =>
                date(
                    'd/m',
                    $current
                ),
            'total' => 0
        ];

        $current =
            strtotime(
                '+1 day',
                $current
            );
    }

    foreach ($rows as $row) {

        if (
            isset(
                $result[
                    $row->fecha
                ]
            )
        ) {

            $result[
                $row->fecha
            ]['total'] =
                (int)
                $row->visitas;
        }
    }

    return array_values(
        $result
    );
}

function rm_get_period_dates(
    $period = '30'
)
{
    $today =
        current_time('Y-m-d');

    switch ($period) {

        case 'today':

            return [
                'from' => $today,
                'to' => $today
            ];

        case '7':

            return [
                'from' => date(
                    'Y-m-d',
                    strtotime('-6 days')
                ),
                'to' => $today
            ];

        case 'month':

            return [
                'from' => date(
                    'Y-m-01'
                ),
                'to' => $today
            ];

        case '30':
        default:

            return [
                'from' => date(
                    'Y-m-d',
                    strtotime('-29 days')
                ),
                'to' => $today
            ];
    }
}

function rm_get_avg_page_time(
    $period = '30',
    $limit = 10
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_time';

    $dates =
        rm_get_period_dates(
            $period
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                pagina,
                ROUND ( AVG(segundos) ) AS promedio
            FROM $table
            WHERE DATE(fecha)
                BETWEEN %s
                AND %s
            AND pagina NOT LIKE '%wp-json%'
            AND pagina NOT LIKE '%wp-admin%'
            AND pagina NOT LIKE '%wp-login%'
            AND pagina NOT LIKE '%cliente/%'
            GROUP BY pagina
            ORDER BY promedio DESC
            LIMIT %d
            ",
            $dates['from'],
            $dates['to'],
            $limit
        )
    );
}