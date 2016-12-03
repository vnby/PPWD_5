<?php
session_start();

if(!isset($_SESSION['login_user'])) {
	$_SESSION['login_user'] = "guest";
	$_SESSION['role'] = "guest";
	$_SESSION['user_id'] = "0";
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

function bukuKembali($book_id, $user_id) {
	$conn = connectDB();
	$loan_id = $_POST['loan_id'];
	$book_id = $_POST['book_id'];
	$user_id = $_SESSION['user_id'];
	$quantity = $_POST['quantity'];

	$sql1 = "DELETE FROM loan WHERE loan_id=$loan_id";
	$result1 = mysqli_query($conn, $sql1);
	$sql2 = "SELECT quantity FROM book WHERE book_id='$book_id'";
	$result2 = mysqli_query($conn, $sql2);
	$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

	$newquantity = $row2['quantity'] + 1;
	$sql3 = "UPDATE book SET quantity = $newquantity WHERE book_id='$book_id'";
	if(	$result1 = mysqli_query($conn, $sql1) && $result2 = mysqli_query($conn, $sql2) && $result3 = mysqli_query($conn, $sql3)) {
		echo "Terima kasih telah mengembalikan buku:) <br/>";
		header("Location: index.php");
	} else {
		die("Error: $sql3");
	}
	mysqli_close($conn);
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
	} else if($_POST['command'] === 'review') {
		$review = $_POST['reviewtextarea'];
		memberiReview($review, $_POST['bookid']);
	} else if($_POST['command'] === 'kembali') {
		bukuKembali($_POST['book_id'], $_SESSION['user_id']);
	} else if($_POST['command'] === 'tambah') {
		tambahBuku();
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
					<li class="active"><a href="index.php"><i class="material-icons right">view_module</i>The Library</a></li>
					<?php
					if($_SESSION['role'] == 'admin')
						echo '<li><a href="addbook.php"><i class="material-icons right">library_books</i>Add New Book</a></li>';

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

	<?php
	$books = selectAllFromTable("book");
	$whilecount = 0;
	while ($row = mysqli_fetch_row($books)) {
		$whilecount = $whilecount + 1;
		echo '<div class="row">';
		if($whilecount % 4 == 0) {
			echo 
			"<div class='col s3'>
			<div class='card horizontal small'>
				<div class='card-image'>
					<img src='".$row['1']."'>
				</div>
				<div class='card-stacked'>
					<div class='card-content'>
						<p>";
						if($row['6'] <= 5)
							echo "<span class='new badge red' data-badge-caption='remaining'>".$row['6']."</span></p>";
						else
							echo "<span class='new badge' data-badge-caption='remaining'>".$row['6']."</span></p>";
						echo "<br>
						<br>
						<p>".$row['2']."</p>
						<p>-- by <b>".$row['3']."</b></p>
						<p>-- published by <b>".$row['4']."</b></p>
					</div>
					<div class='card-action'>";
						echo "<a href='book.php?bookid=" . $row['0'] . "'>Details</a></br>";
						if($_SESSION['role'] == 'user') {
							$conn = connectDB();
							$user_id = $_SESSION['user_id'];
							$book_id = $row['0'];
							$sql1 = "SELECT * FROM loan WHERE user_id='$user_id' AND book_id=$book_id";
							$result1 = mysqli_query($conn,$sql1);
							$row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);

							if(!($row['6'] <= 0)) {
								if($row1['book_id'] == $row['0']) {
									echo '<form action="index.php" method="post" id="returnform'.$row['0'].'">
									<input type="hidden" name="loan_id" value="'.$row1['loan_id'].'">
									<input type="hidden" name="book_id" value="'.$row1['book_id'].'">
									<input type="hidden" name="command" value="kembali">
									<a href="javascript: submitFormReturn('.$row['0'].');">Return</a>
								</form>';
								} else {
									echo '<form action="index.php" method="post" id="borrowform'.$row['0'].'">
									<input type="hidden" name="book_id" value="'.$row['0'].'">
									<input type="hidden" name="command" value="pinjam">
									<a href="javascript: submitFormBorrow('.$row['0'].');">Borrow</a>
									</form>';
								}
							} else {
								echo 'Out of Stock';
							}
						}
						echo "</div>
					</div>
				</div>
			</div>";
		} else {
			echo 
			"<div class='col s3'>
			<div class='card horizontal small'>
				<div class='card-image'>
					<img src='".$row['1']."'>
				</div>
				<div class='card-stacked'>
					<div class='card-content'>
						<p>";
						if($row['6'] <= 5)
							echo "<span class='new badge red' data-badge-caption='remaining'>".$row['6']."</span></p>";
						else
							echo "<span class='new badge' data-badge-caption='remaining'>".$row['6']."</span></p>";
						echo "<br>
						<br>
						<p>".$row['2']."</p>
						<p>-- by <b>".$row['3']."</b></p>
						<p>-- published by <b>".$row['4']."</b></p>
					</div>
					<div class='card-action'>";
						echo "<a href='book.php?bookid=" . $row['0'] . "'>Details</a></br>";
						if($_SESSION['role'] == 'user') {
							$conn = connectDB();
							$user_id = $_SESSION['user_id'];
							$book_id = $row['0'];
							$sql1 = "SELECT * FROM loan WHERE user_id='$user_id' AND book_id=$book_id";
							$result1 = mysqli_query($conn,$sql1);
							$row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);

							if(!($row['6'] <= 0)) {
								if($row1['book_id'] == $row['0']) {
									echo '<form action="index.php" method="post" id="returnform'.$row['0'].'">
									<input type="hidden" name="loan_id" value="'.$row1['loan_id'].'">
									<input type="hidden" name="book_id" value="'.$row1['book_id'].'">
									<input type="hidden" name="command" value="kembali">
									<a href="javascript: submitFormReturn('.$row['0'].');">Return</a>
								</form>';
								} else {
									echo '<form action="index.php" method="post" id="borrowform'.$row['0'].'">
									<input type="hidden" name="book_id" value="'.$row['0'].'">
									<input type="hidden" name="command" value="pinjam">
									<a href="javascript: submitFormBorrow('.$row['0'].');">Borrow</a>
									</form>';
								}
							} else {
								if($row1['book_id'] == $row['0']) {
									echo '<form action="index.php" method="post" id="returnform'.$row['0'].'">
									<input type="hidden" name="loan_id" value="'.$row1['loan_id'].'">
									<input type="hidden" name="book_id" value="'.$row1['book_id'].'">
									<input type="hidden" name="command" value="kembali">
									<a href="javascript: submitFormReturn('.$row['0'].');">Return</a>
								</form>';
								} else {
									echo 'Out of Stock';
								}
							}
						}
			echo "</div>
		</div>
	</div>
</div>";
}
}
?>
<script>
	function submitFormBorrow(book_id){
		var borrowform = "#borrowform" + book_id;
		$(borrowform).submit();
	}
	function submitFormReturn(book_id){
		var returnform = "#returnform" + book_id;
		$(returnform).submit();
	}
</script>
</body>
</html>