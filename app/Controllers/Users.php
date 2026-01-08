<?php
class Users extends Controller
{
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form

            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Init data
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'goal' => trim($_POST['goal']), // bulk, cut, recomp, beginner
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                // Goals are set in controller logic, pass to model
                'role' => 'user',
                'target_calories' => 2500,
                'target_protein' => 150,
                'target_carbs' => 300,
                'target_fats' => 70
            ];

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                // Check email
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }

            // Validate Name
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter name';
            } else {
                if ($this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username is already taken';
                }
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Goals Logic (Copied from legacy)
            switch ($data['goal']) {
                case 'bulk':
                    $data['target_calories'] = 3200;
                    $data['target_protein'] = 220;
                    $data['target_carbs'] = 400;
                    $data['target_fats'] = 90;
                    break;
                case 'cut':
                    $data['target_calories'] = 2200;
                    $data['target_protein'] = 180;
                    $data['target_carbs'] = 200;
                    $data['target_fats'] = 60;
                    break;
                case 'recomp':
                    $data['target_calories'] = 2800;
                    $data['target_protein'] = 200;
                    $data['target_carbs'] = 300;
                    $data['target_fats'] = 80;
                    break;
                case 'beginner':
                    $data['target_calories'] = 2800;
                    $data['target_protein'] = 180;
                    $data['target_carbs'] = 350;
                    $data['target_fats'] = 80;
                    break;
            }

            // Make sure errors are empty
            if (empty($data['email_err']) && empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Validated

                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register User
                if ($this->userModel->register($data)) {
                    header('location: ' . URLROOT . '/users/login');
                    // Flash message here if helper existed
                } else {
                    die('Something went wrong');
                }

            } else {
                // Load view with errors
                $this->view('users/register', $data);
            }

        } else {
            // Init data
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'goal' => 'bulk',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load view
            $this->view('users/register', $data);
        }
    }

    public function login()
    {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Init data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',
            ];

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/email
            if ($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                // User not found
                $data['email_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';

                    $this->view('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }


        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => '',
            ];

            // Load view
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->username;
        $_SESSION['role'] = $user->role;
        header('location: ' . URLROOT . '/recipes');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['role']);
        session_destroy();
        header('location: ' . URLROOT . '/users/login');
    }
}
