<?php
// use namesapce and classes from existing code
use App\System\Request;

// include autoloader file
require_once __DIR__ . '/../src/autoload.php';

// instantiate empty $errros array
$errors = [];

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
                    <button type="submit" class="btn btn-success btn-lg w-100">Login</button>
                    <p class="mt-4 mb-1 text-lg-center">Don't have an account? <a href="/create-account.php">Create one</a> for free.</p>
                </form>
            </div>
        </section>
    </main>

    <?php render_component('footer'); ?>
</body>

</html>