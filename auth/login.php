<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!empty($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
require_once __DIR__ . '/../config/db.php';
$message = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
	$email = trim($_POST['email'] ?? '');
	$password = trim($_POST['password'] ?? '');
	if ($email === "" || $password === "") {
		$message = "All fields are required.";
	} else {
		$stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
		if ($stmt) {
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows === 1) {
				$stmt->bind_result($id, $username, $email_db, $hashed_password, $role);
				$stmt->fetch();
				if (password_verify($password, $hashed_password)) {
					$_SESSION['user_id'] = $id;
					$_SESSION['username'] = $username;
					$_SESSION['role'] = $role;
					$stmt->close();
					$_SESSION['role'] = $role;
					$stmt->close();

                    if ($role === 'admin') {
                        header('Location: ../admin/index.php');
                    } elseif ($role === 'teacher') {
                        header('Location: ../teacher/index.php');
                    } elseif ($role === 'student') {
                        header('Location: ../student/index.php');
                    } else {
                        header('Location: ../index.php');
                    }
					exit();
				} else {
					$message = "Invalid email or password.";
				}
			} else {
				$message = "Invalid email or password.";
			}
			$stmt->close();
		} else {
			$message = "Prepare failed: " . $conn->error;
		}
	}
}
?>
<?php include '../includes/header.php'; ?>

<div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
    <div class="card w-full max-w-md bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold justify-center mb-6">Login to your account</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-error text-sm py-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div class="form-control w-full">
                    <label class="label" for="email">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" class="input input-bordered w-full" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                </div>

                <div class="form-control w-full">
                    <label class="label" for="password">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" class="input input-bordered w-full" required />
                </div>

                <button type="submit" class="btn btn-primary w-full mt-4">Login</button>
            </form>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-sm">Don't have an account? <a href="signup.php" class="link link-primary font-bold">Sign up now</a></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>