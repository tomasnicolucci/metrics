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
            'read' => true,
            'view_rm_dashboard' => true
        ]
    );

    $admin = get_role(
        'administrator'
    );

    if ($admin) {

        $admin->add_cap(
            'view_rm_dashboard'
        );
    }

    $role = get_role(
        'cliente_metricas'
    );

    if ($role) {

        $role->add_cap(
            'view_rm_dashboard'
        );
    }
}