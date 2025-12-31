<?php
include 'includes/db_connect.php';
session_start();

// Fetch 3 Latest Approved Recipes for "Featured" section
$feat_sql = "SELECT id, title, image, calories, protein FROM recipes WHERE status = 'approved' ORDER BY created_at DESC LIMIT 3";
$feat_res = $conn->query($feat_sql);
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="py-5 text-center container">
    <div class="row py-lg-5">
        <div class="col-lg-8 col-md-8 mx-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <h1 class="fw-light text-light mb-3">Welcome back, <span
                        class="text-success fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>
                <p class="lead text-muted mb-4">You're on the path to greatness. Track your macros or find a new meal to
                    crush.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="/bulkbuilder/tracker/dashboard.php" class="btn btn-custom-green btn-lg px-4 gap-3">Go to
                        Tracker</a>
                    <a href="/bulkbuilder/recipes/list.php" class="btn btn-outline-light btn-lg px-4">Browse Recipes</a>
                </div>
            <?php else: ?>
                <h1 class="fw-light text-light mb-3">Fuel Your <span class="text-success fw-bold">Gains</span></h1>
                <p class="lead text-muted mb-4">The ultimate resource for gym-focused bulk recipes. High protein, high
                    calorie, zero nonsense. Track your daily intake and reach your physique goals.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="/bulkbuilder/auth/register.php" class="btn btn-custom-green btn-lg px-4 gap-3">Join the
                        Bulk</a>
                    <a href="/bulkbuilder/auth/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Recipes Section -->
<div class="album py-5 bg-darker">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-2">
            <h3 class="text-light fw-bold"><i class="fas fa-fire text-warning me-2"></i>Fresh from the Kitchen</h3>
            <a href="/bulkbuilder/recipes/list.php" class="text-success text-decoration-none fw-bold">View All <i
                    class="fas fa-arrow-right"></i></a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php if ($feat_res->num_rows > 0): ?>
                <?php while ($row = $feat_res->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card shadow-sm h-100 bg-dark border-secondary recipe-card">
                            <?php if ($row['image']): ?>
                                <img src="/bulkbuilder/<?php echo htmlspecialchars($row['image']); ?>"
                                    class="bd-placeholder-img card-img-top" width="100%" height="225" style="object-fit: cover;"
                                    alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <?php else: ?>
                                <div class="bd-placeholder-img card-img-top bg-secondary d-flex align-items-center justify-content-center"
                                    style="height: 225px;">
                                    <i class="fas fa-dumbbell fa-3x text-dark"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-light fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-secondary"><i class="fas fa-bolt text-warning me-1"></i>
                                            <?php echo $row['calories']; ?> kcal</span>
                                        <span class="badge bg-secondary"><i class="fas fa-drumstick-bite text-info me-1"></i>
                                            <?php echo $row['protein']; ?>g Prot</span>
                                    </div>
                                    <a href="/bulkbuilder/recipes/view.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-outline-success w-100 stretched-link">View Recipe</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <p>No recipes added yet. Be the first to add one!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Features/Benefits -->
<section class="container py-5 border-top border-secondary">
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-dark text-success flex-shrink-0 me-3 rounded p-3 border border-secondary">
                <i class="fas fa-utensils fa-2x"></i>
            </div>
            <div>
                <h4 class="fw-bold text-light">Macro-Friendly</h4>
                <p class="text-muted">Every recipe comes with a detailed breakdown of calories, protein, carbs, and
                    fats. No guessing.</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-dark text-success flex-shrink-0 me-3 rounded p-3 border border-secondary">
                <i class="fas fa-chart-pie fa-2x"></i>
            </div>
            <div>
                <h4 class="fw-bold text-light">Track Progress</h4>
                <p class="text-muted">Log your daily meals with our built-in tracker. Visualize your gains and stay
                    consistent.</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-dark text-success flex-shrink-0 me-3 rounded p-3 border border-secondary">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <div>
                <h4 class="fw-bold text-light">Community Driven</h4>
                <p class="text-muted">Recipes submitted by gym-goers, for gym-goers. Verified by moderators for quality.
                </p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>