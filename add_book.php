<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require "config.php";
require "auth.php";
checkLogin();

// INSERT NEW BOOK
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $mysqli->real_escape_string($_POST['Name']);
    $description = $mysqli->real_escape_string($_POST['Description']);
    $release_date = $mysqli->real_escape_string($_POST['Release_date']);
    $rate = $mysqli->real_escape_string($_POST['Rate']);
    $category = $mysqli->real_escape_string($_POST['Category']);

    // IMAGE UPLOAD FIX WITH ABSOLUTE PATH
    if (!empty($_FILES['image']['name'])) {

        // Create uploads folder using absolute path
        $uploadDir = __DIR__ . "/uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

        // Validate that file is an image
        if (getimagesize($imageTmp) === false) {
            die("Uploaded file is not a valid image.");
        }

        // Create unique filename
        $newImageName = uniqid("book_", true) . "." . $imageExt;

        // Absolute server path (for uploading)
        $uploadPath = $uploadDir . $newImageName;

        // Browser/DB path
        $dbImagePath = "uploads/" . $newImageName;

        // Move uploaded file to absolute server path
        if (!move_uploaded_file($imageTmp, $uploadPath)) {
            die("Error uploading image.");
        }

    } else {
        die("Image is required.");
    }

    // INSERT DATA
    $insert = "
        INSERT INTO `My books List` (`Name`, `Description`, `Release date`, `Rate`, `image`, `Category`)
        VALUES ('$name', '$description', '$release_date', '$rate', '$dbImagePath', '$category')
    ";

    $mysqli->query($insert);

    header("Location: index.php?added=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
<body>

<h2>Add New Book</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Book Name:</label>
    <input type="text" name="Name" required>

    <label>Description:</label>
    <textarea name="Description" required></textarea>

    <label>Release Date:</label>
    <input type="date" name="Release_date" required>

    <label>Rate:</label>
    <input type="number" step="0.1" name="Rate" required>

    <label>Category:</label>
    <input type="text" name="Category" required>

    <label>Image:</label>
    <input type="file" name="image" required>

    <button type="submit">Add Book</button>
</form>

<a href="index.php">Back</a>

</body>
</html>
