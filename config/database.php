<?php
$mysqli = new mysqli("localhost", "root", "", "venuebook");

if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}