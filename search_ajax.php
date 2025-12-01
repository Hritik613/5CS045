<?php
require "config.php";

$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : "";
$category = isset($_GET['category']) ? $mysqli->real_escape_string($_GET['category']) : "";

$query = "SELECT * FROM `My books List` WHERE 1";

if (!empty($search)) {
    $query .= " AND `Name` LIKE '%$search%'";
}

if (!empty($category)) {
    $query .= " AND `Category` = '$category'";
}

$query .= " ORDER BY `Release date` DESC";

$result = $mysqli->query($query);

// Return only the rows (NO table, NO HTML header)
while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?php echo $row['Name']; ?></td>
    <td><?php echo $row['Description']; ?></td>
    <td><?php echo $row['Release date']; ?></td>

    <td>
        <?php
        $stars = floor($row['Rate']);
        for ($i = 0; $i < $stars; $i++) echo "⭐";
        ?>
    </td>

    <td><?php echo $row['Category']; ?></td>
    <td><img src="<?php echo $row['image']; ?>"></td>

    <td><a class="details-btn" href="details.php?id=<?php echo $row['Id']; ?>">View</a></td>
    <td><a class="edit-btn" href="edit.php?id=<?php echo $row['Id']; ?>">Edit</a></td>
    <td><a class="delete-btn" href="delete.php?id=<?php echo $row['Id']; ?>">Delete</a></td>
</tr>

<?php endwhile; ?>
