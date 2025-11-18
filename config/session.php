<?php
session_start();

function is_logged_in()
{
    return isset($_SESSION['user']);
}

function require_login()
{
    if (!is_logged_in()) {
        header("Location: /login.php");
        exit;
    }
}

function is_admin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === "admin";
}

function require_admin()
{
    if (!is_admin()) {
        header("Location: /index.php");
        exit;
    }
}