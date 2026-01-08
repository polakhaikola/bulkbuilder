<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container text-center py-5">
    <div class="p-5 mb-4 bg-dark rounded-3 text-light">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">
                <?php echo $data['title']; ?>
            </h1>
            <p class="col-md-8 fs-4 mx-auto">
                <?php echo $data['description']; ?>
            </p>
            <a href="<?php echo URLROOT; ?>/recipes" class="btn btn-primary btn-lg" type="button">Browse Recipes</a>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>