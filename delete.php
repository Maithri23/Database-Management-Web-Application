<?php
include("config.php");

// Get the ID from the URL
$id = $_GET['id'];

// Prepare the DELETE statement
$stmt = $conn->prepare("DELETE FROM user6_form WHERE id = ?");
$stmt->bind_param("i", $id); // "i" indicates that the parameter is an integer

// Execute the statement
if ($stmt->execute()) {
    echo "<script>alert('Record Deleted');</script>";
    // Redirect to the display page
    echo '<meta http-equiv="refresh" content="0; url=http://localhost/login/display.php" />';
} else {
    echo "<script>alert('Failed to Delete');</script>";
}

// Close the statement
$stmt->close();
?>
