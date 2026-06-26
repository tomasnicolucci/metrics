<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="rm-container">

    <h1>
        <?= esc_html(
            $config['labels']['dashboard_title']
        ); ?>
    </h1>

    <p>
        Bienvenido
        <?= esc_html(
            wp_get_current_user()->display_name
        ); ?>
    </p>
    
    <h2>Datos de hoy</h2>
    <div class="rm-grid">
        <div class="rm-card">
            <h3>Visitas hoy</h3>

            <div class="rm-number" id="rm-today-visits">
                <?= $today_summary['visitas_hoy']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>Usuarios únicos</h3>

            <div class="rm-number" id="rm-today-users">
                <?= $today_summary['usuarios_unicos']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>PDFs abiertos</h3>

            <div class="rm-number" id="rm-today-pdfs">
                <?= $today_summary['pdf_opens']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>Visitantes activos</h3>

            <div
                class="rm-number"
                id="rm-active-users"
            >
                <?= $today_summary['visitantes_activos']; ?>
            </div>
        </div>

    </div>

    <script>

        const rmChartLabels =
            <?= wp_json_encode(
                array_map(
                    function ($row) {
                        return date(
                            'd/m',
                            strtotime(
                                $row->fecha
                            )
                        );
                    },
                    $daily_visits
                )
            ); ?>;

        const rmChartValues =
            <?= wp_json_encode(
                array_map(
                    function ($row) {
                        return (int)
                            $row->visitas;
                    },
                    $daily_visits
                )
            ); ?>;

    </script>

    <div class="rm-filters">

        <h2>Datos históricos</h2>

        <div class="rm-filter-actions">

            <label>
                Período:
            </label>

            <select id="rm-period">

                <option value="today">Hoy</option>
                <option value="7">Últimos 7 días</option>
                <option value="30" selected>Últimos 30 días</option>
                <option value="month">Este mes</option>

            </select>

            <button
                id="rm-export-csv"
                class="button button-primary"
            >
                Exportar CSV
            </button>

        </div>

    </div>
    
    <!-- Grafico -->
    <div class="rm-grid">

        <div class="rm-card">

            <h3>
                Visitas
            </h3>

            <canvas
                id="rm-visits-chart"
            ></canvas>

        </div>

    </div>

    <hr>

    <div class="rm-grid">

        <div class="rm-card">
            <h3>Visitas</h3>

            <div class="rm-number" id="rm-period-visits">
                <?= $summary['visitas_hoy']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>Usuarios únicos</h3>

            <div class="rm-number" id="rm-period-users">
                <?= $summary['usuarios_unicos']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>PDFs abiertos</h3>

            <div class="rm-number" id="rm-period-pdfs">
                <?= $summary['pdf_opens']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>Páginas más visitadas</h3>

            <ul id="rm-pages-list">
                <?php foreach ($top_pages as $page): ?>

                    <li>
                        <span>
                            <?= esc_html(
                                rm_get_page_label(
                                    $page->pagina
                                )
                            ); ?>
                        </span>    
                        <strong>
                            <?= $page->total; ?>
                        </strong>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>

        <div class="rm-card">

            <h3>
                <?= esc_html(
                    $config['labels']['resource']
                ); ?>
                más interactuados
            </h3>

            <ul id="rm-resources-list">
                <?php foreach ($top_resources as $resource): ?>

                    <li>
                        <span>
                            <?= esc_html($resource->recurso); ?>
                        </span>
                        <strong>
                            <?= $resource->total; ?>
                        </strong>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>

        <div class="rm-card">

            <h3>Países</h3>

            <select id="rm-countries-type">

                <option value="pie">
                    Torta
                </option>

                <option value="bar">
                    Barras
                </option>

            </select>

            <canvas
                id="rm-countries-chart"
            ></canvas>

            <!-- <ul id="rm-countries-list">
                <?php foreach ($countries as $country): ?>

                    <li>
                        <?= esc_html($country->pais); ?>
                        -
                        <?= $country->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul> -->

        </div>

        <div class="rm-card">

            <h3>Dispositivos</h3>

            <ul>
                <?php foreach ($devices as $device): ?>

                    <li>
                        <?= esc_html(
                            rm_get_device_label(
                                $device->dispositivo
                            )
                        ); ?>
                        -
                        <?= $device->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>

        <div class="rm-card">

            <h3>Fuentes de ingreso</h3>

            <select id="rm-sources-type">

                <option value="pie">
                    Torta
                </option>

                <option value="bar">
                    Barras
                </option>

            </select>

            <canvas
                id="rm-sources-chart"
            ></canvas>

            <!-- <ul id="rm-sources-list">
                <?php foreach ($sources as $source): ?>

                    <li>
                        <?= esc_html($source->source); ?>
                        -
                        <?= $source->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul> -->

        </div>
        
        <div class="rm-card">

            <h3>
                Tiempo de permanencia total
            </h3>

            <div
                class="rm-number"
                id="rm-total-time"
            >
                00:00
            </div>

        </div>

        <div class="rm-card">

            <h3>
                Tiempo promedio por página
            </h3>

            <ul id="rm-page-time-list">

                <?php foreach (
                    $avg_page_time
                    as $page
                ): ?>

                    <li>

                        <span>
                            <?= esc_html(
                                rm_get_page_label(
                                    $page->pagina
                                )
                            ); ?>
                        </span>

                        <strong>
                            <?= gmdate(
                                'i:s',
                                (int)
                                $page->promedio
                            ); ?>
                        </strong>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>
    </div>
</div>