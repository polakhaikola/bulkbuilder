<?php
include '../includes/db_connect.php';
include '../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Fetch recipe
$stmt = $conn->prepare("SELECT r.*, u.username FROM recipes r JOIN users u ON r.author_id = u.id WHERE r.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container py-5 text-center'><h3>Recipe not found.</h3></div>";
    include '../includes/footer.php';
    exit();
}

$recipe = $result->fetch_assoc();

// Security: If pending/rejected, only show to author or mod/admin
if ($recipe['status'] !== 'approved') {
    $can_view = false;
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'mod' || $_SESSION['user_id'] == $recipe['author_id']) {
            $can_view = true;
        }
    }
    if (!$can_view) {
        echo "<div class='container py-5 text-center'><h3>Access Denied.</h3></div>";
        include '../includes/footer.php';
        exit();
    }
}
?>

<main class="container py-5">
    <div class="row">
        <!-- Left Column: Image & Macros -->
        <div class="col-lg-5 mb-4">
            <?php 
                $img = $recipe['image'] && file_exists("../" . $recipe['image']) ? "../" . $recipe['image'] : "https://placehold.co/600x400/1e1e1e/28a745?text=No+Image"; 
                if (strpos($recipe['image'], 'http') === 0) $img = $recipe['image'];
            ?>
            <img src="<?php echo htmlspecialchars($img); ?>" class="img-fluid rounded shadow mb-4 w-100" style="object-fit: cover; max-height: 400px;" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
            
            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Macros per Serving</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3 border-end border-secondary">
                            <h4 class="text-success fw-bold mb-0"><?php echo $recipe['calories']; ?></h4>
                            <small class="text-muted">Calories</small>
                        </div>
                        <div class="col-3 border-end border-secondary">
                            <h4 class="text-light fw-bold mb-0"><?php echo $recipe['protein']; ?>g</h4>
                            <small class="text-muted">Protein</small>
                        </div>
                        <div class="col-3 border-end border-secondary">
                            <h4 class="text-light fw-bold mb-0"><?php echo $recipe['carbs']; ?>g</h4>
                            <small class="text-muted">Carbs</small>
                        </div>
                        <div class="col-3">
                            <h4 class="text-light fw-bold mb-0"><?php echo $recipe['fats']; ?>g</h4>
                            <small class="text-muted">Fats</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($recipe['status'] === 'approved'): ?>
                    <a href="/gym-bulk-recipes/tracker/dashboard.php?prefill_recipe_id=<?php echo $recipe['id']; ?>" class="btn btn-custom-green w-100 mt-3">
                        <i class="fas fa-plus-circle me-2"></i>Log to Tracker
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/gym-bulk-recipes/auth/login.php" class="btn btn-outline-secondary w-100 mt-3">Login to Log</a>
            <?php endif; ?>
        </div>

        <!-- Right Column: Details -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h1 class="fw-bold mb-0"><?php echo htmlspecialchars($recipe['title']); ?></h1>
                <?php if ($recipe['status'] !== 'approved'): ?>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><?php echo ucfirst($recipe['status']); ?></span>
                <?php endif; ?>
            </div>
            
            <p class="text-muted mb-4">
                By <strong class="text-light"><?php echo htmlspecialchars($recipe['username']); ?></strong> 
                &bull; Posted on <?php echo date('M d, Y', strtotime($recipe['created_at'])); ?>
            </p>

            <p class="lead mb-5"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>

            <div class="mb-5">
                <h4 class="section-title"><i class="fas fa-carrot me-2 text-success"></i>Ingredients</h4>
                <div class="bg-card p-4 rounded border border-secondary">
                    <?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?>
                </div>
            </div>

            <div class="mb-5">
                <h4 class="section-title"><i class="fas fa-list-ol me-2 text-success"></i>Instructions</h4>
                <div class="bg-card p-4 rounded border border-secondary">
                    <?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
