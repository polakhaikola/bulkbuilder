<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Bulk Recipes | Fuel Your Gains</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Outfit:wght@500;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/bulkbuilder/css/style.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/bulkbuilder/index.php"><i class="fas fa-dumbbell me-2"></i>BulkBuilder</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a
                            class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"
                            href="/bulkbuilder/index.php">Home</a></li>

                    <li class="nav-item"><a
                            class="nav-link <?php echo ($current_page == 'list.php') ? 'active' : ''; ?>"
                            href="/bulkbuilder/recipes/list.php">Recipes</a></li>
                    <li class="nav-item"><a
                            class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>"
                            href="/bulkbuilder/tracker/dashboard.php">Tracker</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Logged In Links -->

                        <?php if ($_SESSION['role'] === 'writer' || $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'mod'): ?>
                            <li class="nav-item"><a class="nav-link text-success" href="/bulkbuilder/recipes/add.php"><i
                                        class="fas fa-plus-circle me-1"></i>Add Recipe</a></li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] === 'mod' || $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link text-warning" href="/bulkbuilder/mod/queue.php"><i
                                        class="fas fa-shield-alt me-1"></i>Mod Queue</a></li>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link text-danger" href="#"><i
                                        class="fas fa-users-cog me-1"></i>Manage Users</a></li>
                        <?php endif; ?>

                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle btn btn-outline-light px-3 py-1 rounded-pill" href="#"
                                role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Dashboard</a></li>
                                <li><a class="dropdown-item" href="/bulkbuilder/profile.php">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="/bulkbuilder/auth/logout.php"><i
                                            class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <!-- Guest Links -->
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link" href="/bulkbuilder/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-custom-green btn-sm px-4 ms-2"
                                href="/bulkbuilder/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Spacer for Fixed Navbar -->
    <div style="height: 76px;"></div>