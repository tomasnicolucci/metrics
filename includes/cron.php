<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'init',
    'rm_schedule_daily_stats'
);

function rm_schedule_daily_stats()
{
    if (
        !wp_next_scheduled(
            'rm_generate_daily_stats'
        )
    ) {

        wp_schedule_event(
            time(),
            'daily',
            'rm_generate_daily_stats'
        );
    }
}

add_action(
    'rm_generate_daily_stats',
    'rm_generate_daily_stats'
);

function rm_generate_daily_stats()
{
    global $wpdb;

    $views =
        $wpdb->prefix .
        'rm_page_views';

    $sessions =
        $wpdb->prefix .
        'rm_sessions';

    $events =
        $wpdb->prefix .
        'rm_events';

    $daily =
        $wpdb->prefix .
        'rm_stats_daily';

    $monthly =
        $wpdb->prefix .
        'rm_stats_monthly';

    $date =
        date(
            'Y-m-d',
            strtotime('-1 day')
        );

    $visitas =
        (int) $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM $views
                WHERE DATE(fecha)=%s
                ",
                $date
            )
        );

    $usuarios =
        (int) $wpdb->get_var(
            "
            SELECT COUNT(
                DISTINCT ip_hash
            )
            FROM $sessions
            "
        );

    $pdfs =
        (int) $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM $events
                WHERE tipo='pdf_open'
                AND DATE(fecha)=%s
                ",
                $date
            )
        );

            $existing =
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT id
                FROM $daily
                WHERE fecha=%s
                ",
                $date
            )
        );

    if (!$existing) {

        $wpdb->insert(
            $daily,
            [
                'fecha' =>
                    $date,

                'visitas' =>
                    $visitas,

                'usuarios_unicos' =>
                    $usuarios,

                'pdf_opens' =>
                    $pdfs,

                'pdf_downloads' =>
                    0
            ]
        );
    }

        $year =
        current_time('Y');

    $month =
        current_time('n');

    $existing =
        $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT id
                FROM $monthly
                WHERE anio=%d
                AND mes=%d
                ",
                $year,
                $month
            )
        );

    if (!$existing) {

        $wpdb->insert(
            $monthly,
            [
                'anio' =>
                    $year,

                'mes' =>
                    $month,

                'visitas' =>
                    $visitas,

                'usuarios_unicos' =>
                    $usuarios,

                'pdf_opens' =>
                    $pdfs,

                'pdf_downloads' =>
                    0
            ]
        );
    } else {

        $wpdb->query(
            $wpdb->prepare(
                "
                UPDATE $monthly
                SET
                    visitas =
                        visitas + %d,

                    pdf_opens =
                        pdf_opens + %d
                WHERE
                    anio=%d
                    AND mes=%d
                ",
                $visitas,
                $pdfs,
                $year,
                $month
            )
        );
    }
}