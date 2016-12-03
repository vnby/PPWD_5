<?php
session_start();

if(!isset($_SESSION['login_user']) || $_SESSION['role'] == 'user') {
	header("Location: login.php");
}

function getBorrowedTotal() {
	$conn = connectDB();
	$user_id = $_SESSION['user_id'];

	$sql1 = "SELECT * FROM loan WHERE user_id='$user_id'";
    $result1 = mysqli_query($conn,$sql1);
    $row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);
    $count1 = mysqli_num_rows($result1);
	
	if(!$result = mysqli_query($conn, $sql1)) {
		die("Error: $sql1");
	}
	mysqli_close($conn);
	return $count1;
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

function tambahBuku() {
	$conn = connectDB();
	$img_path = $_POST['img_path'];
	$title = $_POST['title'];
	$author = $_POST['author'];
	$publisher = $_POST['publisher'];
	$description = $_POST['description'];
	$quantity = $_POST['quantity'];

	//cek dulu ada buku yg sama ato ga
	$sql3 = "SELECT * FROM book WHERE title='$title'";
    $result3 = mysqli_query($conn,$sql3);
    $row3 = mysqli_fetch_array($result3,MYSQLI_ASSOC);
    $count3 = mysqli_num_rows($result3);

    //buat masukin book ke database
	$sql = "INSERT into book (img_path, title, author, publisher, description, quantity) values('$img_path','$title','$author','$publisher','$description','$quantity')";

	//buat ngambil book id terakhir
	$sql2 = "SELECT book_id FROM book ORDER BY book_id DESC LIMIT 1";
	$result2 = mysqli_query($conn, $sql2);
	$row1 = mysqli_fetch_row($result2);

	if($count3 == 1) { //jumlah elemen di array nya ada 5
		//ambil qty yang lama
		$book_id = $row3['book_id'];
		$sql4 = "SELECT quantity FROM book WHERE book_id='$book_id'";
		$result4 = mysqli_query($conn, $sql4);
		$row4 = mysqli_fetch_array($result4, MYSQLI_ASSOC);
		$newquantity = $row4['quantity'] + $quantity;

		//update qty
		$sql5 = "UPDATE book SET quantity = $newquantity WHERE book_id='$book_id'";
		$result5 = mysqli_query($conn, $sql5);

		//ke halaman bukunya
		$bookid = $row3['book_id'];
		header("Location: book.php?bookid=$bookid");
	} else if($result = mysqli_query($conn, $sql)) {
		$bookid = $row1['0'] + 1;
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
	} else if($_POST['command'] === 'tambah') {
		tambahBuku();
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
		<?php

		if($_SESSION['role'] == "guest") {
			echo '<li><a href="login.php">Login</a></li>';
		} else {
			$role = $_SESSION['role'];
			echo '<li><a class="btn-flat disabled">Role:</a></li>
			<li><a class="btn-flat disabled">'; echo $role; echo '</a></li>
			<li class="divider"></li>
			<li><a href="logout.php">Logout</a></li>';
		}
		?>
	</ul>
	<div class="navbar-fixed">
		<nav>
			<div class="nav-wrapper teal lighten-1">
				<a href="index.php" class="brand-logo">.::Personal Library::.</a>
				<ul id="nav-mobile" class="right hide-on-med-and-down">
					<li><a href="index.php"><i class="material-icons right">view_module</i>The Library</a></li>
					<?php
					if($_SESSION['role'] == 'admin')
						echo '<li class="active"><a href="addbook.php"><i class="material-icons right">library_books</i>Add New Book</a></li>';

					if($_SESSION['role'] == 'user') {
						echo '<li><a href="borrowed.php"><i class="material-icons right">library_books</i>';
					
						$borrowed = getBorrowedTotal();
						if($borrowed == 0) {
							echo '(no book borrowed)';
						} else if(getBorrowedTotal() == 1) {
							echo $borrowed; echo ' borrowed book';
						}
						else {
							echo $borrowed; echo ' borrowed books';
						}
					}
					?></a></li>

					<!-- Dropdown Trigger -->
					<li><a class="dropdown-button disable" href="#!" data-activates="dropdown1">Hi, <?php echo $_SESSION['login_user']?><i class="material-icons right">arrow_drop_down</i></a></li>
				</ul>
			</div>
		</nav>
	</div>

		<div class="container">
		<div class="row">
			<div class="col s12">
				<blockquote><h3>Add New Book</h3></blockquote>
			</div>
		</div>
		<div class="row">
		<form method="post" action="addbook.php" class="col s6 offset-s3">
			<div class="row">
				<div class="input-field col s12">
					<i class="material-icons prefix">perm_media</i>
					<label for="img_path">Cover Book Preview</label>
					<input type="text" id="img_path" name="img_path" placeholder="Enter the URL of the book cover">
				</div>
				<div class="input-field col s12">
					<i class="material-icons prefix">book</i>
					<label for="title">Title</label>
					<input type="text" name="title" placeholder="Enter the book title">
				</div>
				<div class="input-field col s12">
					<i class="material-icons prefix">person_pin</i>
					<label for="title">Author</label>
					<input type="text" id="author" name="author" placeholder="Enter the book author">
				</div>
				<div class="input-field col s12">
					<i class="material-icons prefix">class</i>
					<label for="title">Publisher</label>
					<input type="text" id="publisher" name="publisher" placeholder="Enter the book publisher">
				</div>
				<div class="input-field col s12">
					<i class="material-icons prefix">comment</i>
					<label for="title">Description</label>
					<input type="text" id="description" name="description" placeholder="Enter the book description">
				</div>
				<div class="input-field col s12">
					<i class="material-icons prefix">trending_up</i>
					<label for="title">Quantity</label>
					<input type="number" id="quantity" name="quantity" placeholder="Enter the book quantity">
				</div>
			</div>
			<input type="hidden" name="command" value="tambah">
			<button class="btn waves-effect waves-light" type="submit" id="selesaiUpdate" name="save" class="waves-effect waves-teal btn-flat">ADD BOOK</button>
		</form>
		</div>
		</div>
</body>
</html>