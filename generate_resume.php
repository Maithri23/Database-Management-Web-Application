<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

// Fetch user data from the database
$sql = "SELECT * FROM `user6_form` WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("User  not found.");
}

// Set headers to download the file as a Word document
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=resume.doc");

// Create the HTML content for the Word document
echo "<html>";
echo "<head>";
echo "<meta charset='utf-8'>";
echo "<title>Resume</title>";
echo "<style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; font-size: 12px; }
        .container { width: 800px; margin: auto; }
        .header { background-color: #f5f5f5; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 2em; }
        .header p { margin: 5px 0; }
        .section { margin: 20px 0; }
        .section-title { font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #333; padding-bottom: 5px; }
        .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .table th, .table td { padding: 8px; text-align: left; }
        .table th { border-bottom: 2px solid #333; }
        .skills { display: flex; justify-content: space-between; }
        .skills div { width: 48%; }
        .languages { margin: 10px 0; }
      </style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";

// Header Section
echo "<div class='header'>";
echo "<h1>" . htmlspecialchars($user['name']) . "</h1>";
echo "<p>" . htmlspecialchars($user['email']) . " | " . htmlspecialchars($user['contact_number']) . "</p>";
echo "<p>" . htmlspecialchars($user['linkedin_id']) . " | " . htmlspecialchars($user['portfolio']) . "</p>";
echo "</div>";

// Profile Section
echo "<div class='section'>";
echo "<div class='section-title'>Profile</div>";
echo "<p>" . nl2br(htmlspecialchars($user['professional_summary'])) . "</p>";
echo "</div>";

// Education Section
echo "<div class='section'>";
echo "<div class='section-title'>Education</div>";
echo "<table class='table'>";
echo "<tr><th>Degree</th><th>Institution</th><th>Location</th><th>Graduation Date</th><th>GPA</th></tr>";

// Split the education data into an array
$education_entries = explode('; ', $user['education']);
foreach ($education_entries as $entry) {
    preg_match('/(.*) at (.*), (.*) \(Graduation: (.*), GPA: (.*)\)/', $entry, $matches);
    if (count($matches) === 6) {
        echo "<tr style='border: none;'>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[1]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[2]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[3]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[4]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[5]) . "</td>";
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";

// Experience Section
echo "<div class='section'>";
echo "<div class='section-title'>Experience</div>";
echo "<p>" . nl2br(htmlspecialchars($user['work_experience'])) . "</p>";
echo "</div>";

// Skills Section
echo "<div class='section'>";
echo "<div class='section-title'>Skills</div>";
echo "<div class='skills'>";
echo "<div><strong>Hard Skills:</strong><br>" . nl2br(htmlspecialchars($user['hard_skills'])) . "</div>";
echo "<div><strong>Soft Skills:</strong><br>" . nl2br(htmlspecialchars($user['soft_skills'])) . "</div>";
echo "</div>";
echo "</div>";

// Languages Section
echo "<div class='section languages'>";
echo "<div class='section-title'>Languages</div>";
echo "<p>" . nl2br(htmlspecialchars($user['languages'])) . "</p>";
echo "</div>";

// Projects Section
echo "<div class='section'>";
echo "<div class='section-title'>Projects</div>";
echo "<table class='table'>";
echo "<tr><th>Project Name</th><th>Role</th><th>Technologies</th><th>Description</th><th>Achievements</th></tr>";

// Split the project data into an array
$project_entries = explode('; ', $user['projects']);
foreach ($project_entries as $entry) {
    preg_match('/(.*) \(Role: (.*), Technologies: (.*), Description: (.*), Achievements: (.*)\)/', $entry, $matches);
    if (count($matches) === 6) {
        echo "<tr style='border: none;'>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[1]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[2]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[3]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[4]) . "</td>";
        echo "<td style='border: none;'>" . htmlspecialchars($matches[5]) . "</td>";
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";



echo "</div>"; // Close container
echo "</body>";
echo "</html>";

// Close the database connection
mysqli_close($conn);
?>