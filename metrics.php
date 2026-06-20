<?php
/*
Plugin Name: Metrics
Plugin URI: 
Description: Dashboard privado de métricas para clientes.
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

require_once RM_PATH . 'includes/roles.php';
require_once RM_PATH . 'includes/installer.php';
require_once RM_PATH . 'includes/auth.php';
require_once RM_PATH . 'includes/dashboard.php';

register_activation_hook(
    __FILE__,
    'rm_activate'
);