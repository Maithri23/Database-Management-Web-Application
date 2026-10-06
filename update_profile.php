<?php
include 'config.php';
session_start();

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    // Collect user input and sanitize
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name'] ?? '');
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email'] ?? '');
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number'] ?? '');
    $linkedin_id = mysqli_real_escape_string($conn, $_POST['linkedin_id'] ?? '');
    $portfolio = mysqli_real_escape_string($conn, $_POST['portfolio'] ?? '');
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title'] ?? '');
    $professional_summary = mysqli_real_escape_string($conn, $_POST['professional_summary'] ?? '');

    // Work Experience
    $work_experience_details = [];
    if (isset($_POST['work_experience_job_title'])) {
        foreach ($_POST['work_experience_job_title'] as $index => $job_title) {
            $company_name = mysqli_real_escape_string($conn, $_POST['work_experience_company_name'][$index] ?? '');
            $location = mysqli_real_escape_string($conn, $_POST['work_experience_location'][$index] ?? '');
            $start_date = mysqli_real_escape_string($conn, $_POST['work_experience_start_date'][$index] ?? '');
            $end_date = mysqli_real_escape_string($conn, $_POST['work_experience_end_date'][$index] ?? '');
            $responsibilities = mysqli_real_escape_string($conn, $_POST['work_experience_responsibilities'][$index] ?? '');
            $achievements = mysqli_real_escape_string($conn, $_POST['work_experience_achievements'][$index] ?? '');
            $keywords = mysqli_real_escape_string($conn, $_POST['work_experience_keywords'][$index] ?? '');

            $work_experience_details[] = "$job_title at $company_name, $location ($start_date - $end_date). Responsibilities: $responsibilities. Achievements: $achievements. Keywords: $keywords";
        }
    }
    $work_experience = implode('; ', $work_experience_details);

    // Collect and sanitize skills
    $hard_skills = isset($_POST['hard_skills']) ? mysqli_real_escape_string($conn, implode(',', $_POST['hard_skills'])) : '';
    $soft_skills = isset($_POST['soft_skills']) ? mysqli_real_escape_string($conn, implode(',', $_POST['soft_skills'])) : '';

    // Collect education details
    $education_details = [];
    if (isset($_POST['education'])) {
        foreach ($_POST['education'] as $index => $edu) {
            $institution = mysqli_real_escape_string($conn, $_POST['institution'][$index] ?? '');
            $location = mysqli_real_escape_string($conn, $_POST['edu_location'][$index] ?? '');
            $graduation_date = mysqli_real_escape_string($conn, $_POST['edu_graduation_date'][$index] ?? '');
            $gpa = mysqli_real_escape_string($conn, $_POST['edu_gpa'][$index] ?? '');

            $education_details[] = "$edu at $institution, $location (Graduation: $graduation_date, GPA: $gpa)";
        }
    }
    $education = implode('; ', $education_details);

    // Collect project details
    $project_details = [];
    if (isset($_POST['projects'])) {
        foreach ($_POST['projects'] as $index => $proj) {
            $role = mysqli_real_escape_string($conn, $_POST['project_role'][$index] ?? '');
            $technologies = mysqli_real_escape_string($conn, $_POST['project_technologies'][$index] ?? '');
            $description = mysqli_real_escape_string($conn, $_POST['project_description'][$index] ?? '');
            $achievements = mysqli_real_escape_string($conn, $_POST['project_achievements'][$index] ?? '');

            $project_details[] = "$proj (Role: $role, Technologies: $technologies, Description: $description, Achievements: $achievements)";
        }
    }
    $projects = implode('; ', $project_details);

    // Collect languages
    $languages = isset($_POST['languages']) ? mysqli_real_escape_string($conn, implode(',', $_POST['languages'])) : '';

    // Collect professional affiliations
    $professional_affiliations = mysqli_real_escape_string($conn, $_POST['professional_affiliations'] ?? '');

    // Collect volunteer experience
    $volunteer_experience = mysqli_real_escape_string($conn, $_POST['volunteer_experience'] ?? '');

    // Collect publications
    $publications = mysqli_real_escape_string($conn, $_POST['publications'] ?? '');

    // Collect awards
    $awards_details = [];
    if (isset($_POST['award_name'])) {
        foreach ($_POST['award_name'] as $index => $award_name) {
            $issuing_organization = mysqli_real_escape_string($conn, $_POST['issuing_organization'][$index] ?? '');
            $date_obtained = mysqli_real_escape_string($conn, $_POST['date_obtained'][$index] ?? '');
            $expiration_date = mysqli_real_escape_string($conn, $_POST['expiration_date'][$index] ?? '');

            $awards_details[] = "$award_name from $issuing_organization (Obtained: $date_obtained, Expiration: $expiration_date)";
        }
    }
    $awards = implode('; ', $awards_details);

    // Collect certifications
    $certifications_details = [];
    if (isset($_POST['certification_name'])) {
        foreach ($_POST['certification_name'] as $index => $certification_name) {
            $certifications_details[] = mysqli_real_escape_string($conn, $certification_name);
        }
    }
    $certifications = implode('; ', $certifications_details);

    // Update user information
    mysqli_query($conn, "UPDATE `user6_form` SET 
        name = '$update_name', 
        email = '$update_email', 
        contact_number = '$contact_number', 
        linkedin_id = '$linkedin_id', 
        portfolio = '$portfolio', 
        job_title = '$job_title', 
        professional_summary = '$professional_summary', 
        work_experience = '$work_experience', 
        hard_skills = '$hard_skills', 
        soft_skills = '$soft_skills', 
        certifications = '$certifications', 
        education = '$education', 
        projects = '$projects', 
        languages = '$languages', 
        professional_affiliations = '$professional_affiliations', 
        volunteer_experience = '$volunteer_experience', 
        publications = '$publications', 
        awards = '$awards' 
        WHERE id = '$user_id'") or die('query failed');

    // Password update logic
    $old_pass = $_POST['old_pass'] ?? '';
    $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass'] ?? ''));
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass'] ?? ''));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass'] ?? ''));

    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if ($update_pass != $old_pass) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'Confirm password not matched!';
        } else {
            mysqli_query($conn, "UPDATE `user6_form` SET password = '$confirm_pass' WHERE id = '$user_id'") or die('query failed');
            $message[] = 'Password updated successfully!';
        }
    }

    // Image upload logic
    $update_image = $_FILES['update_image']['name'] ?? '';
    $update_image_size = $_FILES['update_image']['size'] ?? 0;
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'] ?? '';
    $update_image_folder = 'uploaded_img/' . $update_image;

    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'Image is too large';
        } else {
            $image_update_query = mysqli_query($conn, "UPDATE `user6_form` SET image = '$update_image' WHERE id = '$user_id'") or die('query failed');
            if ($image_update_query) {
                move_uploaded_file($update_image_tmp_name, $update_image_folder);
            }
            $message[] = 'Image updated successfully!';
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
  font-family: 'Arial', sans-serif;
  background: linear-gradient(135deg, #56d8ff, #457b9d, #a8dadc, #e63946);
  background-size: 300% 300%;
  animation: gradientCycle 15s ease infinite;
  margin: 0;
  padding: 40px 20px;
  color: #fff;
}

@keyframes gradientCycle {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

.update-profile {
  background: rgba(0, 0, 0, 0.75);
  padding: 40px;
  max-width: 1000px;
  margin: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
  border-radius: 20px;
  border: 3px solid #f39c12;
  animation: blinkBorder 2s infinite;
}

@keyframes blinkBorder {
  0% { border-color: #f39c12; }
  50% { border-color: transparent; }
  100% { border-color: #f39c12; }
}

.update-profile img {
  max-width: 150px;
  border-radius: 50%;
  margin-bottom: 20px;
  box-shadow: 0 0 15px rgba(255,255,255,0.3);
}

.inputBox {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

.inputBox span {
  font-weight: bold;
  margin: 5px 0;
  color: #f4f4f4;
  font-size: 1.1em;
}

.box {
  padding: 12px;
  background: #f4f4f4;
  color: #000;
  border-radius: 10px;
  font-size: 1em;
  border: none;
  box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}

textarea.box {
  resize: vertical;
  min-height: 80px;
}

.btn, .delete-btn, button {
  background: linear-gradient(45deg, #e63946, #f39c12);
  color: #fff;
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-weight: bold;
  font-size: 1em;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-right: 10px;
  display: inline-block;
}

button:hover, .btn:hover, .delete-btn:hover {
  background: linear-gradient(45deg, #d62828, #f77f00);
  transform: scale(1.05);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

.delete-btn {
  background: linear-gradient(45deg, #ff416c, #ff4b2b);
}

.flex {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.flex .inputBox {
  flex: 1 1 45%;
}

.message {
  background-color: rgba(255, 99, 71, 0.9);
  color: #fff;
  padding: 12px 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
}


    </style>
</head>
<body>
    <!-- PHP, HTML form and all inputs remain as-is, as per the original code -->
    <!-- Place your original HTML/PHP content below this style section -->
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="update-profile">
        <?php
        $select = mysqli_query($conn, "SELECT * FROM `user6_form` WHERE id = '$user_id'") or die('query failed: ' . mysqli_error($conn));
        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
        } else {
            $fetch = [];
        }
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <?php
            if (empty($fetch['image'])) {
                echo '<img src="images/default-avatar.png">';
            } else {
                echo '<img src="uploaded_img/' . $fetch['image'] . '">';
            }
            if (isset($message)) {
                foreach ($message as $msg) {
                    echo '<div class="message">' . $msg . '</div>';
                }
            }
            ?>
            <div class="flex">
                <div class="inputBox">
                    <span>Username:</span>
                    <input type="text" name="update_name" value="<?php echo htmlspecialchars($fetch['name'] ?? ''); ?>" class="box">
                    <span>Your Email:</span>
                    <input type="email" name="update_email" value="<?php echo htmlspecialchars($fetch['email'] ?? ''); ?>" class="box">
                    <span>Contact Number:</span>
                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($fetch['contact_number'] ?? ''); ?>" class="box">
                    <span>LinkedIn ID:</span>
                    <input type="text" name="linkedin_id" value="<?php echo htmlspecialchars($fetch['linkedin_id'] ?? ''); ?>" class="box">
                    <span>Portfolio URL:</span>
                    <input type="text" name="portfolio" value="<?php echo htmlspecialchars($fetch['portfolio'] ?? ''); ?>" class="box">
                    <span>Update Your Pic:</span>
                    <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
                    <span>Job Title:</span>
                    <input type="text" name="job_title" value="<?php echo htmlspecialchars($fetch['job_title'] ?? ''); ?>" class="box">
                    <span>Professional Summary:</span>
                    <textarea name="professional_summary" class="box"><?php echo htmlspecialchars($fetch['professional_summary'] ?? ''); ?></textarea>
                    
                    <span>Work Experience:</span>
                    <div id="work-experience-container">
                        <div class="work-experience-entry">
                            <input type="text" name="work_experience_job_title[]" placeholder="Job Title" class="box">
                            <input type="text" name="work_experience_company_name[]" placeholder="Company Name" class="box">
                            <input type="text" name="work_experience_location[]" placeholder="Location (City, State)" class="box">
                            <input type="text" name="work_experience_start_date[]" placeholder="Start Date (Month/Year)" class="box">
                            <input type="text" name="work_experience_end_date[]" placeholder="End Date (or 'Present')" class="box">
                            <textarea name="work_experience_responsibilities[]" placeholder="Key Responsibilities" class="box"></textarea>
                            <textarea name="work_experience_achievements[]" placeholder="Achievements" class="box"></textarea>
                            <input type="text" name="work_experience_keywords[]" placeholder="Keywords (skills/tools)" class="box">
                        </div>
                    </div>
                    <button type="button" onclick="addWorkExperience()">Add More Work Experience</button>

                    <span>Hard Skills:</span>
                    <select name="hard_skills[]" class="box" multiple>
                        <option value="PHP">PHP</option>
                        <option value="JavaScript">JavaScript</option>
                        <option value="HTML">HTML</option>
                        <option value="CSS">CSS</option>
                        <option value="Python">Python</option>
                        <option value="Java">Java</option>
                        <option value="C++">C++</option>
                        <option value="SQL">SQL</option>
                        <option value="MySQL">MySQL</option>
                        <option value="React">React</option>
                        <option value="Node.js">Node.js</option>
                        <option value="Angular">Angular</option>
                        <option value="TypeScript">TypeScript</option>
                        <option value="Docker">Docker</option>
                        <option value="Kubernetes">Kubernetes</option>
                        <option value="AWS">AWS</option>
                        <option value="Azure">Azure</option>
                        <option value="GCP">GCP</option>
                        <option value="Linux">Linux</option>
                        <option value="Git">Git</option>
                        <option value="Machine Learning">Machine Learning</option>
                        <option value="Deep Learning">Deep Learning</option>
                        <option value="Data Science">Data Science</option>
                        <option value="Artificial Intelligence">Artificial Intelligence</option>
                        <option value="Cybersecurity">Cybersecurity</option>
                        <option value="Blockchain">Blockchain</option>
                        <option value="IoT">IoT</option>
                        <option value="Automation">Automation</option>
                        <option value="Embedded Systems">Embedded Systems</option>
                        <option value="Networking">Networking</option>
                        <option value="Cloud Computing">Cloud Computing</option>
                        <option value="DevOps">DevOps</option>
                        <option value="Microservices">Microservices</option>
                        <option value="Shell Scripting">Shell Scripting</option>
                        <option value="PowerShell">PowerShell</option>
                        <option value="AutoCAD">AutoCAD</option>
                        <option value="MATLAB">MATLAB</option>
                        <option value="Simulink">Simulink</option>
                        <option value="Ansys">Ansys</option>
                        <option value="SolidWorks">SolidWorks</option>
                        <option value="TensorFlow">TensorFlow</option>
                        <option value="PyTorch">PyTorch</option>
                        <option value="Natural Language Processing">Natural Language Processing</option>
                        <option value="R">R</option>
                        <option value="Scala">Scala</option>
                        <option value="Hadoop">Hadoop</option>
                        <option value="Spark">Spark</option>
                    </select>

                    <span>Soft Skills:</span>
                    <select name="soft_skills[]" class="box" multiple>
                        <option value="Communication">Communication</option>
                        <option value="Teamwork">Teamwork</option>
                        <option value="Problem Solving">Problem Solving</option>
                        <option value="Adaptability">Adaptability</option>
                        <option value="Creativity">Creativity</option>
                        <option value="Time Management">Time Management</option>
                        <option value="Leadership">Leadership</option>
                        <option value="Critical Thinking">Critical Thinking</option>
                        <option value="Interpersonal Skills">Interpersonal Skills</option>
                        <option value="Work Ethic">Work Ethic</option>
                    </select>

                    <span>Certifications:</span>
                    <div id="certifications-container">
                        <div class="certification-entry">
                            <input type="text" name="certification_name[]" placeholder="Certification Name" class="box">
                        </div>
                    </div>
                    <button type="button" onclick="addCertification()">Add More Certifications</button>
                    
                    <span>Education:</span>
                    <div id="education-container">
                        <div class="education-entry">
                            <input type="text" name="education[]" placeholder="Degree" class="box">
                            <input type="text" name="institution[]" placeholder="Institution" class="box">
                            <input type="text" name="edu_location[]" placeholder="Location (City, State)" class="box">
                            <input type="text" name="edu_graduation_date[]" placeholder="Graduation Date (Month/Year)" class="box">
                            <input type="text" name="edu_gpa[]" placeholder="GPA (optional)" class="box">
                        </div>
                    </div>
                    <button type="button" onclick="addEducation()">Add More Education</button>

                    <span>Projects:</span>
                    <div id="project-container">
                        <div class="project-entry">
                            <input type="text" name="projects[]" placeholder="Project Title" class="box">
                            <input type="text" name="project_role[]" placeholder="Role" class="box">
                            <input type="text" name="project_technologies[]" placeholder="Technologies/Tools Used" class="box">
                            <textarea name="project_description[]" placeholder="Description" class="box"></textarea>
                            <textarea name="project_achievements[]" placeholder="Achievements/Outcomes" class="box"></textarea>
                        </div>
                    </div>
                    <button type="button" onclick="addProject()">Add More Projects</button>

                    <span>Languages:</span>
                    <select name="languages[]" class="box" multiple>
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="German">German</option>
                        <!-- Add more options as needed -->
                    </select>

                    <span>Professional Affiliations:</span>
                    <textarea name="professional_affiliations" class="box"><?php echo htmlspecialchars($fetch['professional_affiliations'] ?? ''); ?></textarea>

                    <span>Volunteer Experience:</span>
                    <textarea name="volunteer_experience" class="box"><?php echo htmlspecialchars($fetch['volunteer_experience'] ?? ''); ?></textarea>

                    <span>Publications:</span>
                    <textarea name="publications" class="box"><?php echo htmlspecialchars($fetch['publications'] ?? ''); ?></textarea>

                    <span>Awards:</span>
                    <div id="awards-container">
                        <div class="award-entry">
                            <input type="text" name="award_name[]" placeholder="Award Name" class="box">
                            <input type="text" name="issuing_organization[]" placeholder="Issuing Organization" class="box">
                            <input type="text" name="date_obtained[]" placeholder="Date Obtained" class="box">
                            <input type="text" name="expiration_date[]" placeholder="Expiration Date (if applicable)" class="box">
                        </div>
                    </div>
                    <button type="button" onclick="addAward()">Add More Awards</button>
                </div>
                <div class="inputBox">
                    <input type="hidden" name="old_pass" value="<?php echo htmlspecialchars($fetch['password'] ?? ''); ?>">
                    <span>Old Password:</span>
                    <input type="password" name="update_pass" placeholder="Enter previous password" class="box">
                    <span>New Password:</span>
                    <input type="password" name="new_pass" placeholder="Enter new password" class="box">
                    <span>Confirm Password:</span>
                    <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box">
                </div>
            </div>
            <input type="submit" value="Update Profile" name="update_profile" class="btn">
            <a href="home.php" class="delete-btn">Go Back</a>
            <a href="generate_resume.php" class="btn">Download Resume</a>
            <a href="ats_resume_generator.php" class="btn">ATS Friendly Resume Generator</a>
        </form>
    </div>

    <script>
        function addWorkExperience() {
            const container = document.getElementById('work-experience-container');
            const entry = document.createElement('div');
            entry.className = 'work-experience-entry';
            entry.innerHTML = `
                <input type="text" name="work_experience_job_title[]" placeholder="Job Title" class="box">
                <input type="text" name="work_experience_company_name[]" placeholder="Company Name" class="box">
                <input type="text" name="work_experience_location[]" placeholder="Location (City, State)" class="box">
                <input type="text" name="work_experience_start_date[]" placeholder="Start Date (Month/Year)" class="box">
                <input type="text" name="work_experience_end_date[]" placeholder="End Date (or 'Present')" class="box">
                <textarea name="work_experience_responsibilities[]" placeholder="Key Responsibilities" class="box"></textarea>
                <textarea name="work_experience_achievements[]" placeholder="Achievements" class="box"></textarea>
                <input type="text" name="work_experience_keywords[]" placeholder="Keywords (skills/tools)" class="box">
            `;
            container.appendChild(entry);
        }

        function addEducation() {
            const container = document.getElementById('education-container');
            const entry = document.createElement('div');
            entry.className = 'education-entry';
            entry.innerHTML = `
                <input type="text" name="education[]" placeholder="Degree" class="box">
                <input type="text" name="institution[]" placeholder="Institution" class="box">
                <input type="text" name="edu_location[]" placeholder="Location (City, State)" class="box">
                <input type="text" name="edu_graduation_date[]" placeholder="Graduation Date (Month/Year)" class="box">
                <input type="text" name="edu_gpa[]" placeholder="GPA (optional)" class="box">
            `;
            container.appendChild(entry);
        }

        function addProject() {
            const container = document.getElementById('project-container');
            const entry = document.createElement('div');
            entry.className = 'project-entry';
            entry.innerHTML = `
                <input type="text" name="projects[]" placeholder="Project Title" class="box">
                <input type="text" name="project_role[]" placeholder="Role" class="box">
                <input type="text" name="project_technologies[]" placeholder="Technologies/Tools Used" class="box">
                <textarea name="project_description[]" placeholder="Description" class="box"></textarea>
                <textarea name="project_achievements[]" placeholder="Achievements/Outcomes" class="box"></textarea>
            `;
            container.appendChild(entry);
        }

        function addAward() {
            const container = document.getElementById('awards-container');
            const entry = document.createElement('div');
            entry.className = 'award-entry';
            entry.innerHTML = `
                <input type="text" name="award_name[]" placeholder="Award Name" class="box">
                <input type="text" name="issuing_organization[]" placeholder="Issuing Organization" class="box">
                <input type="text" name="date_obtained[]" placeholder="Date Obtained" class="box">
                <input type="text" name="expiration_date[]" placeholder="Expiration Date (if applicable)" class="box">
            `;
            container.appendChild(entry);
        }

        function addCertification() {
            const container = document.getElementById('certifications-container');
            const entry = document.createElement('div');
            entry.className = 'certification-entry';
            entry.innerHTML = `
                <input type="text" name="certification_name[]" placeholder="Certification Name" class="box">
            `;
            container.appendChild(entry);
        }
    </script>
</body>
</html>
</body>
</html>
