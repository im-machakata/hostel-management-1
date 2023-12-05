<?php

use App\System\Response;
// By initialising the LoginController,
// it checks it will execute the logout function
// when it figures that the url is for logging out
// Nothing more needs to be done as it will redirect the user
// to the login page even if they were not loged in already
// include autoloader
include __DIR__ . "/../src/autoload.php";

// if session id is set, logout
if (user()->getId()) {
    session('user', null);
}

// redirect to login page
if (!user()->getId()) {
    Response::redirect('/login.php');
}
