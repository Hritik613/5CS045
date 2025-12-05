<?php
session_start(); 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require "config.php";
require "auth.php";
checkLogin();

// ------------------ SEARCH & FILTER ------------------
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : "";
$filterCategory = isset($_GET['category']) ? $mysqli->real_escape_string($_GET['category']) : "";

$query = "SELECT * FROM `My books List` WHERE 1";

if (!empty($search)) {
    $query .= " AND `Name` LIKE '%$search%'";
}

if (!empty($filterCategory)) {
    $query .= " AND `Category` = '$filterCategory'";
}

$query .= " ORDER BY `Release date` DESC";
$result = $mysqli->query($query);

$catQuery = $mysqli->query("SELECT DISTINCT `Category` FROM `My books List`");

// ------------------ TOP RATED BOOKS ------------------
$topBooks = $mysqli->query("
    SELECT * FROM `My books List`
    ORDER BY `Rate` DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Books List</title>

<style>
body { font-family: Arial; background: var(--bg); color: var(--text); margin: 0; transition: 0.3s; }
:root { --bg: #f2f2f2; --text: #000; --container: #fff; --nav: #fff; }
body.dark { --bg: #1e1e1e; --text: #fff; --container: #2c2c2c; --nav: #2c2c2c; }
.navbar { width: 100%; padding: 15px 30px; background: var(--nav); display: flex; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.2); position: sticky; top: 0; }
.toggle-btn { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }

.container { max-width: 1200px; margin: auto; padding: 20px; }

.book-list {
    background: var(--container); padding: 20px; border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2); margin-bottom: 20px;
    animation: fadeIn 1s forwards; opacity: 0;
}
@keyframes fadeIn { to { opacity: 1; } }

input, textarea, select {
    width: 100%; padding: 10px; margin-bottom: 15px;
    background: var(--bg); color: var(--text); border: 1px solid #666; border-radius: 5px;
}

button { padding: 12px 25px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; }
button:hover { transform: scale(1.05); }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; border-bottom: 1px solid #666; }
th { background: #007bff; color: white; }
img { width: 100px; border-radius: 8px; }

.details-btn, .edit-btn, .delete-btn {
    padding: 8px 12px; color: white; border-radius: 6px; text-decoration: none;
}
.details-btn { background: green; }
.edit-btn { background: orange; }
.delete-btn { background: red; }

.search-bar { display: flex; gap: 10px; margin-bottom: 20px; }

.slider {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding-bottom: 10px;
}

.slider::-webkit-scrollbar {
    height: 8px;
}
.slider::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.slide {
    min-width: 220px;
    background: var(--container);
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: 0.3s;
}

.slide img {
    width: 100%;
    height: 260px;
    object-fit: cover;
    border-radius: 8px;
}
.slide:hover {
    transform: scale(1.05);
}
</style>

</head>
<body>

<a href="logout.php" class="logout-btn">logout</a>

<div class="navbar">
    <h1>📚 My Books</h1>

    <!-- ⭐ Add Book Button ⭐ -->
    <a href="add_book.php">
        <button>Add New Book</button>
    </a>

    <button class="toggle-btn" onclick="toggleDarkMode()">🌙 Dark Mode</button>
</div>

<div class="container">

<!-- ⭐ TOP RATED SLIDER ⭐ -->
<div class="book-list">
    <h2>Top Rated Books ⭐</h2>

    <div class="slider" id="topBooksSlider">
        <?php while ($top = $topBooks->fetch_assoc()): ?>
            <div class="slide">
                <img src="<?php echo $top['image']; ?>">
                <h3><?php echo $top['Name']; ?></h3>
                <p>⭐ <?php echo $top['Rate']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- SEARCH & FILTER -->
<div class="book-list">
    <h2>Search & Filter</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by book name..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="category">
            <option value="">All Categories</option>
            <?php while ($c = $catQuery->fetch_assoc()): ?>
                <option value="<?php echo $c['Category']; ?>" <?php if ($c['Category'] == $filterCategory) echo "selected"; ?>>
                    <?php echo $c['Category']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search</button>
    </form>
</div>

<!-- BOOK LIST -->
<div class="book-list">
    <h2>Books List</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Release Date</th>
            <th>Rate</th>
            <th>Category</th>
            <th>Image</th>
            <th>Details</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>

        <tbody id="books-container">
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Description']; ?></td>
            <td><?php echo $row['Release date']; ?></td>

            <td>
                <?php for ($i = 0; $i < floor($row['Rate']); $i++) echo "⭐"; ?>
            </td>

            <td><?php echo $row['Category']; ?></td>
            <td><img src="<?php echo $row['image']; ?>"></td>

            <td><a class="details-btn" href="details.php?id=<?php echo $row['Id']; ?>">View</a></td>
            <td><a class="edit-btn" href="edit.php?id=<?php echo $row['Id']; ?>">Edit</a></td>
            <td><a class="delete-btn" href="delete.php?id=<?php echo $row['Id']; ?>" onclick="return confirm('Delete this book?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>

    </table>
</div>

</div>

<script>
function toggleDarkMode() {
    document.body.classList.toggle("dark");
    localStorage.setItem("darkMode", document.body.classList.contains("dark") ? "enabled" : "");
}
if (localStorage.getItem("darkMode") === "enabled") document.body.classList.add("dark");
</script>

<!-- AJAX LIVE SEARCH -->
<script>
function liveSearch() {
    let search = document.querySelector("input[name='search']").value;
    let category = document.querySelector("select[name='category']").value;

    fetch(`search_ajax.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}`)
        .then(res => res.text())
        .then(data => {
            document.getElementById("books-container").innerHTML = data;
        });
}

document.querySelector("input[name='search']").addEventListener("keyup", liveSearch);
document.querySelector("select[name='category']").addEventListener("change", liveSearch);
</script>

<!-- ⭐ AUTO-SCROLL SLIDER -->
<script>
setInterval(() => {
    document.getElementById("topBooksSlider").scrollBy({
        left: 250,
        behavior: "smooth"
    });
}, 3000);
</script>

</body>
</html>  