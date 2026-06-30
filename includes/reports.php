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

    $home =
        untrailingslashit(
            home_url()
        );

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT
                CASE
                    WHEN TRIM(TRAILING '/' FROM pagina) = %s
                    THEN %s
                    ELSE pagina
                END AS pagina,
                COUNT(*) AS total
            FROM $table
            WHERE DATE(fecha)
                BETWEEN %s
                AND %s
                AND pagina NOT LIKE %s
                AND pagina NOT LIKE %s
            GROUP BY
                CASE
                    WHEN TRIM(TRAILING '/' FROM pagina) = %s
                    THEN %s
                    ELSE pagina
                END
            ORDER BY total DESC
            LIMIT %d
            ",
            $home,
            trailingslashit($home),
            $dates['from'],
            $dates['to'],
            '%/cliente/%',
            '%wp-login.php%',
            $home,
            trailingslashit($home),
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
        'rm_page_views';

    $dates =
        rm_get_period_dates(
            $period
        );

    $from =
        $dates['from'];

    $to =
        $dates['to'];

    if ($period === 'today') {

        return rm_get_hourly_visits();
    }

    if ($period === '365') {

        return rm_get_monthly_visits(
            $from,
            $to
        );
    }

    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    DATE(fecha) AS fecha,
                    COUNT(*) AS visitas
                FROM $table
                WHERE DATE(fecha)
                    BETWEEN %s
                    AND %s
                GROUP BY DATE(fecha)
                ORDER BY DATE(fecha)
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

        $result[
            $row->fecha
        ]['total'] =
            (int)
            $row->visitas;
    }

    return array_values(
        $result
    );
}

function rm_get_monthly_visits(
    $from,
    $to
)
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_views';

    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    YEAR(fecha) AS anio,
                    MONTH(fecha) AS mes,
                    COUNT(*) AS total
                FROM $table
                WHERE DATE(fecha)
                    BETWEEN %s
                    AND %s
                GROUP BY
                    YEAR(fecha),
                    MONTH(fecha)
                ORDER BY
                    YEAR(fecha),
                    MONTH(fecha)
                ",
                $from,
                $to
            )
        );

    $months = [

        1  => 'Ene',
        2  => 'Feb',
        3  => 'Mar',
        4  => 'Abr',
        5  => 'May',
        6  => 'Jun',
        7  => 'Jul',
        8  => 'Ago',
        9  => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dic'

    ];

    $result = [];

    $current =
        new DateTime($from);

    $current->modify('first day of this month');

    $end =
        new DateTime($to);

    $end->modify('first day of this month');

    while ($current <= $end) {

        $key =
            $current->format('Y-n');

        $result[$key] = [

            'dia' =>
                $months[
                    (int)
                    $current->format('n')
                ],

            'total' => 0

        ];

        $current->modify('+1 month');
    }

    foreach ($rows as $row) {

        $key =
            $row->anio .
            '-' .
            $row->mes;

        if (
            isset($result[$key])
        ) {

            $result[$key]['total'] =
                (int)
                $row->total;
        }
    }

    return array_values(
        $result
    );
}

function rm_get_hourly_visits()
{
    global $wpdb;

    $table =
        $wpdb->prefix .
        'rm_page_views';

    $today =
        current_time('Y-m-d');

    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    HOUR(fecha) AS hora,
                    COUNT(*) AS total
                FROM $table
                WHERE DATE(fecha) = %s
                GROUP BY HOUR(fecha)
                ORDER BY HOUR(fecha)
                ",
                $today
            )
        );

    $result = [];

    for ($i = 0; $i < 24; $i++) {

        $label =
            str_pad(
                $i,
                2,
                '0',
                STR_PAD_LEFT
            ) . ':00';

        $result[$i] = [

            'dia' => $label,

            'total' => 0

        ];
    }

    foreach ($rows as $row) {

        $result[
            (int)$row->hora
        ]['total'] =
            (int)$row->total;
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
        
        case '365':

            return [
                'from' => date(
                    'Y-m-d',
                    strtotime('-364 days')
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

function rm_get_total_time_site(
    $period = '30'
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

    return (int)
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT
                    SUM(segundos)
                FROM $table
                WHERE DATE(fecha)
                    BETWEEN %s
                    AND %s

                AND pagina NOT LIKE %s
                AND pagina NOT LIKE %s
                AND pagina NOT LIKE %s
                ",
                $dates['from'],
                $dates['to'],

                '%/cliente/%',
                '%/wp-json/%',
                '%/wp-admin/%',
                '%/wp-login/%'
            )
        );
}

function rm_get_total_time(
    $period = '30'
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

    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    pagina,
                    segundos
                FROM $table
                WHERE DATE(fecha)
                    BETWEEN %s
                    AND %s
                ",
                $dates['from'],
                $dates['to']
            )
        );

    $total = 0;

    foreach (
        $rows as $row
    ) {

        if (
            rm_is_internal_page(
                $row->pagina
            )
        ) {
            continue;
        }

        $total +=
            (int)
            $row->segundos;

    }

    return $total;
}

function rm_is_internal_page(
    $url
)
{
    if (
        empty($url)
    ) {
        return false;
    }

    $path =
        wp_parse_url(
            $url,
            PHP_URL_PATH
        );

    $internal = [

        '/wp-login.php',
        '/wp-admin',
        '/dashboard',
        '/metricas',
        '/login'

    ];

    foreach (
        $internal as $page
    ) {

        if (
            strpos(
                $path,
                $page
            ) === 0
        ) {
            return true;
        }

    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Funciones de etiquetas para Dashboard
|--------------------------------------------------------------------------
*/

function rm_get_source_label(
    $source
)
{
    return match ($source) {

        'Direct'   => 'Acceso directo',
        'Referral' => 'Sitios externos',
        'Google'   => 'Google',
        'Social'   => 'Redes sociales',
        'Email'    => 'Correo electrónico',

        default => $source
    };
}

function rm_get_resource_label(
    $resource
)
{
    static $labels = [

        'https://drive.google.com/file/d/1B8Yukmbb3vUZUoj0OALR79nYQxkiM82s/view'
            => 'Revista N° 1',

        // Cuando agregues más recursos:
        // 'https://....'
        //     => 'Revista N° 2',
    ];

    return
        $labels[$resource]
        ??
        $resource;
}

function rm_get_device_label(
    $device
)
{
    return match (
        strtolower($device)
    ) {

        'desktop' =>
            'PC de escritorio',

        'mobile' =>
            'Smartphone',

        'tablet' =>
            'Tablet',

        default =>
            $device
    };
}