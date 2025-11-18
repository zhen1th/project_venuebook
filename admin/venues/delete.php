<?php
require "../../config/database.php";
require "../../config/session.php";
require_admin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$data = $mysqli->query("SELECT gambar FROM venues WHERE id=$id")->fetch_assoc();

if ($data && $data['gambar'] && file_exists("../../assets/images/" . $data['gambar'])) {
    unlink("../../assets/images/" . $data['gambar']);
}

$mysqli->query("DELETE FROM venues WHERE id=$id");

header("Location: index.php");
exit;