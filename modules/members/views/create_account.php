<h1>Create Your Account</h1>

<p>Fill in the form below to create a new member account.</p>

<div class="container-xs">
    <?php
    echo validation_errors();
    echo form_open('members/submit_create_account');

    echo form_label('Username');
    $username_attr = [
        'placeholder' => 'Choose a username...',
        'autocomplete' => 'off',
        'required' => true
    ];
    echo form_input('username', '', $username_attr);

    echo form_label('First Name');
    $first_name_attr = [
        'placeholder' => 'Enter first name...',
        'autocomplete' => 'off'
    ];
    echo form_input('first_name', '', $first_name_attr);

    echo form_label('Last Name');
    $last_name_attr = [
        'placeholder' => 'Enter last name...',
        'autocomplete' => 'off'
    ];
    echo form_input('last_name', '', $last_name_attr);

    echo form_label('Email Address');
    $email_attr = [
        'placeholder' => 'Enter email address...',
        'autocomplete' => 'off',
        'required' => true
    ];
    echo form_email('email_address', '', $email_attr);

    echo form_label('Password');
    $password_attr = [
        'placeholder' => 'Enter password...',
        'autocomplete' => 'off',
        'required' => true
    ];
    echo form_password('password', '', $password_attr);

    echo form_label('Repeat Password');
    $password_repeat_attr = [
        'placeholder' => 'Repeat password...',
        'autocomplete' => 'off',
        'required' => true
    ];
    echo form_password('password_repeat', '', $password_repeat_attr);

    echo '<div class="text-center">';
    echo anchor('member-login', 'Already have an account? Log in', ['class' => 'button alt']);
    echo form_submit('submit', 'Create Account');
    echo '</div>';

    echo form_close();
    ?>
</div>