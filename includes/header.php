<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SVM Course Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-base-100 text-base-content font-sans">
    <div class="navbar bg-base-100 shadow-sm border-b border-base-200">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl font-bold tracking-tight">SVM Course Manager</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <?php if(isset($_SESSION['role'])): ?>
                    <li><div class="badge badge-outline mr-2"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></div></li>
                    <li><a href="../auth/logout.php?logout=1" class="btn btn-sm btn-error text-white">Logout</a></li>
                <?php else: ?>
                    <li><a href="../auth/login.php" class="btn btn-sm btn-primary">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="container mx-auto p-4 md:p-8 flex-grow">
