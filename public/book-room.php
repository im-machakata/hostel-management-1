<?php

use App\System\Request;
use App\System\Response;
use App\System\Database;

// include autoloader
include __DIR__ . "/../src/autoload.php";

// check if user is logged / authenticated
if (!user()->getId()) {
    return Response::redirect('/login.php');
}

// check if id is present
if (!Request::get('id')) {
    return Response::redirect('/');
}

// initiate db
$errors = [];
$db = new Database();

// get available rooms
$db->prepare('SELECT * FROM rooms WHERE id = :id LIMIT 0,1', [
    'id' => Request::get('id')
])->execute();
$room = $db->getRow();

// show error if rooms array empty
if (!$room) :
    $errors[] = 'There\'s no such room.';
endif;

// send mail if room exists
if (Request::isPost() && !$errors) :

    $payment_recorded = $db->prepare('INSERT INTO payments (status, student_id, room_id, created_on, updated_on) VALUES(:status, :student_id, :room_id, :created_on, :updated_on)', [
        'status' => 'Pending',
        'student_id' => user()->getId(),
        'room_id' => $room['id'],
        'created_on' => date('Y/m/d'),
        'updated_on' => date('Y/m/d')
    ])->execute();
    $payment_id = $db->lastInsertId();

    // if an error occured while saving payment details
    // bring it to attention asap
    if (!$payment_recorded) {
        $errors[] = 'Failed to record payment.';
    }

    // todo notify user
    // send email or sms if that's what you want
    $success = mail(Request::getVar('email'), 'Room Booking Instructions', sprintf('You requested to book <strong class="fw-bold">%s</strong> on the school hostels system. If you\'d like to proceed with the booking, kindly pay a sum of USD $%s to the schools account and email us back with the proof. Remember, if you delay with the payment, the room may be snatched from right under your nose by other users.<hr class="my-4">You can use the following link to fake a <a href="http://%3$s/fake-payment.php?id=<?= %4$s ?>">successful</a> or <a href="http://%3$s/fake-payment.php?id=%4$s&status=paid">failed</a> payment to proceed with the testing.', $room['name'], $room['cost'], Request::getServer('server_name'), password_hash($payment_id, PASSWORD_DEFAULT)));
    if (!$success) {
        $errors[] = 'Failed. Make sure server is connected and setup to send emails.';
    }

endif;

render_component('head', ['title' => 'Book Room']);
?>

<body>
    <?php render_component('menu'); ?>
    <main>
        <?php if ($room) : ?>
            <section class="ratio ratio-16x9 room-image border-top border-success input-fix" style="max-height: 50vh;background: url(<?= $room['image_url'] ?? '/assets/images/demo.jpg' ?>) no-repeat; background-size: cover; background-position: center;"></section>
            <div class="container-fluid">
                <?php render_component('header', ['page' => 'Book']); ?>
                <section class="container-lg">
                    <div class="row justify-content-center mt-3">
                        <!-- Errors Layouts -->
                        <?php render_component('errors', ['errors' => $errors]); ?>
                        <?php if (!$errors && Request::isPost()) : ?>
                            <div class="col-11 col-lg-12 px-0">
                                <div class="alert alert-success text-lg-center mt-1 mb-4">
                                    Payment instructions have been sent to your email.
                                </div>
                            </div>
                            <div class="col-12"></div>
                        <?php endif; ?>
                        <div class="col-md-5 col-lg-4 order-md-last">
                            <h4 class="text-dark mb-3">
                                Room Details
                            </h4>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between border-success lh-sm">
                                    <div>
                                        <h6 class="my-0"><?= $room['name'] ?></h6>
                                        <small class="text-muted"><?= $room['description'] ?></small>
                                    </div>
                                    <span class="text-muted">$<?= number_format($room['cost'], 2)  ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between border-success">
                                    <span>Total (USD)</span>
                                    <strong>$<?= number_format($room['cost'], 2) ?></strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-7 col-lg-8">
                            <h4 class="mb-3">Billing address</h4>
                            <form method="post" action="/book-room.php?id=<?= Request::get('id') ?>">
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6">
                                        <label for="first-name" class="form-label">First name</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="Names" required>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="lastName" class="form-label">Last name</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Surname" required>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" placeholder="Username" value="<?= user()->username ?>" readonly>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" placeholder="1234 Main St" required>
                                    </div>
                                    <input type="hidden" name="room-id" value="<?= Request::get('id') ?>">
                                </div>

                                <button class="w-100 btn btn-success border-0 btn-lg mt-2" type="submit">Send Instructions</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        <?php else : ?>
            <div class="container-fluid">
                <?php render_component('header', ['page' => 'Book']); ?>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-6">
                        <p class="alert alert-danger border-danger text-lg-center">We do not know anything about the room you're looking for</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>