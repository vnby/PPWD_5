<?php
session_start();
	
	if(!isset($_SESSION['login_user'])) {
		header("Location: login.php");
	}
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
	
	function pinjamBuku($book_id, $user_id) {
		$conn = connectDB();
		
		//$loan_id = $_POST['loan_id'];
		$book_id = $_POST['book_id'];
		$user_id = $_POST['user_id'];
		$sql = "DELETE FROM loan WHERE book_id=$book_id AND user_id=$user_id";
		
		if($result = mysqli_query($conn, $sql)) {
			echo "Buku berhasil anda pinjam!<br/>";
			header("Location: index.php");
			} else {
			die("Error: $sql");
		}
		mysqli_close($conn);
	}
	
	function bukuKembali() {
		$conn = connectDB();
		
		//$book_id = $_POST['book_id'];
		$title = $_POST['title'];
		$author = $_POST['author'];
		$publisher = $_POST['publisher'];
		$description = $_POST['description'];
		$quantity = $_POST['quantity'];
		$sql = "INSERT into book (title, author, publisher, description, quantity) values('$title','$author','$publisher','$description','$quantity')";
		
		if($result = mysqli_query($conn, $sql)) {
			echo "Terima kasih telah mengembalikan buku:) <br/>";
			header("Location: index.php");
			} else {
			die("Error: $sql");
		}
		mysqli_close($conn);
	}
	
	function memberiReview($book_id, $user_id) {
		$conn = connectDB();
		
		$review_id = $_POST['review_id'];
		$book_id = $_POST['book_id'];
		$user_id = $_POST['user_id'];
		$date = $_POST['date'];
		$content = $_POST['content'];
		$sql = "INSERT INTO review (book_id, user_id, date, content) VALUES(book_id, user_id, date, content)";
		
		if($result = mysqli_query($conn, $sql)) {
			echo "Terima kasih telah memberi review:) <br/>";
			header("Location: index.php");
			} else {
			die("Error: $sql");
		}
		mysqli_close($conn);
	}

	function tambahBuku() {
		$conn = connectDB();
		
		//$book_id = $_POST['book_id'];
		$title = $_POST['title'];
		$author = $_POST['author'];
		$publisher = $_POST['publisher'];
		$description = $_POST['description'];
		$quantity = $_POST['quantity'];
		$sql = "INSERT into book (title, author, publisher, description, quantity) values('$title','$author','$publisher','$description','$quantity')";
		
		if($result = mysqli_query($conn, $sql)) {
			echo "Buku berhasil ditambah! <br/>";
			header("Location: index.php");
			} else {
			die("Error: $sql");
		}
		mysqli_close($conn);
	}
	
	function selectAllFromTable($table) {
		$conn = connectDB();
		
		$sql = "SELECT id, nama_paket, tujuan, fitur, harga FROM $table";
		
		if(!$result = mysqli_query($conn, $sql)) {
			die("Error: $sql");
		}
		mysqli_close($conn);
		return $result;
	}
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if($_POST['command'] === 'insert') {
			insertPaket();
			} else if($_POST['command'] === 'update') {
			updatePaket($_POST['userid']);
			} else if($_POST['command'] === 'delete') {
			deletePaket($_POST['userid']);
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
    <link rel="stylesheet" type="text/css" href="css/normalize.css" > 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!--no need to change this-->

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
	<nav>
    <div class="nav-wrapper">
      <a href="#" class="brand-logo">Logo</a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>
</body>
</html>