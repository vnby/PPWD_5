<?php
session_start();

if(!isset($_SESSION['login_user'])) {
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
		<li><a class="btn-flat disabled">Role:</a></li>
		<li><a class="btn-flat disabled"><?php echo $_SESSION['role'] ?></a></li>
		<li class="divider"></li>
		<li><a href="logout.php">Logout</a></li>
	</ul>
	<div class="navbar-fixed">
		<nav>
			<div class="nav-wrapper teal lighten-1">
				<a href="index.php" class="brand-logo">.::Personal Library::.</a>
				<ul id="nav-mobile" class="right hide-on-med-and-down">
					<?php
					if($_SESSION['role'] == 'admin')
						echo '<li><a href="addbook.php"><i class="material-icons right">library_books</i>Add New Book</a></li>';

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
									<p>".$row['2']."</p>
									<p>---</p>
									<p>by</p>
									<p><b>".$row['3']."</b></p>
								</div>
								<div class='card-action'>
									<a href='book.php?bookid=" . $row['0'] . "'>Details</a>
								</div>
							</div>
						</div>
					</div>";
					echo '</div>';
				} else {
					echo 
					"<div class='col s3'>
						<div class='card horizontal small'>
							<div class='card-image'>
								<img src='".$row['1']."'>
							</div>
							<div class='card-stacked'>
								<div class='card-content'>
									<p>".$row['2']."</p>
									<p>---</p>
									<p>by</p>
									<p><b>".$row['3']."</b></p>
								</div>
								<div class='card-action'>
									<a href='book.php?bookid=" . $row['0'] . "'>Details</a>
								</div>
							</div>
						</div>
					</div>";
				}
			}
			?>
</body>
</html>

