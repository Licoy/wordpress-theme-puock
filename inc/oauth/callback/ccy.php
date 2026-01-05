<?php

require_once dirname(__DIR__) . '/../../../../../wp-load.php';

$type = sanitize_key($_GET['pk_type'] ?? '');
$redirect = $_GET['redirect'] ?? '';
if (empty($redirect)) {
    $redirect = home_url('/');
}

pk_oauth_callback_execute($type, $redirect);
wp_die();
