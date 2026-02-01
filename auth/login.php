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

<div class="min-h-[calc(100vh-theme(spacing.16))] flex items-center justify-center bg-base-200 py-10 px-4">
    <div class="card lg:card-side bg-base-100 shadow-xl max-w-5xl w-full overflow-hidden">
        
        <!-- Left Side: Login Form -->
        <div class="card-body lg:w-1/2 p-8 md:p-12">
            <h2 class="text-3xl font-bold mb-6 text-center lg:text-left">Login to your account</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-error text-sm py-2 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-5">
                <div class="form-control w-full">
                    <label class="label" for="email">
                        <span class="label-text font-medium">Email</span>
                    </label>
                    <input type="email" id="email" name="email"  class="input input-bordered w-full" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                </div>

                <div class="form-control w-full">
                    <label class="label" for="password">
                        <span class="label-text font-medium">Password</span>
                    </label>
                    <input type="password" id="password" name="password"  class="input input-bordered w-full" required />
                </div>

                <button type="submit" class="btn btn-primary w-full text-lg">Login</button>
            </form>

            <div class="divider my-8">OR</div>

            <div class="text-center">
                <p class="text-gray-600">Don't have an account? <a href="signup.php" class="link link-primary font-bold">Sign up now</a></p>
            </div>
        </div>

        <div class="card-body lg:w-1/2 bg-primary text-primary-content flex flex-col justify-center p-8 md:p-12 relative overflow-hidden">

            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                 <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="2" fill="none"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative z-10">
                <h1 class="text-4xl font-bold mb-6">Welcome to Learning</h1>
                <p class="text-lg mb-6 opacity-90 leading-relaxed">
                    Empowering students with quality education and innovative learning experiences. Join our community and unlock your potential.
                </p>
                <ul class="space-y-3 opacity-90">
                    <li class="flex items-start gap-2">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Access to comprehensive course materials</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Interactive learning experiences</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Expert instructors and support</span>
                    </li>
                     <li class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Track your progress and achievements</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>