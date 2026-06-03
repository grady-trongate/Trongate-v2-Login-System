<?php
$routes = [

    // Member route through the login module
    "member-login"                 => "login/login/member-login",

    // Admin route through the admin controller (built-in)
    "tg-admin"                     => "trongate_administrators/login",
    "tg-admin/submit_login"        => "trongate_administrators/submit_login",
];
define('CUSTOM_ROUTES', $routes);