<?php
include("config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Display Profiles</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #56d8ff, #457b9d, #a8dadc, #e63946);
            background-size: 300% 300%;
            animation: gradientCycle 15s ease infinite;
            min-height: 100vh;
        }

        @keyframes gradientCycle {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        h2 {
            color: white;
            text-align: center;
            margin-top: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        table {
            background-color: rgba(255, 255, 255, 0.95);
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1.5s ease-out;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ccc;
            font-size: 0.95em;
        }

        th {
            background-color: #f39c12;
            color: white;
            text-transform: uppercase;
        }

        td a {
            color: #0077cc;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        .update, .delete {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 14px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .delete {
            background: linear-gradient(45deg, #c0392b, #e74c3c);
        }

        .update:hover, .delete:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        img {
            border-radius: 50%;
        }
    </style>
</head>
<body>

<?php
$query = "SELECT * FROM user6_form";
$data = mysqli_query($conn, $query);
$total = mysqli_num_rows($data);

if ($total != 0) {
    echo '<h2>Displaying All Profiles</h2>';
    echo '<center><table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Contact Number</th>
            <th>Email</th>
            <th>LinkedIn</th>
            <th>Portfolio</th>
            <th>Job Title</th>
            <th>Professional Summary</th>
            <th>Work Experience</th>
            <th>Hard Skills</th>
            <th>Soft Skills</th>
            <th>Languages</th>
            <th>Education</th>
            <th>Projects</th>
            <th>Professional Affiliations</th>
            <th>Volunteer Experience</th>
            <th>Publications</th>
            <th>Awards</th>
            <th>Certifications</th>
            <th>Profile Picture</th>
            <th>Operations</th>
        </tr>';

    while ($result = mysqli_fetch_assoc($data)) {
        echo "<tr>
                <td>" . $result['id'] . "</td>
                <td>" . htmlspecialchars($result['name']) . "</td>
                <td>" . htmlspecialchars($result['contact_number']) . "</td>
                <td>" . htmlspecialchars($result['email']) . "</td>
                <td><a href='" . htmlspecialchars($result['linkedin_id']) . "' target='_blank'>" . htmlspecialchars($result['linkedin_id']) . "</a></td>
                <td><a href='" . htmlspecialchars($result['portfolio']) . "' target='_blank'>" . htmlspecialchars($result['portfolio']) . "</a></td>
                <td>" . htmlspecialchars($result['job_title']) . "</td>
                <td>" . nl2br(htmlspecialchars($result['professional_summary'])) . "</td>
                <td>" . nl2br(htmlspecialchars($result['work_experience'])) . "</td>
                <td>" . htmlspecialchars($result['hard_skills']) . "</td>
                <td>" . htmlspecialchars($result['soft_skills']) . "</td>
                <td>" . htmlspecialchars($result['languages']) . "</td>
                <td>" . nl2br(htmlspecialchars($result['education'])) . "</td>
                <td>" . nl2br(htmlspecialchars($result['projects'])) . "</td>
                <td>" . nl2br(htmlspecialchars($result['professional_affiliations'])) . "</td>
                <td>" . nl2br(htmlspecialchars($result['volunteer_experience'])) . "</td>
                <td>" . nl2br(htmlspecialchars($result['publications'])) . "</td>
                <td>" . nl2br(htmlspecialchars($result['awards'])) . "</td>
                <td>";
                
        $certifications = explode(',', $result['certifications']);
        foreach ($certifications as $cert) {
            if (!empty($cert)) {
                echo "<a href='uploaded_certifications/" . htmlspecialchars($cert) . "' target='_blank'>" . htmlspecialchars($cert) . "</a><br>";
            }
        }

        echo "</td><td>";
        if (!empty($result['image'])) {
            echo "<img src='uploaded_img/" . htmlspecialchars($result['image']) . "' alt='Profile Picture' width='50' height='50'>";
        } else {
            echo "<img src='images/default-avatar.png' alt='Default Avatar' width='50' height='50'>";
        }

        echo "</td>
            <td>
                <a href='update_profile.php?id=" . $result['id'] . "'><button class='update'>Update</button></a>
                <a href='delete.php?id=" . $result['id'] . "' onclick='return confirmDelete()'><button class='delete'>Delete</button></a>
            </td>
        </tr>";
    }

    echo "</table></center>";
} else {
    echo "<h2 align='center'>No Records Found</h2>";
}
?>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this record?');
    }
</script>

</body>
</html>
