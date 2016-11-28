<?php
  function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "PPWD_5";
    
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    // Check connection
    if (!$conn) {
      die("Connection failed: " + mysqli_connect_error());
    }
    return $conn;
  }

   session_start();
   
    $db = connectDB();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // username and password sent from form 

        $myusername = mysqli_real_escape_string($db,$_POST['username']);
        $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
        
        //admin side
        $sql1 = "SELECT user_id FROM user WHERE username='$myusername' and password='$mypassword' AND role='admin'";
        $result1 = mysqli_query($db,$sql1);
        $row1 = mysqli_fetch_array($result1,MYSQLI_ASSOC);
        $count1 = mysqli_num_rows($result1);

        //user side
        $sql2 = "SELECT user_id FROM user WHERE username='$myusername' and password='$mypassword' AND role='user'";
        $result2 = mysqli_query($db,$sql2);
        $row2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);
        $count2 = mysqli_num_rows($result2);

        // If result matched $myusername and $mypassword, table row must be 1 row

        if($count1 == 1) {
            $_SESSION['login_user'] = $myusername;
            $_SESSION['role'] = "admin";

            header("location: index.php");
        } else if($count2 == 1) {
            $_SESSION['login_user'] = $myusername;
            $_SESSION['role'] = "user";

            header("location: index.php");
        } else {
            $error = "Username atau Password anda salah.";
            $_SESSION['invalid'] = $error;
        }
    }
?>

<!DOCTYPE html>
  <html>
    <head>
    <meta charset="UTF-8">
    <title>Personal Library</title>
    <script src="js/jquery-3.1.0.min.js"> </script>
    <script src="js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="css/mycv.css" >
    <link rel="stylesheet" type="text/css" href="css/normalize.css" > <!--no need to change this-->

    <!--Import jQuery before materialize.js-->
    <script type="text/javascript" src="js/jquery-3.1.0.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>

    <!--Import Google Icon Font-->
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  </head>

    <body>
     <div class="login">
      <h1>Personal Library</h1>
      <form action="login.php" method="post">
        <input type="text" id="username" name="username" placeholder="Username" />
        <input type="password" id="password" name="password" placeholder="Password" />
        <button type="submit" id="submit" name="masuk" class="waves-effect waves-teal btn-flat">Login</button>
      </form>
    </div>
    </body>
  </html>