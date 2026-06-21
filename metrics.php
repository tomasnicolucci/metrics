<?php
/*
Plugin Name: Metrics
Plugin URI: 
Description: Dashboard privado de métricas para clientes, usuarios administradores de paginas web Wordpress.
Version: 1.0.0
Author: Tomas Nicolucci
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

define('RM_VERSION', '1.0.0');
define('RM_PATH', plugin_dir_path(__FILE__));
define('RM_URL', plugin_dir_url(__FILE__));

require_once RM_PATH . 'vendor/autoload.php';

require_once RM_PATH . 'includes/roles.php';
require_once RM_PATH . 'includes/installer.php';
require_once RM_PATH . 'includes/auth.php';
require_once RM_PATH . 'includes/dashboard.php';
require_once RM_PATH . 'includes/session.php';
require_once RM_PATH . 'includes/tracker.php';
require_once RM_PATH . 'includes/detector.php';
require_once RM_PATH . 'includes/geo.php';
require_once RM_PATH . 'includes/events.php';
require_once RM_PATH . 'includes/stats.php';
require_once RM_PATH . 'includes/api.php';
require_once RM_PATH . 'includes/cron.php';
require_once RM_PATH . 'includes/reports.php';
require_once RM_PATH . 'includes/config.php';

register_activation_hook(
    __FILE__,
    'rm_activate'
);

add_action(
    'wp_enqueue_scripts',
    'rm_enqueue_scripts'
);

function rm_enqueue_scripts()
{
    wp_enqueue_script(
        'rm-tracker',
        RM_URL . 'assets/js/tracker.js',
        [],
        '1.0.0',
        true
    );

    wp_localize_script(
        'rm-tracker',
        'rmTracker',
        [
            'apiUrl' => rest_url(
                'rm/v1'
            ),
            'nonce' => wp_create_nonce(
                'wp_rest'
            )
        ]
    );
}