<?php

if (!defined('ABSPATH')) {
    exit;
}

use Detection\MobileDetect;

function rm_get_device()
{
    $detect = new MobileDetect();

    if ($detect->isTablet()) {
        return 'Tablet';
    }

    if ($detect->isMobile()) {
        return 'Mobile';
    }

    return 'Desktop';
}

function rm_get_browser()
{
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (stripos($agent, 'Edg') !== false) {
        return 'Edge';
    }

    if (stripos($agent, 'Chrome') !== false) {
        return 'Chrome';
    }

    if (stripos($agent, 'Firefox') !== false) {
        return 'Firefox';
    }

    if (
        stripos($agent, 'Safari') !== false
        &&
        stripos($agent, 'Chrome') === false
    ) {
        return 'Safari';
    }

    return 'Other';
}