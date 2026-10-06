<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_GET['logout'])){
   unset($user_id);
   session_destroy();
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

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

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    .profile {
      background: rgba(0, 0, 0, 0.75);
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
      color: #fff;
      width: 90%;
      max-width: 400px;
      border: 3px solid #f39c12;
      text-align: center;
    }

    .profile img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      margin-bottom: 20px;
      object-fit: cover;
    }

    .btn, .delete-btn {
      background: linear-gradient(45deg, #e63946, #f39c12);
      color: #fff;
      border: none;
      padding: 15px 20px;
      font-size: 1.2em;
      font-weight: bold;
      cursor: pointer;
      border-radius: 10px;
      width: 100%;
      text-align: center;
      margin-top: 10px;
      text-decoration: none;
    }

    .btn:hover, .delete-btn:hover {
      background: linear-gradient(45deg, #d62828, #f77f00);
      transform: scale(1.05);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

    .delete-btn {
      background: linear-gradient(45deg, #f77f00, #d62828);
    }

    p {
      color: white;
      text-align: center;
      margin-top: 20px;
    }

    a {
      color: #f39c12;
      text-decoration: none;
    }

   </style>

</head>
<body>
   
<div class="container">

   <div class="profile">
      <?php
         $select = mysqli_query($conn, "SELECT * FROM `user6_form` WHERE id = '$user_id'") or die('query failed');
         if(mysqli_num_rows($select) > 0){
            $fetch = mysqli_fetch_assoc($select);
         }
         if($fetch['image'] == ''){
            echo '<img src="images/default-avatar.png">';
         }else{
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }
      ?>
      <h3><?php echo $fetch['name']; ?></h3>
      <a href="update_profile.php" class="btn">Update Profile</a>
      <a href="home.php?logout=<?php echo $user_id; ?>" class="delete-btn">Logout</a>
      <p>New <a href="login.php">Login</a> or <a href="register.php">Register</a></p>
   </div>

</div>

</body>
</html>
