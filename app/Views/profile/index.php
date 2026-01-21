<?php require APPROOT . '/Views/layouts/header.php'; ?>
<main class="container py-5">
    <div class="row justify-content-center">
        <!-- Profile Info -->
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow h-100">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-user-circle fa-6x text-muted"></i>
                    </div>
                    <h3 class="fw-bold text-light mb-1">
                        <?php echo htmlspecialchars($data['user']->username); ?>
                    </h3>
                    <span class="badge bg-success mb-3 text-uppercase">
                        <?php echo htmlspecialchars($data['user']->role); ?>
                    </span>
                    <p class="text-muted mb-4">
                        <?php echo htmlspecialchars($data['user']->email); ?>
                    </p>

                    
                    <div class="d-flex justify-content-around text-start border-top border-secondary pt-4">
                        <div>
                            <h5 class="fw-bold text-light mb-0">
                                <?php echo $data['stats']['recipes']; ?>
                            </h5>
                            <small class="text-muted">Recipes</small>
                        </div>
                        <div>
                            <h5 class="fw-bold text-light mb-0">
                                <?php echo $data['stats']['days_tracked']; ?>
                            </h5>
                            <small class="text-muted">Days Tracked</small>
                        </div>
                    </div>

                    <p class="mt-4 small text-muted">Member since
                        <?php echo date('M Y', strtotime($data['user']->created_at)); ?>
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
                    <?php if (!empty($data['msg'])): ?>
                        <div class="alert alert-success">
                            <?php echo $data['msg']; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $data['error']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4 pb-4 border-bottom border-secondary">
                        <h5 class="text-light mb-3">Primary Goal</h5>

                        <div class="p-3 bg-black rounded mb-3 border border-secondary d-flex align-items-center">
                            <?php
                            $goal_icon = '💪';
                            $goal_color = 'success';
                            $goal_label = 'Bulking';
                            switch ($data['user']->goal) {
                                case 'cut':
                                    $goal_icon = '🔥';
                                    $goal_color = 'warning';
                                    $goal_label = 'Cutting';
                                    break;
                                case 'recomp':
                                    $goal_icon = '⚖️';
                                    $goal_color = 'info';
                                    $goal_label = 'Recomposition';
                                    break;
                                case 'beginner':
                                    $goal_icon = '🏋️';
                                    $goal_color = 'primary';
                                    $goal_label = 'Beginner';
                                    break;
                            }
                            ?>
                            <span class="display-6 me-3">
                                <?php echo $goal_icon; ?>
                            </span>
                            <div>
                                <h4 class="fw-bold text-<?php echo $goal_color; ?> mb-0 text-uppercase">
                                    <?php echo $goal_label; ?>
                                </h4>
                                <small class="text-muted">Current Focus</small>
                            </div>
                        </div>

                        <form method="POST" action="<?php echo URLROOT; ?>/profiles">
                            <input type="hidden" name="update_primary_goal" value="1">
                            <label class="form-label text-muted small">Change Focus</label>
                            <div class="input-group">
                                <select class="form-select bg-dark text-light border-secondary" name="goal">
                                    <option value="bulk" <?php if ($data['user']->goal == 'bulk')
                                        echo 'selected'; ?>>💪
                                        Bulk</option>
                                    <option value="cut" <?php if ($data['user']->goal == 'cut')
                                        echo 'selected'; ?>>🔥 Cut
                                    </option>
                                    <option value="recomp" <?php if ($data['user']->goal == 'recomp')
                                        echo 'selected'; ?>>⚖️ Recomp</option>
                                    <option value="beginner" <?php if ($data['user']->goal == 'beginner')
                                        echo 'selected'; ?>>🏋️ Beginner</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="submit">Update</button>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">*Updating goal will reset
                                your nutritional targets below.</small>
                        </form>
                    </div>

                    <h5 class="text-light mb-3">Custom Nutritional Targets</h5>
                    <!-- Manual Override Form -->
                    <form method="POST" action="<?php echo URLROOT; ?>/profiles" class="mb-5">
                        <input type="hidden" name="update_goals" value="1">
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Daily Calories</label>
                                <input type="number" class="form-control bg-dark border-secondary text-light"
                                    name="target_calories" value="<?php echo $data['user']->target_calories; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Protein (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="target_protein" value="<?php echo $data['user']->target_protein; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Carbs (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="target_carbs" value="<?php echo $data['user']->target_carbs; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Fats (g)</label>
                                <input type="number" step="0.1" class="form-control bg-dark border-secondary text-light"
                                    name="target_fats" value="<?php echo $data['user']->target_fats; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-light w-100">Save Custom Targets</button>
                    </form>

                    <h5 class="text-light mb-3">Change Password</h5>
                    <form method="POST" action="<?php echo URLROOT; ?>/profiles">
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
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
