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
	$user_id = $_SESSION['user_id'];

	$sql1 = "INSERT INTO loan (book_id, user_id) VALUES ($book_id, $user_id)";
	$sql2 = "SELECT quantity FROM book WHERE book_id='$book_id'";
	
	$result2 = mysqli_query($conn, $sql2);
	$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
	$newquantity = $row2['quantity'] - 1;
	
	$sql3 = "UPDATE book SET quantity = $newquantity WHERE book_id='$book_id'";

	if($result1 = mysqli_query($conn, $sql1) && $result2 = mysqli_query($conn, $sql2) && $result3 = mysqli_query($conn, $sql3)) {
		echo "Buku berhasil anda pinjam!<br/>";
		header("Location: index.php");
	} else {
		die("Error: $sql3");
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

	$sql = "SELECT book_id, img_path, title, author, publisher, description, quantity FROM $table";

	if(!$result = mysqli_query($conn, $sql)) {
		die("Error: $sql");
	}
	mysqli_close($conn);
	return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if($_POST['command'] === 'pinjam') {
		pinjamBuku($_POST['book_id'], $_SESSION['user_id']);
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
	
	<ul id="dropdown1" class="dropdown-content">
		<li><a href="#">Role: <?php echo $_SESSION['role'] ?></a></li>
		<li class="divider"></li>
		<li><a href="logout.php">Logout</a></li>
	</ul>
	<div class="navbar-fixed">
		<nav>
			<div class="nav-wrapper">
				<a href="#" class="brand-logo">.::Personal Library::.</a>
				<ul id="nav-mobile" class="right hide-on-med-and-down">
					<!-- Dropdown Trigger -->
					<li><a class="dropdown-button disable" href="#!" data-activates="dropdown1">Hi, <?php echo $_SESSION['login_user']?><i class="material-icons right">arrow_drop_down</i></a></li>
				</ul>
			</div>
		</nav>
	</div>
	<div class="container">
			<?php
				if(isset($_GET['bookid'])) {
					$book_id = $_GET['bookid'];
					$conn = connectDB();
					$sql = "SELECT * FROM book WHERE book_id='$book_id'";
					$result = mysqli_query($conn, $sql);
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
					//$newquantity = $row['quantity'] - 1;

					echo '
						<div class="row center">
							<div class="col s12">
								<h3>Book Details</h3>
							</div>
						</row>
						<div class="row center">
							<div class="col s3">
								<img src='.$row['img_path'].' height=350 width=200>
							</div>
							<div class="col s9">

							</div>
						</row>
					';
				} else {
					echo "bookid gada";
				}
			 ?>
	</div>
</body>
</html>

