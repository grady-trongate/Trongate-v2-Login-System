<?php
/**
 * Custom Routing Configuration
 *
 * Maps secret login words to the correct authentication controllers.
 * The route keys must match the secret_login_word values in config/login.php
 * exactly, otherwise the login module will fail to resolve user levels.
 *
 * Expected location: APPPATH . '/config/custom_routing.php'
 */
$routes = [

    // Admin routes through the admin controller
    "tg-admin"                     => "trongate_administrators/login",
    "tg-admin/submit_login"        => "trongate_administrators/submit_login",

    // Member routes through the login module
    "member-login"                 => "login/login/member-login",
];
define('CUSTOM_ROUTES', $routes);
