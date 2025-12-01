<?php
$mysqli = new mysqli("localhost", "2410923", "pp2410923", "db2410923");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
