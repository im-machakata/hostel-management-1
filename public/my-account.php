<?php

use App\System\Database;
use App\System\Request;
use App\System\Response;

// include autoloader
include __DIR__ . "/../src/autoload.php";

// check if user is logged / authenticated
if (!user()->getId()) {
    return Response::redirect('/login.php');
}

$errors = [];

if (Request::isPost()) {
    $db = new Database();

    // remove extra spaces
    $username = trim(Request::post('student-id'));
    $password = trim(Request::post('password'));

    // check if username is unique
    $db->prepare('SELECT id FROM users WHERE username = :username', [
        'username' => $username
    ])->execute();

    if (!$db->getRow()) {
        $db->prepare('UPDATE users SET username = :username', [
            'username' => $username
        ])->execute();

        if ($password) {
            $db->prepare('UPDATE users SET password = :password', [
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ])->execute();
        }
    }
}


render_component('head', ['title' => 'My Profile']); ?>

<body>
    <?php render_component('menu'); ?>
    <main class="container-fluid">
        <section class="login row justify-content-center">
            <!-- Header -->
            <div class="col-12 col-lg-12 text-lg-center">
                <?php render_component('header', ['page' => 'Profile']); ?>
            </div>

            <!-- Errors Layouts -->
            <?php render_component('errors', ['errors' => $errors]); ?>

            <!-- Success Layout -->
            <?php if (Request::isPost() && !$errors) : ?>
                <div class="col-11 col-lg-6">
                    <div class="alert alert-success border border-success">Your account has been updated.</div>
                </div>
                <div class="col-12"></div>
            <?php endif; ?>

            <!-- Register Form -->
            <div class="col-11 col-lg-6 my-2 p-3 p-lg-4 mb-4">
                <form action="/my-account.php" method="post">
                    <div class="mb-3">
                        <label for="student-id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" placeholder="Student ID" id="student-id" name="student-id" value="<?= user()->username ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password" value="" required>
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" value="" id="admin" <?= !user()->is_student ? 'checked' : '' ?> disabled>
                        <label class="form-check-label" for="admin">
                            Is Adminstrator
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100">Update Account</button>
                </form>
            </div>
        </section>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>