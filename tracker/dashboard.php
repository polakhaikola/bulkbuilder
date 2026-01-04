<?php
include '../includes/db_connect.php';
session_start();

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// Date Navigation
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date . ' -1 day'));
$next_date = date('Y-m-d', strtotime($date . ' +1 day'));
$display_date = date('l, M d', strtotime($date));

// Handle Water Log
if (isset($_POST['update_water'])) {
    $change = intval($_POST['change']);
    // Check if entry exists
    $w_check = $conn->prepare("SELECT glasses FROM water_logs WHERE user_id = ? AND log_date = ?");
    $w_check->bind_param("is", $user_id, $date);
    $w_check->execute();
    $w_res = $w_check->get_result();

    if ($w_res->num_rows > 0) {
        $cur_water = $w_res->fetch_assoc()['glasses'];
        $new_water = max(0, $cur_water + $change);
        $w_up = $conn->prepare("UPDATE water_logs SET glasses = ? WHERE user_id = ? AND log_date = ?");
        $w_up->bind_param("iis", $new_water, $user_id, $date);
        $w_up->execute();
        $w_up->close();
    } else {
        $new_water = max(0, $change);
        $w_in = $conn->prepare("INSERT INTO water_logs (user_id, glasses, log_date) VALUES (?, ?, ?)");
        $w_in->bind_param("iis", $user_id, $new_water, $date);
        $w_in->execute();
        $w_in->close();
    }
    $w_check->close();
    // Redirect to avoid resubmission
    header("Location: dashboard.php?date=$date");
    exit();
}

// Handle Delete
if (isset($_POST['delete_log_id'])) {
    $log_id = intval($_POST['delete_log_id']);
    $del_stmt = $conn->prepare("DELETE FROM daily_logs WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $log_id, $user_id);
    if ($del_stmt->execute()) {
        $msg = "Entry deleted.";
    }
    $del_stmt->close();
}

// Handle Add Log
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_log'])) {
    $food_name = trim($_POST['food_name']);
    $calories = intval($_POST['calories']);
    $protein = floatval($_POST['protein']);
    $carbs = floatval($_POST['carbs']);
    $fats = floatval($_POST['fats']);
    $quantity = 1.0; // Simplification for now
    $recipe_id = !empty($_POST['recipe_id']) ? intval($_POST['recipe_id']) : NULL;

    if ($food_name && $calories > 0) {
        $stmt = $conn->prepare("INSERT INTO daily_logs (user_id, recipe_id, food_name, quantity, calories, protein, carbs, fats, log_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisidddds", $user_id, $recipe_id, $food_name, $quantity, $calories, $protein, $carbs, $fats, $date);

        if ($stmt->execute()) {
            $msg = "Logged successfully!";
        } else {
            $error = "Error logging food.";
        }
        $stmt->close();
    } else {
        $error = "Please provide valid food details.";
    }
}

// Fetch Today's Totals
$totals_sql = "SELECT SUM(calories) as total_cal, SUM(protein) as total_pro, SUM(carbs) as total_carbs, SUM(fats) as total_fats FROM daily_logs WHERE user_id = ? AND log_date = ?";
$t_stmt = $conn->prepare($totals_sql);
$t_stmt->bind_param("is", $user_id, $date);
$t_stmt->execute();
$totals = $t_stmt->get_result()->fetch_assoc();
$t_stmt->close();

// Fetch Water
$w_sql = "SELECT glasses FROM water_logs WHERE user_id = ? AND log_date = ?";
$w_stmt = $conn->prepare($w_sql);
$w_stmt->bind_param("is", $user_id, $date);
$w_stmt->execute();
$water_res = $w_stmt->get_result();
$cur_water = ($water_res->num_rows > 0) ? $water_res->fetch_assoc()['glasses'] : 0;
$w_stmt->close();

