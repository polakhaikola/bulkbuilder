<?php
include '../includes/db_connect.php';
include '../includes/header.php';

// Fetch approved recipes
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM recipes WHERE status = 'approved'";
if ($search) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
}
$stmt = $conn->prepare($sql);
if ($search) {
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h2 class="section-title mb-0">Browse Recipes</h2>
        </div>
        <div class="col-md-6">
            <form action="list.php" method="GET" class="d-flex">
                <input class="form-control me-2 bg-dark border-secondary text-light" type="search" placeholder="Search for gains..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="row g-4" id="recipe-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="recipe-card h-100">
                        <!-- Use default image if none provided or if file doesn't exist (basic check) -->
                        <?php 
                            $img = $row['image'] && file_exists("../" . $row['image']) ? "../" . $row['image'] : "https://placehold.co/600x400/1e1e1e/28a745?text=No+Image"; 
                             // If it's an external URL (from sample data potentially), use it directly if logic allows, 
                             // but for now we assume local paths from upload. 
                             // For sample data we might want to put placeholders.
                             if (strpos($row['image'], 'http') === 0) {
                                 $img = $row['image'];
                             }
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="recipe-img" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <div class="d-flex mb-3 flex-wrap">
                                <span class="badge-macro mb-1"><i class="fas fa-fire me-1"></i> <?php echo $row['calories']; ?> Cal</span>
                                <span class="badge-macro mb-1"><i class="fas fa-drumstick-bite me-1"></i> <?php echo $row['protein']; ?>g Pro</span>
                            </div>
                            <p class="text-muted small mb-4 flex-grow-1"><?php echo htmlspecialchars(substr($row['description'], 0, 80)) . '...'; ?></p>
                            <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success w-100 mt-auto">View Recipe</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted lead">No recipes found matching your search.</p>
                <a href="list.php" class="btn btn-custom-green btn-sm">View All</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
