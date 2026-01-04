<?php
include '../includes/db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $goal = $_POST['goal'];

    // Basic Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($goal)) {
        $error = "All fields are required, including your goal.";
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
            $role = 'user';

            // Set Targets based on Goal
            $t_cal = 0;
            $t_pro = 0;
            $t_carbs = 0;
            $t_fats = 0;
            switch ($goal) {
                case 'bulk':
                    $t_cal = 3200;
                    $t_pro = 220;
                    $t_carbs = 400;
                    $t_fats = 90;
                    break;
                case 'cut':
                    $t_cal = 2200;
                    $t_pro = 180;
                    $t_carbs = 200;
                    $t_fats = 60;
                    break;
                case 'recomp':
                    $t_cal = 2800;
                    $t_pro = 200;
                    $t_carbs = 300;
                    $t_fats = 80;
                    break;
                case 'beginner':
                    $t_cal = 2800;
                    $t_pro = 180;
                    $t_carbs = 350;
                    $t_fats = 80;
                    break;
                default:
                    $t_cal = 2500;
                    $t_pro = 150;
                    $t_carbs = 300;
                    $t_fats = 70; // Fallback
            }

            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role, goal, target_calories, target_protein, target_carbs, target_fats) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssssiddd", $username, $email, $hashed_password, $role, $goal, $t_cal, $t_pro, $t_carbs, $t_fats);

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

<style>
    .goal-option input[type="radio"] {
        display: none;
    }

    .goal-option label {
        cursor: pointer;
        transition: all 0.2s;
    }

    .goal-option input[type="radio"]:checked+label {
        border-color: #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1);
    }

    .goal-option:hover label {
        border-color: #6c757d !important;
        transform: translateY(-2px);
    }
</style>

<main class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card bg-dark text-light border-secondary shadow-lg p-4" style="max-width: 600px; width: 100%;">
        <div class="card-body">
            <h2 class="text-center mb-1 fw-bold">Join the <span class="text-success">Bulk</span></h2>
            <p class="text-center text-muted mb-4">Create your account and start your journey.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="needs-validation" novalidate>
                <!-- Goal Selection -->
                <h5 class="mb-3 text-center fw-bold">Select Your Primary Goal</h5>
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="goal-option text-center">
                            <input type="radio" name="goal" id="goal_bulk" value="bulk" checked>
                            <label class="card bg-dark border-secondary h-100 py-3" for="goal_bulk">
                                <div class="fs-1">💪</div>
                                <div class="fw-bold text-success">Bulk</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Build Muscle Size</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="goal-option text-center">
                            <input type="radio" name="goal" id="goal_cut" value="cut">
                            <label class="card bg-dark border-secondary h-100 py-3" for="goal_cut">
                                <div class="fs-1">🔥</div>
                                <div class="fw-bold text-warning">Cut</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Lose Body Fat</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="goal-option text-center">
                            <input type="radio" name="goal" id="goal_recomp" value="recomp">
                            <label class="card bg-dark border-secondary h-100 py-3" for="goal_recomp">
                                <div class="fs-1">⚖️</div>
                                <div class="fw-bold text-info">Recomp</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Maintain & Tone</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="goal-option text-center">
                            <input type="radio" name="goal" id="goal_beginner" value="beginner">
                            <label class="card bg-dark border-secondary h-100 py-3" for="goal_beginner">
                                <div class="fs-1">🏋️</div>
                                <div class="fw-bold text-primary">Beginner</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Getting Started</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-user"></i></span>
                        <input type="text" class="form-control bg-dark border-secondary text-light" id="username"
                            name="username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control bg-dark border-secondary text-light" id="email"
                            name="email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-lock"></i></span>
                        <input type="password" class="form-control bg-dark border-secondary text-light" id="password"
                            name="password" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-lock"></i></span>
                        <input type="password" class="form-control bg-dark border-secondary text-light"
                            id="confirm_password" name="confirm_password" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-green btn-lg">Start My Journey</button>
                </div>

                <p class="text-center mt-3 text-muted">
                    Already have an account? <a href="login.php" class="text-success text-decoration-none">Login</a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>