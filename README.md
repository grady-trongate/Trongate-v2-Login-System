# Trongate v2 Login System

A complete member authentication system for **Trongate v2**, demonstrating best-practice login workflows for **member accounts** — registration, login, logout, remember-me cookies, forgot-password flows, and account management.

This repository provides the finished code for the *How To Build A Login System* chapter of **The Trongate Way** — a step-by-step guide to building member authentication using Trongate v2's config-driven login module.

> **Administrators** are a special case in Trongate v2. The framework ships with a ready-made `trongate_administrators` module that provides login, CRUD, and `make_sure_allowed()` security out of the box. You do not need to build that from scratch. This repository focuses on what you **do** need to build: the member side.

## Features

- **Member registration** — Create accounts with username, email, and password
- **Config-driven authentication** — Define member settings in a single `config/login.php` file
- **Secret login words** — Clean member login URL (`/member-login`) instead of numeric level IDs
- **Dual identifier login** — Members can log in with either their username or email address
- **Rate limiting** — Brute-force protection with configurable max failed attempts and block duration
- **Remember-me cookies** — Persistent session support for member accounts (30 days)
- **Forgot-password flow** — Time-limited reset tokens with email notifications
- **Bcrypt password hashing** — Configurable cost factor (default 11), always using `$this->login->hash_password()`
- **Account management** — Protected dashboard, profile update, and password change
- **Validation callbacks** — Unique username/email checks and password strength enforcement

## Files Included

```
config/
  login.php              — Member user level definition and security settings
  custom_routing.php     — Maps member-login secret word to the login module

modules/
  members/
    Members.php                      — Member controller (login, logout, registration,
                                       account management)
    Members_model.php                — Member validation utilities
    views/
      welcome.php         — Landing page after successful login
      create_account.php  — Registration form
      your_account.php    — Protected account dashboard
      update_password.php — Password change form

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
   - Download the framework from GitHub: [trongate/trongate-framework](https://github.com/trongate/trongate-framework)
   - Follow the installation guide at [trongate.io/documentation](https://trongate.io/documentation)

2. **Copy the configuration files**:
   ```bash
   cp config/login.php /path/to/your/project/config/login.php
   # Merge the routes from config/custom_routing.php into your
   # project's config/custom_routing.php
   ```

3. **Add the members module**:
   ```bash
   cp -r modules/members /path/to/your/project/modules/members
   ```

4. **Create the database tables**:
   ```bash
   mysql -u your_user -p your_database < login.sql
   ```

5. **Configure the Trongate email module** (for forgot-password):
   - Create `config/trongate_email.php` with your SMTP credentials
   - See the [Trongate Email documentation](https://trongate.io/documentation) for details

## URL Routes

| URL | Purpose |
|-----|---------|
| `/member-login` | Member login form |
| `/members/welcome` | Member home page (after login) |
| `/members/logout` | Log out of member session |
| `/members/create_account` | Registration form |
| `/members/submit_create_account` | Process registration |
| `/members/your_account` | Protected account dashboard |
| `/members/update_password` | Change member password |
| `/login/forgot_password/member-login` | Member forgot-password flow |

## Architecture

The member authentication system has four components that work together:

1. **`config/login.php`** — Defines the member user level, target table (`members`), login identifiers (`username`, `email_address`), and security settings (remember-me, forgot-password, rate limiting)
2. **`config/custom_routing.php`** — Maps the `member-login` secret word to the login module's controller
3. **`modules/login/Login.php`** — The core login module (included with Trongate v2) that handles authentication, credential validation, token creation, rate limiting, and forgot-password
4. **`modules/members/Members.php`** — Your custom controller with the welcome page, registration, account management, and logout

### How the Login Flow Works

1. Visitor navigates to `/member-login`
2. Custom routing forwards to `login/login/member-login`
3. The login module resolves the secret word to user level 2 (Members)
4. If the member already has a valid token, they are redirected to `members/welcome`
5. Otherwise, the login module renders the login form
6. On submission, the login module validates credentials, enforces rate limiting, and creates a session token
7. On success, the member is redirected to `members/welcome`

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

## Key Principles

- The login module is **config-driven** — you configure, not code authentication logic
- Each user level gets its own **database table** linked via `trongate_users`
- **Secret login words** require matching entries in `custom_routing.php`
- **Members** route through the login module directly; **administrators** use their own controller
- Always use **`$this->login->hash_password()`** to hash passwords
- Protect member-only pages with **`attempt_get_valid_token()`** or **`is_logged_in()`**
- The **forgot-password** flow is built into the login module — enable it in config and configure SMTP

## License

Released under the same open-source license as the Trongate framework (MIT-style — permissive and free to use).

Happy coding with Trongate! 🚀