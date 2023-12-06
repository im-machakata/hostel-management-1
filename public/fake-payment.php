<?php

use App\System\Database;
use App\System\Request;
use App\System\Response;

// include autoloader
include __DIR__ . "/../src/autoload.php";

// check if payment id is present
if (!Request::get('id')) {
    return Response::redirect('/');
}

$db = new Database();
$db->prepare('UPDATE payments SET status = :status WHERE id = :id', [
    'status' => Request::get('status') ?? 'success',
    'id' => ''
])->execute();
render_component('head', ['title' => 'Fake a payment']); ?>

<body>
    <?php render_component('menu'); ?>
    <main>
        <div class="container-fluid">
            <section class="container">
                <?php render_component('header', ['page' => 'Fake Payment']); ?>
                <div class="row justify-content-center mt-4">
                    <!-- Errors Layouts -->
                    <?php render_component('errors', ['errors' => $errors]); ?>
                    <form action="/fake-payment.php" method="get">
                        <div class="row justify-content-center mb-4">
                            <div class="col-lg-3 mb-3">
                                <div class="form-floating">
                                    <input type="text" value="<?= Request::get('id') ?>" placeholder="Payment" id="id" name="id" class="form-control">
                                    <label for="id" class="form-label">Payment ID</label>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <div class="form-floating">
                                    <input type="text" value="<?= Request::get('status') ?>" placeholder="Payment" name="status" id="status" class="form-control">
                                    <label for="status" class="form-label">Payment Status</label>
                                </div>
                            </div>
                            <div class="col-12"></div>
                            <div class="col-lg-3">
                                <button class="btn btn-outline-success w-100 btn-lg" type="submit">Process</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>