<?php

use App\System\Database;
use App\System\Response;

// include autoload file
require_once __DIR__ . '/../src/autoload.php';

// if session id is not set, 
// redirect to login page
if (!user()->getId()) {
    Response::redirect('/login.php');
}

// initiate db
$db = new Database();

// get available rooms
$db->prepare('SELECT * FROM rooms WHERE is_booked = :is_booked', [
    'is_booked' => (int) false
])->execute();
$rooms = $db->getRows();

$errors = ['There\'s no free rooms at the moment.'];

render_component('head', ['title' => 'Home']);
?>

<body>
    <?php render_component('menu'); ?>
    <main class="container-fluid">
        <?php render_component('header', ['page' => 'Dashboard']); ?>
        <section class="row justify-content-center">
            <div class="col-12"></div>
            <?php foreach ($rooms as $room) : ?>
                <div class="col-sm-6 col-lg-3">
                    <?php render_component('room', $room); ?>
                </div>
            <?php endforeach; ?>

            <!-- Errors Layouts -->
            <?php render_component('errors', ['errors' => $errors]); ?>
        </section>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>