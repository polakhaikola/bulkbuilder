<?php require APPROOT . '/Views/layouts/header.php'; ?>
<main class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h2 class="section-title mb-0">Browse Recipes</h2>
        </div>
        <div class="col-md-6">
            <form action="<?php echo URLROOT; ?>/recipes" method="GET" class="d-flex">
                <input class="form-control me-2 bg-dark border-secondary text-light" type="search"
                    placeholder="Search for gains..." name="search"
                    value="<?php echo htmlspecialchars($data['search']); ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="row g-4" id="recipe-grid">
        <?php if (!empty($data['recipes'])): ?>
            <?php foreach ($data['recipes'] as $recipe): ?>
                <div class="col-md-4">
                    <div class="recipe-card h-100">
                        <!-- Use default image if none provided or if file doesn't exist (basic check inside View logic or Helper) -->
                        <?php
                        $img = $recipe->image && file_exists(dirname(APPROOT) . "/public/" . $recipe->image) ? URLROOT . "/" . $recipe->image : "https://placehold.co/600x400/1e1e1e/28a745?text=No+Image";
                        if (strpos($recipe->image, 'http') === 0) {
                            $img = $recipe->image;
                        }
                        ?>
                        <img src="<?php echo $img; ?>" class="recipe-img" alt="<?php echo htmlspecialchars($recipe->title); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate">
                                <?php echo htmlspecialchars($recipe->title); ?>
                            </h5>
                            <div class="d-flex mb-3 flex-wrap">
                                <span class="badge-macro mb-1"><i class="fas fa-fire me-1"></i>
                                    <?php echo $recipe->calories; ?> Cal
                                </span>
                                <span class="badge-macro mb-1"><i class="fas fa-drumstick-bite me-1"></i>
                                    <?php echo $recipe->protein; ?>g Pro
                                </span>
                            </div>
                            <p class="text-muted small mb-4 flex-grow-1">
                                <?php echo htmlspecialchars(substr($recipe->description, 0, 80)) . '...'; ?>
                            </p>
                            <a href="<?php echo URLROOT; ?>/recipes/view/<?php echo $recipe->id; ?>"
                                class="btn btn-sm btn-outline-success w-100 mt-auto">View Recipe</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted lead">No recipes found matching your search.</p>
                <a href="<?php echo URLROOT; ?>/recipes" class="btn btn-custom-green btn-sm">View All</a>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>