<?php
require_once __DIR__ . '/../config/db.php';

if (!empty($_GET['logout'])) {
	session_destroy();
	header('Location: login.php');
	exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);
	if ($email == "" || $password == "") {
		$message = "All fields are required.";
	} else {
		$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
		if ($stmt) {
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows == 1) {
				$stmt->bind_result($id, $username, $email_db, $hashed_password, $role);
				$stmt->fetch();
				if (password_verify($password, $hashed_password)) {
					$_SESSION['user_id'] = $id;
					$_SESSION['username'] = $username;
					$_SESSION['role'] = $role;
					$stmt->close();
					if ($role == 'admin') header('Location: ../dashboard/admin.php');
					elseif ($role == 'teacher') header('Location: ../dashboard/teacher.php');
					else header('Location: ../dashboard/student.php');
					exit;
				}
				$message = "Invalid email or password.";
			} else {
				$message = "Invalid email or password.";
			}
			$stmt->close();
		} else {
			$message = "Database error.";
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
</head>
<body>
	<?php if ($message != "") { echo '<p style="color:red;">' . htmlspecialchars($message) . '</p>'; } ?>
	<form action="" method="post">
		<label>Email:</label>
		<input type="email" name="email" required><br><br>
		<label>Password:</label>
		<input type="password" name="password" required><br><br>
		<button type="submit">Login</button>
	</form>
	<p><a href="signup.php">Sign up</a></p>
</body>
</html>
