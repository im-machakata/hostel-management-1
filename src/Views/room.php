<?php

use App\System\Request;
?>
<div class="card shadow border-0">
    <div class="ratio ratio-16x9">
        <img src="<?= $image_url ?? ('/assets/images/demo.jpg') ?>" class="card-img-top img-fluid" alt="<?= $name ?> Image">
    </div>
    <div class="card-body">
        <div class="clearfix mb-1">
            <?php if (!Request::isUrl('/booked-rooms.php')) : ?>
                <div class="h5 btn-sm btn btn-primary mb-2">
                    USD $<?= number_format($cost, 2) ?>
                </div>
            <?php endif;
            if (!user()->is_student && Request::isUrl('/manage-rooms.php')) : ?>
                <div class="float-end">
                    <form action="/manage-rooms.php" method="post" class="d-block">
                        <a class="h5 btn-sm btn btn-outline-dark mb-2 text-decoration-none" href="#editRoom" data-bs-toggle="modal" data-bs-target="#newRoom" data-id="<?= $id ?>" data-name="<?= $name ?>" data-details="<?= $description ?>" data-cost="<?= $cost ?>" data-image="<?= $image_url ?>" data-booked="<?= $is_booked ?>" data-action="edit">EDIT</a>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" class="btn btn-dark btn-sm mb-2 delete-room">DELETE</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <h2 class="card-title h4"><?= $name ?></h2>
        <p class="card-text mb-0"><?= $description ?></p>
        <?php if (Request::isUrl('/booked-rooms.php')) : ?>
            <p class="mb-0">Booked By: <?= $username ?? '<strong>Uknown</strong>' ?></p>
            <div class="row g-2">
                <div class="mb-2 col-6">
                    From: <?= $booked_from ?? 'NIL' ?>
                </div>
                <div class="mb-2 col-6">
                    To: <?= $booked_to ?? 'NIL' ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($is_booked) : ?>
            <a href="/book-room.php?id=<?= $id ?>" class="btn btn-primary disabled mt-3">Room Is Booked</a>
        <?php else : ?>
            <a href="/book-room.php?id=<?= $id ?>" class="btn btn-outline-dark mt-3">Book This Room</a>
        <?php endif; ?>
    </div>
</div>