<?php

if (!defined('ABSPATH')) {
    exit;
}

define('RM_COOKIE_NAME', 'rm_session');
define('RM_COOKIE_DURATION', YEAR_IN_SECONDS);

function rm_get_session_id()
{
    if (!empty($_COOKIE[RM_COOKIE_NAME])) {
        return sanitize_text_field(
            $_COOKIE[RM_COOKIE_NAME]
        );
    }

    return rm_create_session_id();
}

function rm_create_session_id()
{
    $session_id = wp_generate_uuid4();

    setcookie(
        RM_COOKIE_NAME,
        $session_id,
        time() + RM_COOKIE_DURATION,
        COOKIEPATH ?: '/',
        COOKIE_DOMAIN,
        is_ssl(),
        true
    );

    $_COOKIE[RM_COOKIE_NAME] = $session_id;

    return $session_id;
}