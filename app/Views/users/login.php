<?php require APPROOT . '/Views/layouts/header.php'; ?>
<main class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card bg-dark text-light border-secondary shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            <h2 class="text-center mb-4 fw-bold">Welcome <span class="text-success">Back</span></h2>

            <form action="<?php echo URLROOT; ?>/users/login" method="POST">
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
                  /*password*/
                <div class="mb-4">
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

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-green btn-lg">Login</button>
                </div>

                <p class="text-center mt-3 text-muted">
                    Don't have an account? <a href="<?php echo URLROOT; ?>/users/register"
                        class="text-success text-decoration-none">Register</a>
                </p>
            </form>
        </div>
    </div>
</main>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>