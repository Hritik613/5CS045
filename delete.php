<?php 

session_start(); 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Load database + session
require "config.php";

// Load login protection
require "auth.php";
checkLogin();

$mysqli = new mysqli("localhost", "2410923", "pp2410923", "db2410923");

if (!isset($_GET['id'])) {
    die("No book selected.");
}

$id = intval($_GET['id']);

// Delete book image file if exists
$result = $mysqli->query("SELECT image FROM `My books List` WHERE ID=$id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagePath = $row['image'];

    if (!empty($imagePath) && file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete database row
$delete = $mysqli->query("DELETE FROM `My books List` WHERE ID=$id");

if ($delete) {
    header("Location: index.php?msg=deleted");
    exit();
} else {
    echo "Error deleting book: " . $mysqli->error;
}
?>
