<?php
class Members_model extends Model {

    /**
     * Check if a username is available.
     *
     * @param string $username The username to check.
     * @return bool|string True if available, error message if taken.
     */
    public function unique_username(string $username): bool|string {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            return 'The {label} can only contain letters and numbers.';
        }

        $user_obj = $this->db->get_one_where('username', $username, 'members');
        if ($user_obj === false) {
            return true;
        } else {
            return 'The {label} that you submitted is not available.';
        }
    }

    /**
     * Check if an email address is available.
     *
     * @param string $email_address The email address to check.
     * @return bool|string True if available, error message if taken.
     */
    public function unique_email(string $email_address): bool|string {
        $user_obj = $this->db->get_one_where('email_address', $email_address, 'members');
        if ($user_obj === false) {
            return true;
        } else {
            return 'This {label} cannot be used. Please try a different one.';
        }
    }

    /**
     * Validate password strength.
     *
     * Requires at least one letter and one number.
     *
     * @param string $password The password to validate.
     * @return bool|string True if strong enough, error message if not.
     */
    public function password_check(string $password): bool|string {
        if (!preg_match('/[A-Za-z]/', $password)) {
            return 'The {label} must contain at least one letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'The {label} must contain at least one number.';
        }
        return true;
    }

    /**
     * Get member data from posted form fields.
     *
     * @return array Associative array of form field values.
     */
    public function get_data_from_post(): array {
        $data['username'] = post('username', true);
        $data['email_address'] = post('email_address', true);
        $data['first_name'] = post('first_name', true);
        $data['last_name'] = post('last_name', true);
        return $data;
    }
}