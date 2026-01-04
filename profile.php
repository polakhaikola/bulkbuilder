<?php
include 'includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (password_verify($current_pass, $res['password'])) {
            $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $up_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $up_stmt->bind_param("si", $new_hashed, $user_id);
            if ($up_stmt->execute()) {
                $msg = "Password updated successfully!";
            } else {
                $error = "Error updating password.";
            }
            $up_stmt->close();
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

// Handle Primary Goal Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_primary_goal'])) {
    $new_goal = $_POST['goal'];
    $t_cal = 0;
    $t_pro = 0;
    $t_carbs = 0;
    $t_fats = 0;

    switch ($new_goal) {
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
            $t_fats = 70;
    }

    $g_up = $conn->prepare("UPDATE users SET goal = ?, target_calories = ?, target_protein = ?, target_carbs = ?, target_fats = ? WHERE id = ?");
    $g_up->bind_param("sidddi", $new_goal, $t_cal, $t_pro, $t_carbs, $t_fats, $user_id);

    if ($g_up->execute()) {
        $msg = "Primary goal updated! Targets reset.";
    } else {
        $error = "Error updating goal.";
    }
    $g_up->close();
}

// Handle Goals Update (Manual Overwrite)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_goals'])) {
    $t_cal = intval($_POST['target_calories']);
    $t_pro = floatval($_POST['target_protein']);
    $t_carbs = floatval($_POST['target_carbs']);
    $t_fats = floatval($_POST['target_fats']);

    $g_stmt = $conn->prepare("UPDATE users SET target_calories = ?, target_protein = ?, target_carbs = ?, target_fats = ? WHERE id = ?");
    $g_stmt->bind_param("idddi", $t_cal, $t_pro, $t_carbs, $t_fats, $user_id);

    if ($g_stmt->execute()) {
        $msg = "Nutritional goals updated!";
    } else {
        $error = "Error updating goals.";
    }
    $g_stmt->close();
}

// Fetch User Info
$u_stmt = $conn->prepare("SELECT username, email, role, created_at, goal, target_calories, target_protein, target_carbs, target_fats FROM users WHERE id = ?");
$u_stmt->bind_param("i", $user_id);
$u_stmt->execute();
$user = $u_stmt->get_result()->fetch_assoc();
$u_stmt->close();

// Fetch Stats
$stats = [];
// Recipe Count
$r_stmt = $conn->prepare("SELECT COUNT(*) as count FROM recipes WHERE author_id = ?");
$r_stmt->bind_param("i", $user_id);
$r_stmt->execute();
$stats['recipes'] = $r_stmt->get_result()->fetch_assoc()['count'];
$r_stmt->close();
// Log Days Count
$l_stmt = $conn->prepare("SELECT COUNT(DISTINCT log_date) as count FROM daily_logs WHERE user_id = ?");
$l_stmt->bind_param("i", $user_id);
$l_stmt->execute();
$stats['days_tracked'] = $l_stmt->get_result()->fetch_assoc()['count'];
$l_stmt->close();

?>
<?php include 'includes/header.php'; ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <!-- Profile Info -->
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow h-100">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-user-circle fa-6x text-muted"></i>
                    </div>
                    <h3 class="fw-bold text-light mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                    <span
                        class="badge bg-success mb-3 text-uppercase"><?php echo htmlspecialchars($user['role']); ?></span>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($user['email']); ?></p>

                    <div class="d-flex justify-content-around text-start border-top border-secondary pt-4">
                        <div>
                            <h5 class="fw-bold text-light mb-0"><?php echo $stats['recipes']; ?></h5>
                            <small class="text-muted">Recipes</small>
                        </div>
                        <div>
                            <h5 class="fw-bold text-light mb-0"><?php echo $stats['days_tracked']; ?></h5>
                            <small class="text-muted">Days Tracked</small>
                        </div>
                    </div>

                    <p class="mt-4 small text-muted">Member since
                        <?php echo date('M Y', strtotime($user['created_at'])); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="col-lg-6">
            <div class="card bg-dark border-secondary shadow">
                <div class="card-header border-secondary fw-bold text-light">
                    <i class="fas fa-cog me-2"></i> Account Settings
                </div>
                <div class="card-body p-4">
                    <?php if ($msg): ?>
                        <div class="alert alert-success"><?php echo $msg; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="mb-4 pb-4 border-bottom border-secondary">
                        <h5 class="text-light mb-3">Primary Goal</h5>
                        
                        <div class="p-3 bg-black rounded mb-3 border border-secondary d-flex align-items-center">
                            <?php 
                                $goal_icon = '💪'; $goal_color = 'success'; $goal_label = 'Bulking';
                                switch($user['goal']) {
                                    case 'cut': $goal_icon = '🔥'; $goal_color = 'warning'; $goal_label = 'Cutting'; break;
                                    case 'recomp': $goal_icon = '⚖️'; $goal_color = 'info'; $goal_label = 'Recomposition'; break;
                                    case 'beginner': $goal_icon = '🏋️'; $goal_color = 'primary'; $goal_label = 'Beginner'; break;
                                }
                            ?>
                            <span class="display-6 me-3"><?php echo $goal_icon; ?></span>
                            <div>
                                <h4 class="fw-bold text-<?php echo $goal_color; ?> mb-0 text-uppercase"><?php echo $goal_label; ?></h4>
                                <small class="text-muted">Current Focus</small>
                            </div>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="update_primary_goal" value="1">
                            <label class="form-label text-muted small">Change Focus</label>
                            <div class="input-group">
                                <select class="form-select bg-dark text-light border-secondary" name="goal">
                                    <option value="bulk" <?php if($user['goal']=='bulk') echo 'selected'; ?>>💪 Bulk</option>
                                    <option value="cut" <?php if($user['goal']=='cut') echo 'selected'; ?>>🔥 Cut</option>
                                    <option value="recomp" <?php if($user['goal']=='recomp') echo 'selected'; ?>>⚖️ Recomp</option>
                                    <option value="beginner" <?php if($user['goal']=='beginner') echo 'selected'; ?>>🏋️ Beginner</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="submit">Update</button>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">*Updating goal will reset your nutritional targets below.</small>
                        </form>
                    </div>

                    <h5 class="text-light mb-3">Custom Nutritional Targets</h5>
                    <!-- Manual Override Form -->
                    <form method="POST" class="mb-5">
                        <input type="hidden" name="update_goals" value="1">
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Daily Calories</label>
                                <input type="number" class="form-control bg-dark border-secondary text-light"
                                    name="target_calories" value="<?php echo $user['target_calories']; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Protein (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="target_protein" value="<?php echo $user['target_protein']; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Carbs (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="target_carbs" value="<?php echo $user['target_carbs']; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Fats (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="target_fats" value="<?php echo $user['target_fats']; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-light w-100">Save Custom Targets</button>
                    </form>

                    <h5 class="text-light mb-3">Change Password</h5>
                    <form method="POST">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Current Password</label>
                            <input type="password" class="form-control bg-dark border-secondary text-light"
                                name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">New Password</label>
                            <input type="password" class="form-control bg-dark border-secondary text-light"
                                name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Confirm New Password</label>
                            <input type="password" class="form-control bg-dark border-secondary text-light"
                                name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-outline-light">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>