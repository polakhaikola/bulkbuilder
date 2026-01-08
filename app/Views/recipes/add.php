<?php require APPROOT . '/Views/layouts/header.php'; ?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="section-title mb-4">Add New Recipe</h2>

            <div class="card bg-dark border-secondary shadow-lg">
                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/recipes/add" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label text-light fw-bold">Recipe Title</label>
                            <input type="text"
                                class="form-control bg-black text-light border-secondary <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>"
                                name="title" placeholder="e.g. High Protein Chicken Alfredo"
                                value="<?php echo $data['title']; ?>" required>
                            <div class="invalid-feedback">
                                <?php echo $data['title_err']; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-light fw-bold">Short Description</label>
                            <textarea
                                class="form-control bg-black text-light border-secondary <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>"
                                name="description" rows="2"
                                placeholder="Briefly describe the dish (e.g. Creamy, savory, and perfect for bulking)"
                                required><?php echo $data['description']; ?></textarea>
                            <div class="invalid-feedback">
                                <?php echo $data['description_err']; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light fw-bold">Ingredients (Line separated)</label>
                                <textarea
                                    class="form-control bg-black text-light border-secondary <?php echo (!empty($data['ingredients_err'])) ? 'is-invalid' : ''; ?>"
                                    name="ingredients" rows="5"
                                    placeholder="200g Chicken Breast&#10;1 cup Pasta&#10;..."
                                    required><?php echo $data['ingredients']; ?></textarea>
                                <div class="invalid-feedback">
                                    <?php echo $data['ingredients_err']; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light fw-bold">Instructions (Step by step)</label>
                                <textarea
                                    class="form-control bg-black text-light border-secondary <?php echo (!empty($data['instructions_err'])) ? 'is-invalid' : ''; ?>"
                                    name="instructions" rows="5"
                                    placeholder="1. Boil pasta according to package instructions...&#10;2. Grill chicken..."
                                    required><?php echo $data['instructions']; ?></textarea>
                                <div class="invalid-feedback">
                                    <?php echo $data['instructions_err']; ?>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 text-success">Macros (per serving)</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Calories</label>
                                <input type="number" class="form-control bg-black text-light border-secondary"
                                    name="calories" placeholder="e.g. 550" value="<?php echo $data['calories']; ?>"
                                    required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Protein (g)</label>
                                <input type="number" step="0.1"
                                    class="form-control bg-black text-light border-secondary" name="protein"
                                    placeholder="e.g. 45" value="<?php echo $data['protein']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Carbs (g)</label>
                                <input type="number" step="0.1"
                                    class="form-control bg-black text-light border-secondary" name="carbs"
                                    placeholder="e.g. 60" value="<?php echo $data['carbs']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Fats (g)</label>
                                <input type="number" step="0.1"
                                    class="form-control bg-black text-light border-secondary" name="fats"
                                    placeholder="e.g. 15" value="<?php echo $data['fats']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-light fw-bold">Recipe Image</label>
                            <input type="file"
                                class="form-control bg-black text-light border-secondary <?php echo (!empty($data['image_err'])) ? 'is-invalid' : ''; ?>"
                                name="recipe_image" accept="image/*">
                            <div class="invalid-feedback">
                                <?php echo $data['image_err']; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-custom-green btn-lg w-100">Submit Recipe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>