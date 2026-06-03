<?php
class Trongate_administrators_model extends Model {

    private string $table_name = 'trongate_administrators';

    /**
     * Find a single record based on a column value.
     *
     * @param string $column The column name to match
     * @param mixed $value The value to match
     * @return object|bool Returns record object if found, false otherwise
     */
    private function find_one(string $column, $value): object|bool {
        $sql = "SELECT * FROM {$this->table_name} WHERE {$column} = :{$column} LIMIT 1";
        $params = [$column => $value];

        $rows = $this->db->query_bind($sql, $params, 'object');
        return !empty($rows) ? $rows[0] : false;
    }

    /**
     * Get data from database for a specific record.
     *
     * @param int $record_id The record ID to fetch
     * @return array|bool Record data formatted for form display, or false if not found
     */
    public function get_data_from_db(int $record_id): array|bool {
        $record = $this->find_one('id', $record_id);

        if ($record === false) {
            return false;
        }

        return (array) $record;
    }

    /**
     * Get raw data from POST request.
     *
     * @return array Raw POST data
     */
    public function get_data_from_post(): array {
        return [
            'username' => post('username', true),
            'active'   => post('active', true)
        ];
    }

    /**
     * Convert posted administrator data for database storage.
     *
     * @param array $post_data Raw POST data
     * @return array Database-ready data
     */
    public function convert_posted_data_for_db(array $post_data): array {
        $data = $post_data;
        $data['active'] = (int) (bool) $data['active'];
        return $data;
    }

    /**
     * Update a record in the database.
     *
     * @param int $update_id The ID of the record to update
     * @param array $data The data to update
     * @return bool True if successful
     */
    public function update(int $update_id, array $data): bool {
        return $this->db->update($update_id, $data, $this->table_name);
    }

    /**
     * Update a user's password.
     *
     * Hashes the provided plain-text password and updates the record.
     *
     * @param int $update_id The ID of the user record to update
     * @param string $new_password The new plain-text password
     * @return bool True if successful
     */
    public function update_password(int $update_id, string $new_password): bool {
        $data['password'] = $this->hash_password($new_password);
        return $this->db->update($update_id, $data, $this->table_name);
    }

    /**
     * Hash a plain-text password using bcrypt.
     *
     * @param string $password The plain-text password to hash
     * @return string The bcrypt hash
     */
    private function hash_password(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }

    /**
     * Fetch a user record by ID.
     *
     * @param int $user_id The user identifier to look up
     * @return object|bool Returns user object if found, false otherwise
     */
    public function get_user_by_id(int $user_id): object|bool {
        return $this->find_one('id', $user_id);
    }

    /**
     * Fetch any active user record.
     *
     * Used in dev mode to auto-login without credentials.
     *
     * @return object|bool Returns the first active user object, or false if none found
     */
    public function get_any_active_user(): object|bool {
        $sql = 'SELECT * FROM ' . $this->table_name . ' WHERE active = 1 ORDER BY id ASC LIMIT 1';
        $rows = $this->db->query_bind($sql, [], 'object');
        return !empty($rows) ? $rows[0] : false;
    }

    /**
     * Log a user in by username and create an authentication token.
     *
     * @param string $username The username to log in
     * @param int $user_level_id The user level ID (1 = administrator)
     * @return string|bool The token string on success, false on failure
     */
    public function log_user_in(string $username, int $user_level_id): string|bool {
        $user_obj = $this->find_one('username', $username);

        if ($user_obj === false) {
            return false;
        }

        $token = make_rand_str(32);

        // Calculate expiry based on remember preference
        $expiry_days = ($user_level_id === 2) ? 30 : 1;
        $expiry_date = time() + 86400 * $expiry_days;

        $token_data = [
            'token' => $token,
            'user_id' => $user_obj->trongate_user_id,
            'user_level_id' => $user_level_id,
            'expiry_date' => $expiry_date,
            'code' => make_rand_str(32),
        ];

        $this->db->insert($token_data, 'trongate_tokens');

        // Store token in session and cookie
        $_SESSION['trongatetoken'] = $token;

        if ($user_level_id === 2) {
            setcookie('trongatetoken', $token, $expiry_date, '/');
        }

        return $token;
    }

    /**
     * Fetch a user record by authentication token.
     *
     * @param string $token The authentication token to look up
     * @return object|bool Returns user object if found, false otherwise
     */
    public function get_user_by_token(string $token): object|bool {
        $params['token'] = $token;
        $sql = 'SELECT
                        ' . $this->table_name . '.*
                    FROM
                        ' . $this->table_name . '
                    INNER JOIN
                        trongate_tokens
                    ON
                        ' . $this->table_name . '.trongate_user_id = trongate_tokens.user_id
                    WHERE
                        trongate_tokens.token = :token';
        $rows = $this->db->query_bind($sql, $params, 'object');

        if (!empty($rows)) {
            return $rows[0];
        }

        return false;
    }

