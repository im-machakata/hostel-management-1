<?php

use App\System\Request;
use App\System\Response;
use App\System\Database;

// include autoloader
include __DIR__ . "/../src/autoload.php";

// initiate db
$errors = [];
$db = new Database();

// check if user is logged / authenticated
if (!user()->getId()) {
    return Response::redirect('/login.php');
}

// get available rooms
$db->prepare('SELECT * FROM rooms WHERE is_booked = :is_booked', [
    'is_booked' => (int) true
])->execute();
$rooms = $db->getRows();

// show error if rooms array empty
if (!$rooms) :
    $errors[] = 'There\'s no booked rooms at the moment.';
endif;
render_component('head', ['title' => 'View Bookings']);
?>

<body>
    <?php render_component('menu'); ?>
    <main>
        <div class="container-fluid">
            <?php render_component('header', ['page' => 'View Bookings']); ?>
            <section class="px-0">
                <div class="row mt-2 justify-content-center">
                    <!-- Errors Layouts -->
                    <?php render_component('errors', ['errors' => $errors]); ?>

                    <?php foreach ($rooms as $room) : ?>
                        <div class="col-lg-3 mb-3">
                            <?php render_component('room', $room) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>