// Fetch Goals from User Profile
$u_stmt = $conn->prepare("SELECT goal, target_calories, target_protein, target_carbs, target_fats FROM users WHERE id = ?");
$u_stmt->bind_param("i", $user_id);
$u_stmt->execute();
$u_goals = $u_stmt->get_result()->fetch_assoc();
$u_stmt->close();

$goal_cal = ($u_goals['target_calories'] > 0) ? $u_goals['target_calories'] : 2500;
$goal_pro = ($u_goals['target_protein'] > 0) ? $u_goals['target_protein'] : 150;
$goal_carbs = ($u_goals['target_carbs'] > 0) ? $u_goals['target_carbs'] : 300;
$goal_fats = ($u_goals['target_fats'] > 0) ? $u_goals['target_fats'] : 70;

// Goal Display Logic
$user_goal = $u_goals['goal'] ?? 'bulk';
$goal_icon = '💪';
$goal_color = 'success';
$goal_label = 'Bulking Up';
switch ($user_goal) {
    case 'cut':
        $goal_icon = '🔥';
        $goal_color = 'warning';
        $goal_label = 'Cutting Down';
        break;
    case 'recomp':
        $goal_icon = '⚖️';
        $goal_color = 'info';
        $goal_label = 'Recomposition';
        break;
    case 'beginner':
        $goal_icon = '🏋️';
        $goal_color = 'primary';
        $goal_label = 'Getting Started';
        break;
}

// Current Values (Default to 0 if null)
$cur_cal = $totals['total_cal'] ?? 0;
$cur_pro = $totals['total_pro'] ?? 0;
$cur_carbs = $totals['total_carbs'] ?? 0;
$cur_fats = $totals['total_fats'] ?? 0;

// Progress & Motivation Logic
$pct_cal = min(($cur_cal / $goal_cal) * 100, 100);
$pct_pro = min(($cur_pro / $goal_pro) * 100, 100);

$motivation = "Let's crush today's targets!";
if ($pct_pro >= 80) {
    $motivation = "Protein goal nearly crushed! 🔥";
} elseif ($pct_cal >= 90) {
    if ($user_goal == 'cut' && $cur_cal > $goal_cal) {
        $motivation = "Watch those calories! 🛑";
    } else {
        $motivation = "Energy targets hit! Great work.";
    }
} elseif ($pct_pro < 30 && date('H') > 15) {
    $motivation = "Don't forget your protein! 🥩";
}

// Helper for Progress Color
function getProgressColor($pct)
{
    if ($pct < 50)
        return 'danger';
    if ($pct < 80)
        return 'warning';
    return 'success';
}

// Fetch Approved Recipes for Dropdown
$recipes_res = $conn->query("SELECT id, title, calories, protein, carbs, fats FROM recipes WHERE status = 'approved' ORDER BY title ASC");
?>
<?php include '../includes/header.php'; ?>

