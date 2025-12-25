<?php
include '../includes/db_connect.php';
session_start();

// Access Check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'writer' && $_SESSION['role'] !== 'mod' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $calories = intval($_POST['calories']);
    $protein = floatval($_POST['protein']);
    $carbs = floatval($_POST['carbs']);
    $fats = floatval($_POST['fats']);
    $author_id = $_SESSION['user_id'];
    
    // Status logic: Admin/Mod auto-approved, Writer pending
    $status = ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'mod') ? 'approved' : 'pending';

    // Image Upload
    $target_dir = __DIR__ . "/../img/recipes/";
    $image_path = "";
    
    if (isset($_FILES["recipe_image"]) && $_FILES["recipe_image"]["error"] == 0) {
        $file_ext = strtolower(pathinfo($_FILES["recipe_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid() . "." . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
                $image_path = "img/recipes/" . $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, WEBP allowed.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO recipes (title, description, ingredients, instructions, calories, protein, carbs, fats, image, author_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssidddsis", $title, $desc, $ingredients, $instructions, $calories, $protein, $carbs, $fats, $image_path, $author_id, $status);
        
        if ($stmt->execute()) {
            $success = "Recipe submitted successfully! Status: " . ucfirst($status);
        } else {
            $error = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!-- Header include handles session start, so we used a trick above to check session before include, 
     but now we need to be careful not to double session_start. 
     The header.php logic is: if (session_status() === PHP_SESSION_NONE) session_start();
     So it's safe. -->
<?php include '../includes/header.php'; ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="section-title mb-4">Add New Recipe</h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?> 
                    <a href="list.php" class="alert-link">View Recipes</a>
                </div>
            <?php endif; ?>

            <div class="card bg-dark border-secondary shadow-lg">
                <div class="card-body p-4">
                    <form action="add.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label text-light fw-bold">Recipe Title</label>
                            <input type="text" class="form-control bg-black text-light border-secondary" name="title" placeholder="e.g. High Protein Chicken Alfredo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-light fw-bold">Short Description</label>
                            <textarea class="form-control bg-black text-light border-secondary" name="description" rows="2" placeholder="Briefly describe the dish (e.g. Creamy, savory, and perfect for bulking)" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light fw-bold">Ingredients (Line separated)</label>
                                <textarea class="form-control bg-black text-light border-secondary" name="ingredients" rows="5" placeholder="200g Chicken Breast&#10;1 cup Pasta&#10;..." required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light fw-bold">Instructions (Step by step)</label>
                                <textarea class="form-control bg-black text-light border-secondary" name="instructions" rows="5" placeholder="1. Boil pasta according to package instructions...&#10;2. Grill chicken..." required></textarea>
                            </div>
                        </div>

                        <h5 class="mt-3 mb-3 text-success">Macros (per serving)</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Calories</label>
                                <input type="number" class="form-control bg-black text-light border-secondary" name="calories" placeholder="e.g. 550" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Protein (g)</label>
                                <input type="number" step="0.1" class="form-control bg-black text-light border-secondary" name="protein" placeholder="e.g. 45" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Carbs (g)</label>
                                <input type="number" step="0.1" class="form-control bg-black text-light border-secondary" name="carbs" placeholder="e.g. 60" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-light fw-bold">Fats (g)</label>
                                <input type="number" step="0.1" class="form-control bg-black text-light border-secondary" name="fats" placeholder="e.g. 15" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-light fw-bold">Recipe Image</label>
                            <input type="file" class="form-control bg-black text-light border-secondary" name="recipe_image" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-custom-green btn-lg w-100">Submit Recipe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
