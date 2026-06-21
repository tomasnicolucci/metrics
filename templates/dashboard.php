<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="rm-container">

    <h1>Dashboard de Métricas</h1>

    <p>
        Bienvenido
        <?= esc_html(
            wp_get_current_user()->display_name
        ); ?>
    </p>

    <div class="rm-grid">

        <div class="rm-card">
            <h3>Visitas hoy</h3>

            <div class="rm-number">
                <?= $summary['visitas_hoy']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>Usuarios únicos</h3>

            <div class="rm-number">
                <?= $summary['usuarios_unicos']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>PDFs abiertos</h3>

            <div class="rm-number">
                <?= $summary['pdf_opens']; ?>
            </div>
        </div>

        <div class="rm-card">
            <h3>Visitantes activos</h3>

            <div
                class="rm-number"
                id="rm-active-users"
            >
                <?= $summary['visitantes_activos']; ?>
            </div>
        </div>

    </div>

    <!-- Gráfico -->
    <div class="rm-grid">

        <div class="rm-card">

            <h3>
                Visitas últimos 30 días
            </h3>

            <canvas
                id="rm-visits-chart"
            ></canvas>

        </div>

    </div>

    <hr>

    <div class="rm-grid">

        <div class="rm-card">

            <h2>Páginas más visitadas</h2>

            <ul>
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

            <h2>Revistas más abiertas</h2>

            <ul>
                <?php foreach ($top_pdfs as $pdf): ?>

                    <li>
                        <?= esc_html($pdf->recurso); ?>
                        -
                        <?= $pdf->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>

        <div class="rm-card">

            <h2>Países</h2>

            <ul>
                <?php foreach ($countries as $country): ?>

                    <li>
                        <?= esc_html($country->pais); ?>
                        -
                        <?= $country->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>

        <div class="rm-card">

            <h2>Dispositivos</h2>

            <ul>
                <?php foreach ($devices as $device): ?>

                    <li>
                        <?= esc_html($device->dispositivo); ?>
                        -
                        <?= $device->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>

        <div class="rm-card">

            <h2>Fuentes de tráfico</h2>

            <ul>
                <?php foreach ($sources as $source): ?>

                    <li>
                        <?= esc_html($source->source); ?>
                        -
                        <?= $source->total; ?>
                    </li>

                <?php endforeach; ?>
            </ul>

        </div>
    </div>
</div>