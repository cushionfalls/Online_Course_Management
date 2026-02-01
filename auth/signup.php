<?php
require_once __DIR__ . '/../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$user_name = trim($_POST['username']);
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);
	$fullname = trim($_POST['fullname']);

	if ($user_name == "" || $email == "" || $password == "" || $fullname == "") {
		$message = "All fields are required.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = "Invalid email format.";
	} elseif (strlen($password) < 8) {
		$message = "Password must be at least 8 characters long.";
	} elseif (!preg_match('/[A-Z]/', $password)) {
		$message = "Password must contain at least one uppercase letter.";
	} elseif (!preg_match('/[a-z]/', $password)) {
		$message = "Password must contain at least one lowercase letter.";
	} elseif (!preg_match('/[0-9]/', $password)) {
		$message = "Password must contain at least one number.";
	} elseif (!preg_match('/[\W]/', $password)) {
		$message = "Password must contain at least one special character.";
	} else {
		$hashed_password = password_hash($password, PASSWORD_BCRYPT);
		$stmt = $conn->prepare("INSERT INTO users (username, email, password, fullname) VALUES (?, ?, ?, ?)");
		if ($stmt) {
			$stmt->bind_param("ssss", $user_name, $email, $hashed_password, $fullname);
			if ($stmt->execute()) {
				header('Location: login.php');
				exit;
			}
			if ($conn->errno == 1062) {
				$message = "Username or email already exists.";
			} else {
				$message = "Database error: " . $conn->error;
			}
			$stmt->close();
		} else {
			$message = "Database error: " . $conn->error;
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Signup</title>
</head>
<body>
	<?php if ($message != "") { echo '<p style="color:red;">' . htmlspecialchars($message) . '</p>'; } ?>
	<form action="" method="post">
		<label>Username:</label>
		<input type="text" name="username" required><br><br>
		<label>Email:</label>
		<input type="email" name="email" required><br><br>
		<label>Password:</label>
		<input type="password" name="password" required><br><br>
		<label>Full Name:</label>
		<input type="text" name="fullname" required><br><br>
		<button type="submit">Signup</button>
	</form>
	<p><a href="login.php">Login</a></p>
</body>
</html>
