<?php
session_start();

if(!isset($_SESSION['login_user'])) {
	header("Location: login.php");
} else if($_SESSION['role'] == "admin" || $_SESSION['role'] == "guest") {
	header("Location: index.php");
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

function bukuKembali($book_id, $user_id) {
	$conn = connectDB();
		//$book_id = $_POST['book_id'];
	$book_id = $_POST['book_id'];
	$user_id = $_SESSION['user_id'];
	$title = $_POST['title'];
	$author = $_POST['author'];
	$publisher = $_POST['publisher'];
	$description = $_POST['description'];
	$quantity = $_POST['quantity'];
	$sql1 = "INSERT into book (title, author, publisher, description, quantity) values('$title','$author','$publisher','$description','$quantity')";
	$sql2 = "SELECT quantity FROM book WHERE book_id='$book_id'";
	$result2 = mysqli_query($conn, $sql2);
	$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
	$newquantity = $row2['quantity'] + 1;
	$sql3 = "UPDATE book SET quantity = $newquantity WHERE book_id='$book_id'";
	if($result1 = mysqli_query($conn, $sql1) && $result2 = mysqli_query($conn, $sql2) && $result3 = mysqli_query($conn, $sql3)) {
		echo "Terima kasih telah mengembalikan buku:) <br/>";
		header("Location: book.php?bookid=$book_id");
	} else {
		die("Error: $sql3");
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

function selectBookReview($book_id) {
	$conn = connectDB();

	$sql = "SELECT user_id, date, content FROM review WHERE book_id='$book_id'";
	
	if(!$result = mysqli_query($conn, $sql)) {
		die("Error: $sql");
	}
	mysqli_close($conn);
	return $result;
}

function getNameFromID($user_id) {
	$conn = connectDB();

	$sql = "SELECT username FROM user WHERE user_id='$user_id'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$name = $row['username'];
	
	if(!$result = mysqli_query($conn, $sql)) {
		die("Error: $sql");
	}
	mysqli_close($conn);
	return $name;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if($_POST['command'] === 'pinjam') {
		pinjamBuku($_POST['book_id'], $_SESSION['user_id']);
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
						echo '<li><a href="addbook.php"><i class="material-icons right">library_books</i>Add New Book</a></li>';

					if($_SESSION['role'] == 'user') {
						echo '<li class="active"><a href="borrowed.php"><i class="material-icons right">library_books</i>';
					
						$borrowed = getBorrowedTotal();
						if($borrowed == 0) {
							echo '(no book borrowed)';
						} else if(getBorrowedTotal() == 1) {
							echo $borrowed; echo ' book borrowed';
						}
						else {
							echo $borrowed; echo ' books borrowed';
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
			<?php
				if(getBorrowedTotal() == 0) {
					echo '<blockquote><h3>No Book Borrowed</h3></blockquote>';
				} else if (getBorrowedTotal() == 1) {
					echo '<blockquote><h3>Book Borrowed</h3></blockquote>';
					echo 'click book cover thumbnail for book details';
				}
				else {
					echo '<blockquote><h3>Books Borrowed</h3></blockquote>';
					echo 'click book cover thumbnail for book details';
				}
					?>
			</div>
		</div>
		<div class="row">
			<div class="col s12">
				<ul class="collection">
					<?php
						$user_id = $_SESSION['user_id'];
						$conn = connectDB();
						$sql = "SELECT * FROM loan WHERE user_id='$user_id'";
						$result = mysqli_query($conn, $sql);

						while($row1 = mysqli_fetch_row($result)) {
							$book_id = $row1['1'];
							//echo $book_id;
							$sql2 = "SELECT * FROM book WHERE book_id='$book_id'";
							$result2 = mysqli_query($conn, $sql2);
							$row2 = mysqli_fetch_row($result2);

							echo '<li class="collection-item avatar">
								<a href="book.php?bookid=' . $row2['0'] . '"><img src="'.$row2['1'].'" alt="" class="circle"></a>
								<span class="title">'.$row2['2'].'</span>
								<p>authored by <b>'.$row2['3'].'</b></p>
								<p>published by <b>'.$row2['4'].'</b></p>
								<div class="chip"><b>'.$row2['6'].'</b> available</div>
								<form action="book.php" method="post">
									<input type="hidden" name="loan_id" value="'.$row1['0'].'">
									<input type="hidden" name="book_id" value="'.$row1['1'].'">
										<input type="hidden" name="command" value="kembali">
										<button class="btn waves-effect waves-light secondary-content" type="submit">Return This Book<i class="material-icons right">settings_backup_restore</i></button>
								</form>
					</li>';
						}
					
					?>
				</ul>
			</div>
		</div>
	</div>
	
</body>
</html>