    /**
     * Retrieve a paginated set of records from the database table.
     *
     * @param int $limit  Number of records to return
     * @param int $offset Number of records to skip
     * @return array Array of record objects
     */
    public function get_all_paginated(int $limit, int $offset): array {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY id LIMIT :limit OFFSET :offset";
        $params = [
            'limit' => $limit,
            'offset' => $offset
        ];
        return $this->db->query_bind($sql, $params, 'object');
    }

    /**
     * Prepare multiple records for display in list views.
     *
     * @param array $rows Array of record objects
     * @return array Array of prepared record objects
     */
    public function prepare_records_for_display(array $rows): array {
        $prepared = [];

        foreach ($rows as $row) {
            $prepared[] = (object) $this->prepare_for_display((array) $row);
        }

        return $prepared;
    }

    /**
     * Prepare a single record for display.
     *
     * Adds derived, human-readable values without mutating raw data.
     *
     * @param array $data Record data
     * @return array Prepared record data
     */
    public function prepare_for_display(array $data): array {
        if (array_key_exists('active', $data)) {
            $data['active_formatted'] = ((int) $data['active'] === 1)
                ? 'Active'
                : 'Not Active';
        }

        return $data;
    }

    /**
     * Count all records in the database table.
     *
     * @return int Total number of records
     */
    public function count_all(): int {
        return $this->db->count($this->table_name);
    }

    /**
     * Check if a username is available for use.
     *
     * @param string $username The username to check
     * @param int $update_id The ID of the record being updated (0 for new records)
     * @return bool Returns true if available, false if taken
     */
    public function is_username_available(string $username, int $update_id = 0): bool {
        $params['username'] = $username;

        if ($update_id > 0) {
            // For updates: check if username exists on ANOTHER record
            $sql = 'SELECT id FROM ' . $this->table_name . '
                    WHERE username = :username
                    AND id != :update_id';
            $params['update_id'] = $update_id;
        } else {
            // For creates: check if username exists at all
            $sql = 'SELECT id FROM ' . $this->table_name . '
                    WHERE username = :username';
        }

        $rows = $this->db->query_bind($sql, $params, 'object');

        return empty($rows);
    }

    /**
     * Create a new admin record with associated user account.
     *
     * Performs a two-step creation:
     * 1. Creates a record in 'trongate_users' to establish a user ID
     * 2. Creates a record in the admin table with the foreign key reference
     *
     * @param array $data Record data including username, etc.
     * @return int The ID of the newly created record
     */
    public function create_new_record(array $data): int {
        // Create trongate_users entry and get ID
        $trongate_user_data = [
            'code' => make_rand_str(32),
            'user_level_id' => 1
        ];

        $data['trongate_user_id'] = $this->db->insert($trongate_user_data, 'trongate_users');

        // Set defaults
        $data['active'] = $data['active'] ?? 1;
        $data['failed_login_attempts'] = $data['failed_login_attempts'] ?? 0;
        $data['last_failed_attempt'] = $data['last_failed_attempt'] ?? 0;
        $data['login_blocked_until'] = $data['login_blocked_until'] ?? 0;
        $data['failed_login_ip'] = $data['failed_login_ip'] ?? '';

        // Generate random password (user sets it via password reset)
        $data['password'] = $this->hash_password(make_rand_str(16));

        $new_record_id = $this->db->insert($data, $this->table_name);
        return $new_record_id;
    }

    /**
     * Delete a record and its associated user account.
     *
     * Performs a two-step deletion:
     * 1. Deletes the record from the main table
     * 2. Deletes the associated record from 'trongate_users'
     *
     * @param int $update_id The ID of the record to delete
     * @return bool True if successful
     */
    public function delete_record(int $update_id): bool {
        // Get foreign key before deletion
        $sql = 'SELECT trongate_user_id FROM ' . $this->table_name . ' WHERE id = :id';
        $params['id'] = $update_id;
        $rows = $this->db->query_bind($sql, $params, 'object');

        if (empty($rows)) {
            return false;
        }

        // Delete from main table
        $this->db->delete($update_id, $this->table_name);

        // Delete associated trongate_users record
        $trongate_user_id = $rows[0]->trongate_user_id;
        if ($trongate_user_id > 0) {
            $this->db->delete($trongate_user_id, 'trongate_users');
        }

        return true;
    }
}
