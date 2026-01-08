<?php
class Recipes extends Controller
{
    public function __construct()
    {
        $this->recipeModel = $this->model('Recipe');
    }

    public function index()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $recipes = $this->recipeModel->getRecipes($search);

        $data = [
            'recipes' => $recipes,
            'search' => $search
        ];

        $this->view('recipes/index', $data);
    }

    public function view($id)
    {
        $recipe = $this->recipeModel->getRecipeById($id);

        if (!$recipe) {
            // Not found
            die('Recipe not found'); // Better 404 handling later
        }

        // Access Check (Pending/Rejected logic)
        if ($recipe->status !== 'approved') {
            $can_view = false;
            if (isset($_SESSION['user_id'])) {
                $role = $_SESSION['role'];
                if ($role === 'admin' || $role === 'mod' || $_SESSION['user_id'] == $recipe->author_id) {
                    $can_view = true;
                }
            }
            if (!$can_view) {
                die('Access Denied');
            }
        }

        $data = [
            'recipe' => $recipe
        ];

        $this->view('recipes/view', $data);
    }

    public function add()
    {
        // Check if logged in
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/users/login');
        }

        // Check role
        if (!($_SESSION['role'] === 'writer' || $_SESSION['role'] === 'mod' || $_SESSION['role'] === 'admin')) {
            header('location: ' . URLROOT . '/recipes');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST arrays
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'ingredients' => trim($_POST['ingredients']),
                'instructions' => trim($_POST['instructions']),
                'calories' => trim($_POST['calories']),
                'protein' => trim($_POST['protein']),
                'carbs' => trim($_POST['carbs']),
                'fats' => trim($_POST['fats']),
                'author_id' => $_SESSION['user_id'],
                'status' => ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'mod') ? 'approved' : 'pending',
                'image' => '',
                'title_err' => '',
                'description_err' => '',
                'ingredients_err' => '',
                'instructions_err' => '',
                'image_err' => ''
            ];

            // Validate
            if (empty($data['title']))
                $data['title_err'] = 'Please enter title';
            if (empty($data['description']))
                $data['description_err'] = 'Please enter description';
            if (empty($data['ingredients']))
                $data['ingredients_err'] = 'Please enter ingredients';
            if (empty($data['instructions']))
                $data['instructions_err'] = 'Please enter instructions';

            // Image Upload Logic
            if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $filename = $_FILES['recipe_image']['name'];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($file_ext, $allowed)) {
                    // Upload
                    // Ensure upload dir exists or use public/img/recipes
                    // The original used ../img/recipes. In new structure it is public/img/recipes
                    $new_filename = uniqid() . '.' . $file_ext;
                    $target_dir = APPROOT . '/../public/img/recipes/';

                    // Create dir if not exists
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }

                    if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $target_dir . $new_filename)) {
                        $data['image'] = 'img/recipes/' . $new_filename;
                    } else {
                        $data['image_err'] = 'Failed to upload image';
                    }
                } else {
                    $data['image_err'] = 'Invalid file type';
                }
            }

            // Make sure no errors
            if (empty($data['title_err']) && empty($data['description_err']) && empty($data['image_err'])) {
                if ($this->recipeModel->addRecipe($data)) {
                    // Success
                    // Redirect to list
                    header('location: ' . URLROOT . '/recipes');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('recipes/add', $data);
            }

        } else {
            $data = [
                'title' => '',
                'description' => '',
                'ingredients' => '',
                'instructions' => '',
                'calories' => '',
                'protein' => '',
                'carbs' => '',
                'fats' => '',
                'title_err' => '',
                'description_err' => '',
                'ingredients_err' => '',
                'instructions_err' => '',
                'image_err' => ''
            ];

            $this->view('recipes/add', $data);
        }
    }
}
