<?php require APPROOT . '/Views/layouts/header.php'; ?>
<main class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card bg-dark text-light border-secondary shadow-lg p-4" style="max-width: 600px; width: 100%;">
        <div class="card-body">
            <h2 class="text-center mb-1 fw-bold">Join the <span class="text-success">Bulk</span></h2>
            <p class="text-center text-muted mb-4">Create your account and start your journey.</p>

            <form action="<?php echo URLROOT; ?>/users/register" method="POST">
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
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-user"></i></span>
                        <input type="text"
                            class="form-control bg-dark border-secondary text-light <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                            id="username" name="username" value="<?php echo $data['username']; ?>">
                        <div class="invalid-feedback">
                            <?php echo $data['username_err']; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-envelope"></i></span>
                        <input type="email"
                            class="form-control bg-dark border-secondary text-light <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                            id="email" name="email" value="<?php echo $data['email']; ?>">
                        <div class="invalid-feedback">
                            <?php echo $data['email_err']; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-lock"></i></span>
                        <input type="password"
                            class="form-control bg-dark border-secondary text-light <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                            id="password" name="password" value="<?php echo $data['password']; ?>">
                        <div class="invalid-feedback">
                            <?php echo $data['password_err']; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text bg-secondary border-secondary text-light"><i
                                class="fas fa-lock"></i></span>
                        <input type="password"
                            class="form-control bg-dark border-secondary text-light <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                            id="confirm_password" name="confirm_password"
                            value="<?php echo $data['confirm_password']; ?>">
                        <div class="invalid-feedback">
                            <?php echo $data['confirm_password_err']; ?>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-green btn-lg">Start My Journey</button>
                </div>

                <p class="text-center mt-3 text-muted">
                    Already have an account? <a href="<?php echo URLROOT; ?>/users/login"
                        class="text-success text-decoration-none">Login</a>
                </p>
            </form>
        </div>
    </div>
</main>
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
<?php require APPROOT . '/Views/layouts/footer.php'; ?>