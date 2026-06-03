# Trongate v2 Login System

A complete, config-driven multi-level authentication system for **Trongate v2**, demonstrating best-practice login workflows for **administrators** and **members**.

This repository provides the finished code for the *How To Build A Login System* chapter of **The Trongate Way** — a step-by-step guide to building secure, production-ready authentication without writing a single line of authentication logic.

## Features

- **Multi-level authentication** — Administrators (level 1) and Members (level 2), each with their own database table, login identifiers, and redirect destinations
- **Config-driven design** — Define user levels, identifiers, password fields, and security settings in a single `config/login.php` file
- **Secret login words** — Clean, human-readable login URLs (`/tg-admin`, `/member-login`) instead of numeric level IDs
- **Rate limiting** — Brute-force protection with configurable max failed attempts and block duration (15 minutes by default)
- **Remember-me cookies** — Persistent session support for member accounts (30 days)
- **Forgot-password flow** — Time-limited reset tokens with email notifications (configurable per level)
- **Bcrypt password hashing** — Configurable cost factor (default 11)
- **Session management** — Token-based authentication with automatic expiry

## Files Included

```
config/
  login.php              — User level definitions and security settings
  custom_routing.php     — Maps secret login words to controllers

modules/
  trongate_administrators/
    Trongate_administrators.php         — Admin controller (login, logout, CRUD)
    Trongate_administrators_model.php   — Admin database operations
    views/
      create.php          — Admin account create/edit form
      delete_conf.php     — Delete confirmation page
      manage.php          — Paginated admin list
      not_found.php       — Record not found page
      show.php            — Admin account detail view
      update_password.php — Password update form

login.sql — Complete database schema (trongate_user_levels, trongate_users,
            trongate_tokens, trongate_administrators, members)
```

## Prerequisites

- Trongate v2 framework (latest version recommended)
- PHP 8.0+
- MySQL / MariaDB database
- Web server with URL rewriting enabled

Visit the official site: [trongate.io](https://trongate.io)

## Installation

1. **Install Trongate v2** (if not already done):
   - Download the framework from GitHub: [trongate-framework](https://github.com/trongate/trongate-framework)
   - Follow the installation guide at [trongate.io/documentation](https://trongate.io/documentation)

2. **Copy the configuration files**:
   ```bash
   # Copy the login configuration
   cp config/login.php /path/to/your/project/config/login.php

   # Merge custom routing with your existing routes
   # Add the routes from config/custom_routing.php to your
   # project's config/custom_routing.php
   ```

3. **Add the admin module**:
   ```bash
   # Copy the trongate_administrators module
   cp -r modules/trongate_administrators /path/to/your/project/modules/trongate_administrators
   ```

4. **Create the database tables**:
   ```bash
   mysql -u your_user -p your_database < login.sql
   ```

5. **Configure the Trongate email module** (for forgot-password):
   - Create `config/trongate_email.php` with your SMTP credentials
   - Refer to the [Trongate Email documentation](https://trongate.io/documentation) for details

## URL Routes

| URL | Purpose |
|-----|---------|
| `/tg-admin` | Admin login form |
| `/tg-admin/submit_login` | Admin login submission |
| `/trongate_administrators/manage` | Admin management panel (requires login) |
| `/trongate_administrators/create` | Create a new admin account |
| `/trongate_administrators/show/{id}` | View admin account details |
| `/trongate_administrators/logout` | Log out of admin session |
| `/member-login` | Member login form |
| `/members/welcome` | Member home page (after login) |
| `/login/forgot_password/member-login` | Member forgot-password flow |

## Architecture

The login system has four components that work together:

1. **`config/login.php`** — Defines user levels, their target tables, login identifiers, and security settings
2. **`config/custom_routing.php`** — Maps secret login words to the correct authentication flow
3. **`modules/login/Login.php`** — The core login controller that handles authentication, password resets, and session checks (included with Trongate v2)
4. **`modules/trongate_administrators/Trongate_administrators.php`** — The admin panel controller that integrates with the login module

### Database Relationships

```
trongate_user_levels ── defines roles (Administrator, Member)
       │
trongate_users ── shared identity registry (one row per real user)
       │
       ├── trongate_administrators (level 1) ── linked via trongate_user_id
       │
       └── members (level 2) ── linked via trongate_user_id

trongate_tokens ── session tokens linking user_id + user_level_id
```

## License

Released under the same open-source license as the Trongate framework (MIT-style — permissive and free to use).

Happy coding with Trongate! 🚀
