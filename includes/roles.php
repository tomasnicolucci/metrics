<?php

if (!defined('ABSPATH')) {
    exit;
}

function rm_create_role()
{
    add_role(
        'cliente_metricas',
        'Cliente Métricas',
        [
            'read' => true
        ]
    );
}