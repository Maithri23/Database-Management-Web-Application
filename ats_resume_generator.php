<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT name, email, contact_number, linkedin_id, portfolio, hard_skills, soft_skills, professional_summary, work_experience, education, projects, awards FROM user6_form WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
$user_data = mysqli_fetch_assoc($result);
if (!$user_data) {
    die("No user data found with ID: " . $user_id);
}

$name = $user_data['name'];
$email = $user_data['email'];
$phone = $user_data['contact_number'];
$hard_skills = array_map('trim', explode(',', strtolower($user_data['hard_skills'])));
$soft_skills = array_map('trim', explode(',', strtolower($user_data['soft_skills'])));
$user_all_skills = array_unique(array_merge($hard_skills, $soft_skills));

$summary = $user_data['professional_summary'] ?? '';
$experience = $user_data['work_experience'] ?? '';
$education = $user_data['education'] ?? '';
$projects = $user_data['projects'] ?? '';
$awards = $user_data['awards'] ?? '';

$matched_skills = [];
$missing_job_skills = [];
$extra_user_skills = [];
$resume_content = "";

if (isset($_POST['job_description'])) {
    $job_description_raw = $_POST['job_description'];
    $job_description = mysqli_real_escape_string($conn, $job_description_raw);

    $api_key = 'AIzaSyBWPZhu-n2V54AJbspJX2j0NXqZlXXZQWQ';
    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";

    $prompt_extract_skills = <<<EOD
Extract only a comma-separated list of essential hard and soft skills from the following job description. No explanation.

Job Description:
$job_description_raw
EOD;

    $postDataSkills = [
        "contents" => [[ "parts" => [["text" => $prompt_extract_skills]] ]],
    ];
    $ch_skills = curl_init($api_url);
    curl_setopt($ch_skills, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_skills, CURLOPT_POSTFIELDS, json_encode($postDataSkills));
    curl_setopt($ch_skills, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response_skills = curl_exec($ch_skills);
    curl_close($ch_skills);

    $api_skills_response = json_decode($response_skills, true);
    $extracted_skills = [];

    if (isset($api_skills_response['candidates'][0]['content']['parts'][0]['text'])) {
        $extracted_text = strtolower($api_skills_response['candidates'][0]['content']['parts'][0]['text']);
        $extracted_skills = array_map('trim', explode(',', $extracted_text));
    }

    foreach ($extracted_skills as $skill) {
        if (in_array($skill, $user_all_skills)) {
            $matched_skills[] = $skill;
        } else {
            $missing_job_skills[] = $skill;
        }
    }

    foreach ($user_all_skills as $user_skill) {
        if (!in_array($user_skill, $matched_skills)) {
            $extra_user_skills[] = $user_skill;
        }
    }

    $ordered_skills = array_unique(array_merge($matched_skills, $extra_user_skills));
    $ordered_skills_text = implode(', ', $ordered_skills);

    $prompt_resume = <<<EOT
You are an expert resume writer. Using the user profile and job description below, generate a one-page, ATS-friendly resume in plain text format. 

Focus on:
- Making content concise, not exceeding two points per section where applicable.
- Using keywords from the job description.
- Prioritizing matched skills.
- Structuring with clean headers: Name, Contact, Professional Summary, Experience, Skills, Education, Projects, Awards.
- Using bullet points.
- Avoiding colors, graphics, or columns.
- Making it concise and focused on relevant content.

Name: $name
Email: $email
Phone: $phone
Professional Summary: $summary
Skills: $ordered_skills_text
Experience: $experience
Education: $education
Projects: $projects
Awards: $awards

Job Description:
$job_description_raw

Return only the final resume content, no explanation or extra notes.
EOT;

    $postDataResume = [
        "contents" => [[ "parts" => [["text" => $prompt_resume]] ]],
    ];
    $ch_resume = curl_init($api_url);
    curl_setopt($ch_resume, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_resume, CURLOPT_POSTFIELDS, json_encode($postDataResume));
    curl_setopt($ch_resume, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response_resume = curl_exec($ch_resume);
    curl_close($ch_resume);

    $api_resume_response = json_decode($response_resume, true);
    if (isset($api_resume_response['candidates'][0]['content']['parts'][0]['text'])) {
        $resume_content = $api_resume_response['candidates'][0]['content']['parts'][0]['text'];
    } else {
        $resume_content = "⚠️ Resume generation failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ATS Resume Generator</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
      background: linear-gradient(135deg, #56d8ff, #457b9d, #a8dadc, #e63946);
      background-size: 300% 300%;
      animation: gradientCycle 15s ease infinite;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    @keyframes gradientCycle {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    h1 {
      color: white;
      text-align: center;
      margin-bottom: 30px;
      font-size: 2.5em;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
    }

    form {
      background: rgba(0, 0, 0, 0.75);
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
      color: #fff;
      width: 90%;
      max-width: 700px;
      border: 3px solid #f39c12;
      animation: blinkBorder 2s infinite;
    }

    @keyframes blinkBorder {
      0% { border-color: #f39c12; }
      50% { border-color: transparent; }
      100% { border-color: #f39c12; }
    }

    textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: none;
      border-radius: 10px;
      background: #f4f4f4;
      color: #000;
      font-size: 1em;
      height: 120px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    }

    input[type="submit"], button {
      background: linear-gradient(45deg, #e63946, #f39c12);
      color: #fff;
      border: none;
      padding: 15px 20px;
      font-size: 1.1em;
      font-weight: bold;
      cursor: pointer;
      border-radius: 10px;
      width: 100%;
      margin-top: 10px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    input[type="submit"]:hover, button:hover {
      background: linear-gradient(45deg, #d62828, #f77f00);
      transform: scale(1.05);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

    .resume-preview {
      background: #fff;
      color: #000;
      padding: 20px;
      border-radius: 10px;
      font-family: monospace;
      white-space: pre-wrap;
      font-size: 12px;
      margin-top: 20px;
    }

    .highlight {
      background-color: blue;
      
      font-weight: bold;
    }

    h2 {
      margin-top: 30px;
      color: #fff;
      border-bottom: 2px solid #f39c12;
      padding-bottom: 5px;
    }

    ul {
      padding-left: 20px;
    }

    li {
      color: #f4f4f4;
    }
  </style>
</head>
<body>
  <h1>ATS-Friendly Resume Generator</h1>
  <form method="post">
    <textarea name="job_description" placeholder="Paste Job Description Here..." required><?= isset($_POST['job_description']) ? htmlspecialchars($_POST['job_description']) : '' ?></textarea>
    <input type="submit" value="Generate Resume">
  </form>

  <?php if (!empty($matched_skills)): ?>
    <h2>✅ Matched Skills:</h2>
    <ul><?php foreach ($matched_skills as $skill): ?>
      <li><span class="highlight"><?= htmlspecialchars($skill) ?></span></li>
    <?php endforeach; ?></ul>
  <?php endif; ?>

  <?php if (!empty($missing_job_skills)): ?>
    <h2>📌 Missing Skills:</h2>
    <ul><?php foreach ($missing_job_skills as $skill): ?>
      <li><?= htmlspecialchars($skill) ?></li>
    <?php endforeach; ?></ul>
  <?php endif; ?>

  <?php if (!empty($extra_user_skills)): ?>
    <h2>➕ Extra User Skills:</h2>
    <ul><?php foreach ($extra_user_skills as $skill): ?>
      <li><?= htmlspecialchars($skill) ?></li>
    <?php endforeach; ?></ul>
  <?php endif; ?>

  <?php if (!empty($resume_content)): ?>
    <h2>📄 Resume Preview:</h2>
    <div class="resume-preview">
      <?php
        foreach ($matched_skills as $matched_skill) {
            $resume_content = str_replace($matched_skill, "<span class='highlight'>$matched_skill</span>", $resume_content);
        }
        echo $resume_content;
      ?>
    </div>
    <form action="resume_download.php" method="post">
      <input type="hidden" name="resume_content" value="<?= htmlspecialchars($resume_content) ?>">
      <input type="submit" value="Download Resume">
    </form>
  <?php endif; ?>
</body>
</html>
