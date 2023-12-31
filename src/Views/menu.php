<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/">Home</a>
        <button class="navbar-toggler bg-info" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!user()->is_student) : ?>
                    <li class="nav-item">
                        <a class="nav-link<?= url_active('/booked-rooms.php') ?>" href="/booked-rooms.php">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_active('/manage-rooms.php') ?>" href="/manage-rooms.php">Rooms</a>
                    </li>
                <?php endif; ?>
                <?php if (user()->getId()) : ?>
                    <li class="nav-item">
                        <a class="nav-link<?= url_active('/my-account.php') ?>" href="/my-account.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_active('/logout.php') ?>" href="/logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>