<?php
/**
 * Login Module Configuration
 *
 * Defines authentication rules per user level. Each user level maps to a
 * target database table and specifies which columns serve as login identifiers,
 * where to redirect after login, and which view file to use for the login form.
 *
 * Expected location: APPPATH . '/config/login.php'
 *
 * @see https://trongate.io/documentation
 */
$config['login'] = [

    // -----------------------------------------------------------------
    // Global settings (applied to all user levels)
    // -----------------------------------------------------------------

    // Fallback view file if a user level doesn't specify its own
    'default_view_file' => 'login_default',

    // When at least one user level defines a secret_login_word, numeric
    // level IDs in the URL are no longer accepted — the correct secret
    // word must be present to reach that level's login form.

    // Number of failed login attempts before the account is temporarily blocked
    'max_failed_attempts' => 3,

    // How long an account stays blocked after exceeding max_failed_attempts (seconds)
    'block_duration' => 900,

    // Bcrypt cost factor for password hashing (higher = more secure but slower)
    'password_hash_cost' => 11,

    // Lifespan of a password-reset token (seconds)
    'reset_token_lifespan' => 3600,

    // -----------------------------------------------------------------
    // User-level definitions
    // -----------------------------------------------------------------
    //
    // The array key matches a row ID in the `trongate_user_levels` table.
    //
    // Each level defines:
    //
    //   target_table            — The database table that stores this level's user records.
    //
    //   user_ref_field          — The column in target_table that holds a foreign key
    //                             referencing `trongate_users.id`.
    //
    //   secret_login_word       — A unique word used in the URL (creates clean login URLs).
    //
    //   redirect_on_success     — Where the user is sent after a successful login
    //                             (format: "module/method").
    //
    //   allow_remember          — Whether "Remember Me" is offered on the login form
    //                             (0 = no, 1 = yes).
    //
    //   remember_days           — How long the "Remember Me" cookie lasts (in days).
    //                             Only relevant when allow_remember is 1.
    //
    //   enable_forgot_password  — Whether the forgot-password flow is enabled for this level
    //                             (true or false).
    //
    //   view_file               — The view file used to render the login form for this level.
    //                             Looked up in modules/login/views/.
    //
    //   fields.identifiers      — Columns that can be used to identify the user during login.
    //
    //   fields.password         — The column that stores the hashed password.

    'user_levels' => [

        // ── Administrator accounts (level 1) ────────────────────────
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
                    'username' => [
                        'column' => 'username',
                        'label'  => 'Username'
                    ],
                    'email' => [
                        'column' => 'email',
                        'label'  => 'Email'
                    ]
                ],
                'password' => [
                    'column' => 'password',
                    'label'  => 'Password'
                ]
            ]
        ],

        // ── Member accounts (level 2) ──────────────────────────────
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
                    'username' => [
                        'column' => 'username',
                        'label'  => 'Username'
                    ],
                    'email' => [
                        'column' => 'email_address',
                        'label'  => 'Email Address'
                    ]
                ],
                'password' => [
                    'column' => 'pword',
                    'label'  => 'Password'
                ]
            ]
        ]
    ]
];
