<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location: /admin/login');
    }
echo "hello dashboard";
