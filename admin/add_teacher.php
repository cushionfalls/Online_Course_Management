<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../includes/header.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $fullname = trim($_POST['fullname']);

    if (empty($username) || empty($email) || empty($password) || empty($fullname)) {
        $message = "All fields are required.";
    } else {
        // Check if email or username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $message = "Username or Email already exists.";
        } else {
            $stmt->close();
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = 'teacher';
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, fullname, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $fullname, $role);
            
            if ($stmt->execute()) {
                $message = "Teacher added successfully!";
            } else {
                $message = "Error adding teacher: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <aside class="md:col-span-1">
        <div class="bg-base-200 p-4 rounded-lg h-full">
            <h3 class="font-bold text-lg mb-4 px-4">Admin Menu</h3>
             <ul class="menu bg-base-100 rounded-box w-full shadow-sm">
                <li><a href="add_course.php">Add Course & Assign Teacher</a></li>
                <li><a href="add_teacher.php" class="active">Add Teacher</a></li>
                <li><a href="edit.php">Manage Courses & Teachers</a></li>
                <li><a href="manage_students.php">View Students</a></li>
                <li><a href="delete.php">Delete Data</a></li>
            </ul>
        </div>
    </aside>

    <main class="md:col-span-3">
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Add New Teacher</h2>
                
                <?php if ($message != ""): ?>
                    <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?> text-sm py-2 mb-4">
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Full Name</span></label>
                            <input type="text" name="fullname" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Username</span></label>
                            <input type="text" name="username" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input type="email" name="email" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Password</span></label>
                            <input type="password" name="password" class="input input-bordered w-full" required />
                        </div>
                    </div>
                    
                    <div class="card-actions justify-end mt-6">
                        <button type="submit" class="btn btn-primary">Add Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>