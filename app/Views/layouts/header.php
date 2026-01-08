<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo SITENAME; ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Outfit:wght@500;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo URLROOT; ?>"><i class="fas fa-dumbbell me-2"></i>
                <?php echo SITENAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/recipes">Recipes</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-success" href="<?php echo URLROOT; ?>/recipes/add"><i
                                    class="fas fa-plus-circle me-1"></i>Add Recipe</a>
                        </li>
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle btn btn-outline-light px-3 py-1 rounded-pill" href="#"
                                role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/profile">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="<?php echo URLROOT; ?>/users/logout"><i
                                            class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo URLROOT; ?>/users/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo URLROOT; ?>/users/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div style="height: 76px;"></div>