<?php
class Members extends Trongate {

    /**
     * Display the welcome page.
     * Members are redirected here after logging in.
     *
     * @return void
     */
    public function welcome(): void {
        $data['view_module'] = 'members';
        $data['view_file'] = 'welcome';
        $this->templates->public($data);
    }

    /**
     * Log the member out.
     *
     * @return void
     */
    public function logout(): void {
        $this->trongate_tokens->destroy();
        redirect('member-login');
    }

    /**
     * Display the registration form.
     *
     * @return void
     */
    public function create_account(): void {
        $data['view_module'] = 'members';
        $data['view_file'] = 'create_account';
        $this->templates->public($data);
    }

    /**
     * Process registration form submission.
     *
     * Validates input, creates trongate_users and members records,
     * and redirects to the login page.
     *
     * @return void
     */
    public function submit_create_account(): void {
        $this->validation->set_rules('username', 'username', 'required|min_length[3]|max_length[55]|callback_unique_username');
        $this->validation->set_rules('email_address', 'email address', 'required|valid_email|max_length[255]|callback_unique_email');
        $this->validation->set_rules('password', 'password', 'required|min_length[5]|max_length[35]|callback_password_check');
        $this->validation->set_rules('password_repeat', 'password repeat', 'required|matches[password]');

        $result = $this->validation->run();

        if ($result === true) {
            // Create the trongate_users record
            $sql = 'INSERT INTO trongate_users (user_level_id, code) VALUES (:user_level_id, :code)';
            $params = [
                'user_level_id' => 2,
                'code' => make_rand_str(32)
            ];
            $this->db->query_bind($sql, $params);
            $trongate_user_id = $this->db->insert_id();

            // Create the members record
            $data['username'] = post('username', true);
            $data['email_address'] = post('email_address', true);
            $data['password'] = $this->login->hash_password(post('password'));
            $data['first_name'] = post('first_name', true);
            $data['last_name'] = post('last_name', true);
            $data['trongate_user_id'] = $trongate_user_id;
            $data['date_created'] = time();
            $data['num_logins'] = 0;
            $data['last_login'] = 0;
            $data['code'] = make_rand_str(16);
            $data['ip_address'] = '';

            $this->db->insert($data, 'members');

            set_flashdata('Your account has been created. Please log in.');
            redirect('member-login');
        }

        // Validation failed — redisplay the form with errors
        $data['view_module'] = 'members';
        $data['view_file'] = 'create_account';
        $this->templates->public($data);
    }

    /**
     * Display the member's account dashboard.
     *
     * @return void
     */
    public function your_account(): void {
        $token = $this->trongate_tokens->attempt_get_valid_token(2);

        if ($token === false) {
            redirect('member-login');
        }

        $params = ['trongate_user_id' => $token->user_id];
        $sql = 'SELECT * FROM members WHERE trongate_user_id = :trongate_user_id';
        $member = $this->db->query_bind($sql, $params, 'object');

        if (empty($member)) {
            redirect('member-login');
        }

        $data = (array) $member[0];
        $data['view_module'] = 'members';
        $data['view_file'] = 'your_account';
        $this->templates->public($data);
    }

    /**
     * Update the member's password.
     *
     * @return void
     */
    public function update_password(): void {
        $token = $this->trongate_tokens->attempt_get_valid_token(2);

        if ($token === false) {
            redirect('member-login');
        }

        $this->validation->set_rules('password', 'password', 'required|min_length[5]|max_length[35]|callback_password_check');
        $this->validation->set_rules('password_repeat', 'password repeat', 'required|matches[password]');

        $result = $this->validation->run();

        if ($result === true) {
            $hashed_password = $this->login->hash_password(post('password'));

            $params = ['trongate_user_id' => $token->user_id];
            $sql = 'SELECT id FROM members WHERE trongate_user_id = :trongate_user_id';
            $member = $this->db->query_bind($sql, $params, 'object');
            $update_id = $member[0]->id;

            $this->db->update($update_id, ['password' => $hashed_password], 'members');

            set_flashdata('Your password has been updated.');
            redirect('members/your_account');
        }

        $data['view_module'] = 'members';
        $data['view_file'] = 'update_password';
        $this->templates->public($data);
    }

    // ──────────────────────────────────────────────
    //  Validation Callbacks
    // ──────────────────────────────────────────────

    /**
     * Validate unique username.
     */
    public function unique_username(string $username): bool|string {
        $user_obj = $this->db->get_one_where('username', $username, 'members');
        if ($user_obj !== false) {
            return 'The username that you submitted is not available.';
        }
        return true;
    }

    /**
     * Validate unique email address.
     */
    public function unique_email(string $email_address): bool|string {
        $user_obj = $this->db->get_one_where('email_address', $email_address, 'members');
        if ($user_obj !== false) {
            return 'This email address cannot be used. Please try a different one.';
        }
        return true;
    }

    /**
     * Validate password strength.
     */
    public function password_check(string $password): bool|string {
        if (!preg_match('/[A-Za-z]/', $password)) {
            return 'The password must contain at least one letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            return 'The password must contain at least one number.';
        }
        return true;
    }
}