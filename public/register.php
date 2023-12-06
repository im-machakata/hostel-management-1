<?php
// use namesapce and classes from existing code
use App\System\Request;
use App\System\Response;
use App\System\Database;

// include autoloader file
require_once __DIR__ . '/../src/autoload.php';

// instantiate empty $errros array
$errors = [];

// if request method is post, handle login
if (Request::isPost()) {

    // get username and password from request
    $student_id = Request::post('student-id');
    $password = Request::post('password');

    // connect to database
    $db = new Database();

    // check if table exists
    if (!$table_exists = $db->tableExists('users')) {
        $errors[] = 'Database table not found. Install migration file.';
    }

    if ($table_exists) {

        // prepare and execute sql statement
        $db->prepare('SELECT * FROM users WHERE username = :username LIMIT 0,1', [
            'username' => $student_id
        ])->execute();

        // get user results from database
        if ($user = $db->getRow()) {
            $errors[] = 'Username is taken';
        }

        if (!$user) :
            // prepare and execute sql statement
            $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)', [
                'username' => $student_id,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ])->execute();

            if ($user_id = $db->lastInsertId()) {
                session('user', $user_id);
                return Response::redirect('/');
            }

            $errors = [$errors, ...$db->getErrors()];
        endif;
    }
}

render_component('head', ['title' => 'Register']); ?>

<body>
    <main class="container-fluid">
        <section class="login row jumbotron justify-content-center">
            <!-- Header -->
            <div class="col-11 col-lg-12 text-lg-center">
                <?php render_component('header', ['page' => 'Register']); ?>
            </div>

            <!-- Errors Layouts -->
            <?php render_component('errors', ['errors' => $errors]); ?>

            <!-- Register Form -->
            <div class="col-11 col-lg-6 my-2 mb-4 mt-4">
                <form action="/register.php" method="post">
                    <div class="mb-3">
                        <label for="student-id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" placeholder="Student ID" id="student-id" name="student-id" value="<?= Request::post('student-id') ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password" value="<?= Request::post('password') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success border-0 btn-lg w-100">Create Account</button>
                    <p class="mt-4 mb-1"><a href="/login.php">Login</a> to your account.</p>
                </form>
            </div>
        </section>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>