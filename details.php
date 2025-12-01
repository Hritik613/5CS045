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

if (!isset($_GET['id'])) die("No book selected.");

$id = intval($_GET['id']);
$result = $mysqli->query("SELECT * FROM `My books List` WHERE ID=$id");

if ($result->num_rows == 0) die("Book not found.");

$book = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>Book Details</title>

<style>
body {
    font-family: Arial;
    background: var(--bg);
    color: var(--text);
    margin: 0;
}

/* Light + Dark Mode Variables */
:root {
    --bg: #f2f2f2;
    --text: #000;
    --container: #fff;
}
body.dark {
    --bg: #1e1e1e;
    --text: #fff;
    --container: #2c2c2c;
}

/* Navbar */
.navbar {
    background: var(--container);
    padding: 15px 25px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.navbar h2 {
    margin: 0;
}

/* Layout */
.container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
}

.card {
    background: var(--container);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
}

img {
    width: 260px;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Buttons */
.back-btn {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    display: inline-block;
    margin-top: 20px;
}
.back-btn:hover {
    background: #005fcc;
}
</style>
</head>

<body>

<div class="navbar">
    <h2>📘 Book Details</h2>
</div>

<div class="container">
    <div class="card">

        <img src="<?php echo $book['image']; ?>" />

        <h2><?php echo $book['Name']; ?></h2>

        <p><strong>Description:</strong><br><?php echo $book['Description']; ?></p>

        <p><strong>Release Date:</strong> <?php echo $book['Release date']; ?></p>

        <p><strong>Rate:</strong> ⭐ <?php echo $book['Rate']; ?></p>

        <p><strong>Category:</strong> <?php echo $book['Category']; ?></p>

        <a href="index.php" class="back-btn">⬅ Back</a>

    </div>
</div>

<script>
// Dark mode sync
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark");
}
</script>

</body>
</html>
