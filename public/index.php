<?php
// include autoload file
require_once __DIR__.'/../src/autoload.php';
render_component('head', ['title' => 'Home']);
?>

<body>
    <?php render_component('menu'); ?>
    <main class="container-fluid">
        <?php render_component('header', ['page' => 'Dashboard']); ?>
        <section class="row justify-content-center">
            <div class="col-12"></div>
            <?php foreach ($available_rooms as $room) : ?>
                <div class="col-sm-6 col-lg-3">
                    <?php render_component('room', $room); ?>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>