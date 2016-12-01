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
		header("Location: book.php?bookid=$book_id");
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
		header("Location: book.php?bookid=$book_id");
	} else {
		die("Error: $sql3");
	}
	mysqli_close($conn);
}

function memberiReview($review, $book_id) {
	$conn = connectDB();

	$book_id = $book_id;
	$user_id = $_SESSION['user_id'];
	$dates = date('Y-m-d');
	$content = $review;
	$sql = "INSERT INTO review (book_id, user_id, date, content) VALUES ('$book_id','$user_id', '$dates', '$content')";

	if($result = mysqli_query($conn, $sql)) {
		echo "Terima kasih telah memberi review:) <br/>";
		header("Location: book.php?bookid=$book_id");
	} else {
		die("Error: $sql");
	}
	mysqli_close($conn);
}

function tambahBuku() {
	$conn = connectDB();

	$loan_id = $_POST['loan_id'];

	$book_id = $_POST['book_id'];
	$img_path = $_POST['img_path'];
	$title = $_POST['title'];
	$author = $_POST['author'];
	$publisher = $_POST['publisher'];
	$description = $_POST['description'];
	$quantity = $_POST['quantity'];
	$sql = "INSERT into book (book_id, img_path, title, author, publisher, description, quantity) values('$book_id','$img_path','$title','$author','$publisher','$description','$quantity')";

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
	<!-- <link rel="stylesheet" type="text/css" href="css/mycv.css" > -->
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
		<li><a class="btn-flat disabled">Role:</a></li>
		<li><a class="btn-flat disabled"><?php echo $_SESSION['role'] ?></a></li>
		<li class="divider"></li>
		<li><a href="logout.php">Logout</a></li>
	</ul>
	<div class="navbar-fixed">
		<nav>
			<div class="nav-wrapper">
				<a href="index.php" class="brand-logo">.::Personal Library::.</a>
				<ul id="nav-mobile" class="right hide-on-med-and-down">
					<li><a href="addbook.php"><i class="material-icons right">library_books</i>Add New Book</a></li>
					<li><a href="borrowed.php"><i class="material-icons right">library_books</i>List of Borrowed Book(s)</a></li>

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
					$user_id = $_SESSION['user_id'];
					$conn = connectDB();
					$sql = "SELECT * FROM book WHERE book_id='$book_id'";
					$sql1 = "SELECT loan_id FROM loan WHERE user_id=$user_id AND book_id=$book_id";
					$result = mysqli_query($conn, $sql);
					$result1 = mysqli_query($conn, $sql1);
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
					$row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC);

					echo '
						<div class="row">
							<div class="col s12">
								<h3>Book Details</h3>
							</div>
						</div>
						<div class="row">
							<div class="col s5 m4 l3">
								<img src='.$row['img_path'].' height=350 width=200>
							</div>
							<div class="col s7 m8 l9">
								<p><b>Title</b>: '.$row['title'].'</p>
								<p><b>Author</b>: '.$row['author'].'</p>
								<p><b>Publisher</b>: '.$row['publisher'].'</p>
								<p><b>Description</b>: '.$row['description'].'</p>
								<p><b>Quantity</b>: '.$row['quantity'].'</p>';
								if($row['quantity'] <= 0){
									echo '<p> Out of Stock </p>';
								} else{
									echo '<p><form action="book.php" method="post">
									<input type="hidden" name="book_id" value="'.$row['book_id'].'">
										<input type="hidden" name="command" value="pinjam">
										<button class="btn waves-effect waves-light" type="submit">Borrow This Book<i class="material-icons right">library_add</i></button>
									</form></p>';	
								}
								echo '
							</div>
						</row>
						<div class="row">

						</div>
						<div class="row">

						</div>

						<div class="row">
							<div class="col s12">
								<div class="row">
        							<div class="input-field col s12">
          								<form action="book.php" method="post" name="reviewform" id="reviewforms">
          									<textarea class="materialize-textarea" id="reviewtextareas" name="reviewtextarea" form="reviewforms"></textarea>
          									<label for="textarea1">Give your review about this book...</label>
											<input type="hidden" name="command" value="review">
											<input type="hidden" name="bookid" value="'.$row['book_id'].'">
          									<button class="btn waves-effect waves-light" type="submit" name="action" value="sbmit">Submit Review<i class="material-icons right">send</i>
 											</button>
          								</form>
       								</div>
      							</div>
							</div>
						</div>

						<div class="row">
							<div class="col s12">
								<h3>Review</h3>
								<div class="table-responsive">
									<table class="table striped centered">
										<thead>
											<tr>
												<th>Reviewer</th>
												<th>Review Date</th>
												<th>Review</th>
											</tr>
										</thead>
										<tbody>';
											$reviews = selectBookReview($book_id);
											while($row = mysqli_fetch_row($reviews)) {
												echo "<tr>";
												$foreachcounter = 0;
												foreach($row as $key => $value) {
													$foreachcounter = $foreachcounter + 1;
													if($foreachcounter == 1) {
														$username = getNameFromID($value);
														echo "<td>$username</td>";
													} else {
														echo "<td>$value</td>";
													}
												}
												echo "</tr>";
											}
											echo '
										</tbody>
									</table>
								</div>
							</div>
						</div>
					';
				} else {
					echo "bookid gada";
				}
			 ?>
	</div>
</body>
</html>

