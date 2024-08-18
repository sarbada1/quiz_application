<?php

namespace MVC\Controllers;

use MVC\Controller;
use MVC\Models\User;

class UserController extends Controller {
    public function index() {
        include 'src/Views/user/index.php';
    }
}
    