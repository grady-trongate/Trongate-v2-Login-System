<h1>Your Account</h1>

<?= flashdata() ?>

<p>Welcome back, <?= out($username) ?>.</p>

<div class="container-xs">
    <table class="table sm">
        <tbody>
            <tr>
                <td>Username</td>
                <td><?= out($username) ?></td>
            </tr>
            <tr>
                <td>First Name</td>
                <td><?= out($first_name) ?></td>
            </tr>
            <tr>
                <td>Last Name</td>
                <td><?= out($last_name) ?></td>
            </tr>
            <tr>
                <td>Email Address</td>
                <td><?= out($email_address) ?></td>
            </tr>
        </tbody>
    </table>

    <p class="text-center">
        <?= anchor('members/update_password', 'Change Password', ['class' => 'button alt']) ?>
        <?= anchor('members/logout', 'Logout', ['class' => 'button']) ?>
    </p>
</div>