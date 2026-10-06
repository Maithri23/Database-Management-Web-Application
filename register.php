<?php

include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select = mysqli_query($conn, "SELECT * FROM `user6_form` WHERE name = '$name' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $message[] = 'user already exist'; 
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }elseif($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $insert = mysqli_query($conn, "INSERT INTO `user6_form`(name, email, password, image) VALUES('$name', '$email', '$pass', '$image')") or die('query failed');

         if($insert){
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'registered successfully!';
            header('location:login.php');
         }else{
            $message[] = 'registration failed!';
         }
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Now</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
      background: linear-gradient(135deg, #56d8ff, #457b9d, #a8dadc, #e63946);
      background-size: 300% 300%;
      animation: gradientCycle 15s ease infinite;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    @keyframes gradientCycle {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    h3 {
      color: white;
      text-align: center;
      margin-bottom: 30px;
      font-size: 2.5em;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
      animation: fadeIn 1.5s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-container {
      background: rgba(0, 0, 0, 0.75);
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
      color: #fff;
      width: 90%;
      max-width: 400px;
      border: 3px solid #f39c12;
    }

    .message {
      color: #f39c12;
      font-size: 1.2em;
      margin-bottom: 10px;
    }

    label {
      font-size: 1.2em;
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
      color: #f4f4f4;
    }

    input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: none;
      border-radius: 10px;
      background: #f4f4f4;
      color: #000;
      font-size: 1em;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    }

    input:focus {
      outline: none;
      box-shadow: 0 4px 10px rgba(218, 67, 67, 0.3);
    }

    button {
      background: linear-gradient(45deg, #e63946, #f39c12);
      color: #fff;
      border: none;
      padding: 15px 20px;
      font-size: 1.2em;
      font-weight: bold;
      cursor: pointer;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      width: 100%;
      text-align: center;
    }

    button:hover {
      background: linear-gradient(45deg, #d62828, #f77f00);
      transform: scale(1.05);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

    p {
      text-align: center;
      color: #fff;
    }

    a {
      color: #f39c12;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
      <h3>Register Now</h3>
      
      <?php
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.htmlspecialchars($message).'</div>';
         }
      }
      ?>

      <label>Username:</label>
      <input type="text" name="name" placeholder="Enter Username" required>

      <label>Email:</label>
      <input type="email" name="email" placeholder="Enter Email" required>

      <label>Password:</label>
      <input type="password" name="password" placeholder="Enter Password" required>

      <label>Confirm Password:</label>
      <input type="password" name="cpassword" placeholder="Confirm Password" required>

      <label>Profile Image:</label>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png">

      <button type="submit" name="submit">Register Now</button>
      
      <p>Already have an account? <a href="login.php">Login Now</a></p>
    </form>
  </div>

</body>
</html>