<main class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">
                <span class="me-2"><?php echo $goal_icon; ?></span> <?php echo $goal_label; ?>
            </h2>
            <p class="text-<?php echo $goal_color; ?> fw-bold mb-0"><?php echo $motivation; ?></p>
        </div>
        <div class="text-end">
            <div class="btn-group">
                <a href="?date=<?php echo $prev_date; ?>" class="btn btn-outline-secondary btn-sm"><i
                        class="fas fa-chevron-left"></i></a>
                <a href="#" class="btn btn-outline-light btn-sm disabled fw-bold"><?php echo $display_date; ?></a>
                <a href="?date=<?php echo $next_date; ?>" class="btn btn-outline-secondary btn-sm"><i
                        class="fas fa-chevron-right"></i></a>
            </div>
            <?php if ($date == date('Y-m-d')): ?>
                <div class="text-success small fw-bold mt-1">TODAY</div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <!-- Calories -->
        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow h-100 text-center position-relative overflow-hidden">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h6 class="text-muted text-uppercase mb-3">Daily Energy</h6>
                    <div class="position-relative d-inline-block" style="width: 120px; height: 120px;">
                        <canvas id="calChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <h4 class="mb-0 fw-bold text-light"><?php echo number_format($cur_cal); ?></h4>
                        </div>
                    </div>
                    <!-- Explicit Label Below Chart -->
                    <h6 class="text-white fw-bold mt-3 mb-1">CALORIES</h6>

                    <?php $rem_cal = max(0, $goal_cal - $cur_cal); ?>
                    <div class="w-100 px-4 mt-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-success"><i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                Consumed</span>
                            <span class="text-light fw-bold"><?php echo number_format($cur_cal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-white-50"><i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                Remaining</span>
                            <span class="text-light fw-bold"><?php echo number_format($rem_cal); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Protein -->
        <div class="col-md-3">
            <div
                class="card bg-dark border-secondary shadow h-100 p-3 d-flex flex-column justify-content-center text-center">
                <div class="d-flex justify-content-between align-items-end mb-1">
                    <h3 class="fw-bold text-<?php echo getProgressColor((($cur_pro / $goal_pro) * 100)); ?> mb-0">
                        <?php echo $cur_pro; ?>g</h3>
                    <small class="text-white-50">/ <?php echo $goal_pro; ?>g</small>
                </div>
                <div class="progress bg-secondary mb-2" style="height: 10px;">
                    <div class="progress-bar bg-<?php echo getProgressColor((($cur_pro / $goal_pro) * 100)); ?>"
                        role="progressbar" style="width: <?php echo min(($cur_pro / $goal_pro) * 100, 100); ?>%"></div>
                </div>
                <h6 class="text-white fw-bold mt-1">PROTEIN</h6>
            </div>
        </div>
        <!-- Carbs -->
        <div class="col-md-3">
            <div
                class="card bg-dark border-secondary shadow h-100 p-3 d-flex flex-column justify-content-center text-center">
                <div class="d-flex justify-content-between align-items-end mb-1">
                    <h3 class="fw-bold text-<?php echo getProgressColor((($cur_carbs / $goal_carbs) * 100)); ?> mb-0">
                        <?php echo $cur_carbs; ?>g</h3>
                    <small class="text-white-50">/ <?php echo $goal_carbs; ?>g</small>
                </div>
                <div class="progress bg-secondary mb-2" style="height: 10px;">
                    <div class="progress-bar bg-<?php echo getProgressColor((($cur_carbs / $goal_carbs) * 100)); ?>"
                        role="progressbar" style="width: <?php echo min(($cur_carbs / $goal_carbs) * 100, 100); ?>%">
                    </div>
                </div>
                <h6 class="text-white fw-bold mt-1">CARBS</h6>
            </div>
        </div>
        <!-- Fats -->
        <div class="col-md-3">
            <div
                class="card bg-dark border-secondary shadow h-100 p-3 d-flex flex-column justify-content-center text-center">
                <div class="d-flex justify-content-between align-items-end mb-1">
                    <h3 class="fw-bold text-<?php echo getProgressColor((($cur_fats / $goal_fats) * 100)); ?> mb-0">
                        <?php echo $cur_fats; ?>g</h3>
                    <small class="text-white-50">/ <?php echo $goal_fats; ?>g</small>
                </div>
                <div class="progress bg-secondary mb-2" style="height: 10px;">
                    <div class="progress-bar bg-<?php echo getProgressColor((($cur_fats / $goal_fats) * 100)); ?>"
                        role="progressbar" style="width: <?php echo min(($cur_fats / $goal_fats) * 100, 100); ?>%">
                    </div>
                </div>
                <h6 class="text-white fw-bold mt-1">FATS</h6>
            </div>
        </div>
    </div>

    <!-- Water Tracker -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-info border-0 shadow">
                <div class="card-body d-flex justify-content-between align-items-center text-dark">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tint fa-2x me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Hydration Tracker</h5>
                            <small>Stay hydrated! Goal: 8 glasses</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="update_water" value="1">
                            <input type="hidden" name="change" value="-1">
                            <button type="submit" class="btn btn-light rounded-circle btn-sm"
                                style="width:32px; height:32px;">-</button>
                        </form>
                        <h3 class="mx-3 fw-bold mb-0"><?php echo $cur_water; ?></h3>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="update_water" value="1">
                            <input type="hidden" name="change" value="1">
                            <button type="submit" class="btn btn-light rounded-circle btn-sm"
                                style="width:32px; height:32px;">+</button>
                        </form>
                        <span class="ms-2">Glasses</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Add Log Form -->
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-success shadow h-100">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-plus me-2"></i> Log Food
                </div>
                <div class="card-body">
                    <form method="POST" action="dashboard.php?date=<?php echo $date; ?>" id="logForm">
                        <input type="hidden" name="add_log" value="1">

                        <!-- Recipe Selector -->
                        <div class="mb-3">
                            <label class="form-label text-white fw-bold small text-uppercase">From Recipe
                                Database</label>
                            <select class="form-select bg-black text-light border-secondary" id="recipeSelect"
                                name="recipe_id">
                                <option value="" selected>-- Select a Recipe (Auto-fill) --</option>
                                <?php while ($r = $recipes_res->fetch_assoc()): ?>
                                    <option value="<?php echo $r['id']; ?>"
                                        data-title="<?php echo htmlspecialchars($r['title']); ?>"
                                        data-cal="<?php echo $r['calories']; ?>" data-pro="<?php echo $r['protein']; ?>"
                                        data-carbs="<?php echo $r['carbs']; ?>" data-fats="<?php echo $r['fats']; ?>">
                                        <?php echo htmlspecialchars($r['title']); ?>
                                        (<?php echo $r['calories']; ?> cal)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="text-center text-muted mb-3 small">- OR Custom Entry -</div>

                        <div class="mb-2">
                            <label class="form-label text-white fw-bold small">Food Item Name</label>
                            <input type="text" class="form-control bg-dark border-secondary text-light" name="food_name"
                                id="foodName" placeholder="e.g. Banana" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-white fw-bold small">Calories (kcal)</label>
                                <input type="number" class="form-control bg-dark border-secondary text-light"
                                    name="calories" id="calInput" placeholder="0" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white fw-bold small">Protein (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="protein" id="proInput" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white fw-bold small">Carbs (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="carbs" id="carbsInput" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-white fw-bold small">Fats (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="fats" id="fatsInput" placeholder="0">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-custom-green w-100">Add to Log</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary shadow">
                <div class="card-header border-secondary fw-bold">
                    <i class="fas fa-history me-2"></i> Today's Logs
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Food Item</th>
                                <th>Calories</th>
                                <th>Protein</th>
                                <th>Carbs</th>
                                <th>Fats</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $logs_sql = "SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ? ORDER BY id DESC";
                            $l_stmt = $conn->prepare($logs_sql);
                            $l_stmt->bind_param("is", $user_id, $date);
                            $l_stmt->execute();
                            $logs_res = $l_stmt->get_result();
                            ?>
                            <?php if ($logs_res->num_rows > 0): ?>
                                <?php while ($log = $logs_res->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($log['food_name']); ?>
                                            <?php if ($log['recipe_id']): ?>
                                                <i class="fas fa-link text-success small ms-1" title="From Recipe"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?php echo $log['calories']; ?></td>
                                        <td><?php echo $log['protein']; ?>g</td>
                                        <td><?php echo $log['carbs']; ?>g</td>
                                        <td><?php echo $log['fats']; ?>g</td>
                                        <td class="text-end">
                                            <form method="POST" onsubmit="return confirm('Delete this entry?');">
                                                <input type="hidden" name="delete_log_id" value="<?php echo $log['id']; ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No food logged today. Start eating!
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php $l_stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Pass PHP Data to JS -->
<script>
    const chartData = {
        cal: <?php echo $cur_cal; ?>,
        goal: <?php echo $goal_cal; ?>
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/tracker.js"></script>

<?php include '../includes/footer.php'; ?>