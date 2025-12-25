<?php
include '../includes/db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email or username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Default role

            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $success = "Registration successful! <a href='login.php' class='alert-link'>Login here</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>
<?php include '../includes/header.php'; ?>

<main class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card bg-dark text-light border-secondary shadow-lg p-4" style="max-width: 450px; width: 100%;">
        <div class="card-body">
            <h2 class="text-center mb-4 fw-bold">Join the <span class="text-success">Bulk</span></h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-light" id="username" name="username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control bg-dark border-secondary text-light" id="email" name="email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control bg-dark border-secondary text-light" id="password" name="password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control bg-dark border-secondary text-light" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-green btn-lg">Register Now</button>
                </div>
                
                <p class="text-center mt-3 text-muted">
                    Already have an account? <a href="login.php" class="text-success text-decoration-none">Login</a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
