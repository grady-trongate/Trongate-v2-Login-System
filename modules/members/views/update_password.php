<h1>Update Password</h1>

<p>Enter your new password below.</p>

<div class="container-xs">
    <?php
    echo validation_errors();
    echo form_open('members/update_password');

    echo form_label('New Password');
    $password_attr = [
        'placeholder' => 'Enter new password...',
        'autocomplete' => 'off',
        'required' => true,
        'id' => 'password'
    ];
    echo form_password('password', '', $password_attr);

    echo form_label('Repeat New Password');
    $password_repeat_attr = [
        'placeholder' => 'Repeat new password...',
        'autocomplete' => 'off',
        'required' => true,
        'id' => 'password_repeat'
    ];
    echo form_password('password_repeat', '', $password_repeat_attr);

    echo '<div class="text-center">';
    echo anchor('members/your_account', 'Cancel', ['class' => 'button alt']);
    echo form_submit('submit', 'Update Password');
    echo '</div>';

    echo form_close();
    ?>
</div>