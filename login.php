<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']); // Change from email to name
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `user6_form` WHERE name = '$name' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];
      header('location:home.php');
   }else{
      $message[] = 'incorrect username or password!'; // Update message
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- custom css file link  -->
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

      input[type="text"], input[type="password"] {
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

   <form action="" method="post">
      <h3>Login Now</h3>
      
      <?php
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.htmlspecialchars($message).'</div>';
         }
      }
      ?>
      
      <label>Username:</label>
      <input type="text" name="name" placeholder="Enter Username" required>

      <label>Password:</label>
      <input type="password" name="password" placeholder="Enter Password" required>

      <button type="submit" name="submit">Login Now</button>

      <p>Don't have an account? <a href="register.php">Register Now</a></p>
   </form>

</div>

</body>
</html>
