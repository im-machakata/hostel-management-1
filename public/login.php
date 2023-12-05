<?php
// include namespaces
use App\System\Request;
use App\System\Response;
use App\System\Database;

// include autoloader file
require_once __DIR__ . '/../src/autoload.php';

// redirect user if logged in
if (session('user')) {
    return Response::redirect('/');
}

// instantiate empty $errors array
$errors = [];

// if request method is post, handle login
if (Request::isPost()) {

    // get username and password from request
    $username = Request::post('username');
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
            'username' => $username
        ])->execute();

        // get user results from database
        if (!$user = $db->getRow()) {
            $errors[] = 'Invalid username or password';
        } else {
            if (!password_verify($password, $user['password'])) {
                $errors[] = 'Invalid username or password';
            } else {
                session('user', $user['id']);
                return Response::redirect('/');
            }
        }
    }
}

// render head tags from components
render_component('head', ['title' => 'Login']);
?>

<body>
    <main class="container-fluid">
        <section class="login row justify-content-center">
            <!-- Header -->
            <div class="col-11 col-lg-12 text-lg-center">
                <?php render_component('header', ['page' => 'Login']); ?>
            </div>

            <!-- Errors Layouts -->
            <?php render_component('errors', ['errors' => $errors]); ?>

            <!-- Login Form -->
            <div class="col-11 col-lg-6 my-2 rounded py-3 p-lg-4 mb-4">
                <form action="/login.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" placeholder="Username" id="username" name="username" value="<?= Request::getVar('username') ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password" value="<?= Request::getVar('password') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 border-0">Login</button>
                    <p class="mt-4 mb-1"><a href="/register.php">Create account</a> for free.</p>
                </form>
            </div>
        </section>
    </main>

    <?php render_component('footer'); ?>
</body>

</html>