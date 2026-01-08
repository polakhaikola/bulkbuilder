<?php
class Profiles extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/users/login');
        }
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($user_id);
        $stats = $this->userModel->getStats($user_id);

        $data = [
            'user' => $user,
            'stats' => $stats,
            'msg' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Handle Password Change
            if (isset($_POST['change_password'])) {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
                $current_pass = $_POST['current_password'];
                $new_pass = $_POST['new_password'];
                $confirm_pass = $_POST['confirm_password'];

                if ($new_pass !== $confirm_pass) {
                    $data['error'] = 'New passwords do not match';
                } else {
                    if (password_verify($current_pass, $user->password)) {
                        $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                        if ($this->userModel->updatePassword($user_id, $new_hashed)) {
                            $data['msg'] = 'Password updated successfully';
                        } else {
                            $data['error'] = 'Something went wrong';
                        }
                    } else {
                        $data['error'] = 'Current password is incorrect';
                    }
                }
            }

            // Handle Primary Goal Change
            if (isset($_POST['update_primary_goal'])) {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
                $goal = $_POST['goal'];
                $macros = [
                    'target_calories' => 2500,
                    'target_protein' => 150,
                    'target_carbs' => 300,
                    'target_fats' => 70
                ];

                switch ($goal) {
                    case 'bulk':
                        $macros['target_calories'] = 3200;
                        $macros['target_protein'] = 220;
                        $macros['target_carbs'] = 400;
                        $macros['target_fats'] = 90;
                        break;
                    case 'cut':
                        $macros['target_calories'] = 2200;
                        $macros['target_protein'] = 180;
                        $macros['target_carbs'] = 200;
                        $macros['target_fats'] = 60;
                        break;
                    case 'recomp':
                        $macros['target_calories'] = 2800;
                        $macros['target_protein'] = 200;
                        $macros['target_carbs'] = 300;
                        $macros['target_fats'] = 80;
                        break;
                    case 'beginner':
                        $macros['target_calories'] = 2800;
                        $macros['target_protein'] = 180;
                        $macros['target_carbs'] = 350;
                        $macros['target_fats'] = 80;
                        break;
                }

                if ($this->userModel->updateGoal($user_id, $goal, $macros)) {
                    $data['msg'] = 'Primary goal updated! Targets reset.';
                    // Refresh user data
                    $data['user'] = $this->userModel->getUserById($user_id);
                } else {
                    $data['error'] = 'Error updating goal';
                }
            }

            // Handle Manual Goals Update
            if (isset($_POST['update_goals'])) {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
                $macros = [
                    'target_calories' => trim($_POST['target_calories']),
                    'target_protein' => trim($_POST['target_protein']),
                    'target_carbs' => trim($_POST['target_carbs']),
                    'target_fats' => trim($_POST['target_fats'])
                ];

                if ($this->userModel->updateMacros($user_id, $macros)) {
                    $data['msg'] = 'Nutritional goals updated!';
                    $data['user'] = $this->userModel->getUserById($user_id); // Refresh
                } else {
                    $data['error'] = 'Error updating goals';
                }
            }
        }

        $this->view('profile/index', $data);
    }
}
