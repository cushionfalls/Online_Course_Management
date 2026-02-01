<?php
require_once __DIR__ . '/../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $user_name = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $fullname  = trim($_POST['fullname'] ?? '');

    // Validation
    if ($user_name === "" || $email === "" || $password === "" || $fullname === "") {
        $message = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    }
    elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    }
    elseif (!preg_match('/[A-Z]/', $password)) {
        $message = "Password must contain at least one uppercase letter.";
    }
    elseif (!preg_match('/[a-z]/', $password)) {
        $message = "Password must contain at least one lowercase letter.";
    }
    elseif (!preg_match('/[0-9]/', $password)) {
        $message = "Password must contain at least one number.";
    }
    else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password, fullname) VALUES (?, ?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param("ssss", $user_name, $email, $hashed_password, $fullname);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                if ($conn->errno === 1062) {
                    $message = "Username or email already exists.";
                } else {
                    $message = "Database error: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $message = "Prepare failed: " . $conn->error;
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="flex justify-center items-center py-10">
    <div class="card w-full max-w-lg bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold justify-center mb-2">Create an Account</h2>
            <p class="text-center text-gray-500 mb-6">Join us to start learning today</p>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-error text-sm py-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="post" class="space-y-4">
                <div class="form-control w-full">
                    <label class="label" for="fullname">
                        <span class="label-text">Full Name</span>
                    </label>
                    <input type="text" id="fullname" name="fullname" placeholder="John Doe" class="input input-bordered w-full" required />
                </div>

                <div class="form-control w-full">
                    <label class="label" for="username">
                        <span class="label-text">Username</span>
                    </label>
                    <input type="text" id="username" name="username" placeholder="johndoe123" class="input input-bordered w-full" required />
                </div>

                <div class="form-control w-full">
                    <label class="label" for="email">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" class="input input-bordered w-full" required />
                </div>

                <div class="form-control w-full">
                    <label class="label" for="password">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" id="password" name="password" placeholder="••••••••" class="input input-bordered w-full" required />
                </div>

                <button type="submit" class="btn btn-primary w-full mt-4">Signup</button>
            </form>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-sm">Already have an account? <a href="login.php" class="link link-primary font-bold">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
