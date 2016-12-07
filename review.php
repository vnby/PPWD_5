<?php
session_start();
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

	$conn = connectDB();
	$review = $_POST['reviewtextarea'];
	$book_id = $_POST['bookid'];

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

?>