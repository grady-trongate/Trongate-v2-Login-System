<?php
$config['login'] = [

    // Global settings
    'default_view_file'     => 'login_default',
    'max_failed_attempts'   => 3,
    'block_duration'        => 900,
    'password_hash_cost'    => 11,
    'reset_token_lifespan'  => 3600,

    'user_levels' => [

        // Level 1: Administrators (built-in with framework)
        1 => [
            'target_table'            => 'trongate_administrators',
            'user_ref_field'          => 'trongate_user_id',
            'secret_login_word'       => 'tg-admin',
            'redirect_on_success'     => 'trongate_administrators/manage',
            'allow_remember'          => 0,
            'remember_days'           => 0,
            'enable_forgot_password'  => false,
            'view_file'               => 'login_default',
            'fields' => [
                'identifiers' => [
                    'username' => ['column' => 'username', 'label' => 'Username'],
                    'email'    => ['column' => 'email', 'label' => 'Email']
                ],
                'password' => [
                    'column' => 'password',
                    'label'  => 'Password'
                ]
            ]
        ],

        // Level 2: Members
        2 => [
            'target_table'            => 'members',
            'user_ref_field'          => 'trongate_user_id',
            'secret_login_word'       => 'member-login',
            'redirect_on_success'     => 'members/welcome',
            'allow_remember'          => 1,
            'remember_days'           => 30,
            'enable_forgot_password'  => true,
            'view_file'               => 'login_default',
            'fields' => [
                'identifiers' => [
                    'username' => ['column' => 'username', 'label' => 'Username'],
                    'email'    => ['column' => 'email_address', 'label' => 'Email Address']
                ],
                'password' => [
                    'column' => 'password',
                    'label'  => 'Password'
                ]
            ]
        ]
    ]
];