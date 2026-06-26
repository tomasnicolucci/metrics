<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'init',
    'rm_register_export'
);

function rm_register_export()
{
    if (
        !isset($_GET['rm_export'])
        ||
        $_GET['rm_export'] !== 'csv'
    ) {
        return;
    }

    if (
        !is_user_logged_in()
    ) {
        wp_die('No autorizado');
    }

    if (
        !current_user_can('administrator')
        &&
        !current_user_can('cliente_metricas')
    ) {
        wp_die('No autorizado');
    }

    $period =
        sanitize_text_field(
            $_GET['period']
            ?? '30'
        );

    rm_export_csv(
        $period
    );

    exit;
}

function rm_export_csv(
    $period = '30'
)
{
    header(
        'Content-Type: text/csv; charset=utf-8'
    );

    header(
        'Content-Disposition: attachment; filename="metricas.csv"'
    );

    $output =
        fopen(
            'php://output',
            'w'
        );

    rm_csv_header(
        $output,
        $period
    );

    rm_csv_summary(
        $output,
        $period
    );

    rm_csv_top_pages(
        $output,
        $period
    );

    rm_csv_resources(
        $output,
        $period
    );

    rm_csv_countries(
        $output,
        $period
    );

    rm_csv_sources(
        $output,
        $period
    );

    rm_csv_time(
        $output,
        $period
    );

    rm_csv_daily_visits(
        $output,
        $period
    );

    fclose(
        $output
    );
}

function rm_csv_header(
    $output,
    $period
)
{
    $period_name = [

        'today' => 'Hoy',
        '7' => 'Últimos 7 días',
        '30' => 'Últimos 30 días',
        'month' => 'Este mes'

    ][$period]
    ?? $period;

    fputcsv(
        $output,
        ['Reporte de Métricas']
    );

    fputcsv($output, []);

    fputcsv(
        $output,
        [
            'Sitio',
            get_bloginfo('name')
        ]
    );

    fputcsv(
        $output,
        [
            'URL',
            home_url()
        ]
    );

    fputcsv(
        $output,
        [
            'Periodo',
            $period_name
        ]
    );

    fputcsv(
        $output,
        [
            'Generado',
            current_time(
                'd/m/Y H:i'
            )
        ]
    );

    fputcsv($output, []);
}

function rm_csv_summary(
    $output,
    $period
)
{
    $summary =
        rm_get_summary(
            $period
        );

    fputcsv(
        $output,
        ['RESUMEN']
    );

    fputcsv($output, []);

    fputcsv(
        $output,
        [
            'Visitas',
            $summary['visitas_hoy']
        ]
    );

    fputcsv(
        $output,
        [
            'Usuarios únicos',
            $summary['usuarios_unicos']
        ]
    );

    fputcsv(
        $output,
        [
            'PDF abiertos',
            $summary['pdf_opens']
        ]
    );

    fputcsv(
        $output,
        [
            'Visitantes activos',
            $summary['visitantes_activos']
        ]
    );

    fputcsv($output, []);
}

function rm_csv_top_pages(
    $output,
    $period
)
{
    $pages =
        rm_get_top_pages(
            $period
        );

    fputcsv(
        $output,
        ['PÁGINAS MÁS VISITADAS']
    );

    fputcsv(
        $output,
        [
            'Página',
            'Visitas'
        ]
    );

    foreach (
        $pages as $page
    ) {

        fputcsv(
            $output,
            [
                rm_get_page_label(
                    $page->pagina
                ),
                $page->total
            ]
        );
    }

    fputcsv($output, []);
}

function rm_csv_resources(
    $output,
    $period
)
{
    $resources =
        rm_get_top_resources(
            $period
        );

    fputcsv(
        $output,
        ['RECURSOS']
    );

    fputcsv(
        $output,
        [
            'Recurso',
            'Interacciones'
        ]
    );

    foreach (
        $resources as $resource
    ) {

        fputcsv(
            $output,
            [
                rm_get_resource_label(
                    $resource->recurso
                ),
                $resource->total
            ]
        );
    }

    fputcsv($output, []);
}

function rm_csv_countries(
    $output,
    $period
)
{
    $countries =
        rm_get_top_countries(
            $period
        );

    fputcsv(
        $output,
        ['PAÍSES']
    );

    fputcsv(
        $output,
        [
            'País',
            'Visitas'
        ]
    );

    foreach (
        $countries as $country
    ) {

        fputcsv(
            $output,
            [
                $country->pais,
                $country->total
            ]
        );
    }

    fputcsv($output, []);
}

function rm_csv_sources(
    $output,
    $period
)
{
    $sources =
        rm_get_sources(
            $period
        );

    fputcsv(
        $output,
        ['FUENTES DE TRÁFICO']
    );

    fputcsv(
        $output,
        [
            'Fuente',
            'Visitas'
        ]
    );

    foreach (
        $sources as $source
    ) {

        fputcsv(
            $output,
            [
                rm_get_source_label(
                    $source->source
                ),
                $source->total
            ]
        );
    }

    fputcsv($output, []);
}

function rm_csv_time(
    $output,
    $period
)
{
    $seconds =
        rm_get_total_time(
            $period
        );

    $hours =
        round(
            $seconds / 3600,
            2
        );

    fputcsv(
        $output,
        ['TIEMPO DE PERMANENCIA']
    );

    fputcsv(
        $output,
        [
            'Horas totales',
            $hours
        ]
    );

    fputcsv($output, []);
}

function rm_csv_daily_visits(
    $output,
    $period
)
{
    $visits =
        rm_get_daily_visits(
            $period
        );

    fputcsv(
        $output,
        ['VISITAS POR DÍA']
    );

    fputcsv(
        $output,
        [
            'Fecha',
            'Visitas'
        ]
    );

    foreach (
        $visits as $visit
    ) {

        fputcsv(
            $output,
            [
                $visit['dia'],
                $visit['total']
            ]
        );
    }
}