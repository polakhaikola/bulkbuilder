<?php
include '../includes/db_connect.php';
// Session started in header.php, but we need to start it here before include header if we want to redirect
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct, start session
                session_regenerate_id(true); // Security: prevent session fixation
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>
<!-- We include header AFTER logic to avoid "headers already sent" issues if we redirect -->
<!-- But we need header to not output anything before PHP logic. 
     Since header.php has HTML, we must include it ONLY if we are rendering the page. -->
<?php include '../includes/header.php'; ?>

<main class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card bg-dark text-light border-secondary shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            <h2 class="text-center mb-4 fw-bold">Welcome <span class="text-success">Back</span></h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control bg-dark border-secondary text-light" id="email" name="email" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control bg-dark border-secondary text-light" id="password" name="password" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-green btn-lg">Login</button>
                </div>
                
                <p class="text-center mt-3 text-muted">
                    Don't have an account? <a href="register.php" class="text-success text-decoration-none">Register</a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
