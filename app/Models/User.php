<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Register User
    public function register($data)
    {
        $this->db->query('INSERT INTO users (username, email, password, role, goal, target_calories, target_protein, target_carbs, target_fats) VALUES (:username, :email, :password, :role, :goal, :target_calories, :target_protein, :target_carbs, :target_fats)');
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':goal', $data['goal']);
        $this->db->bind(':target_calories', $data['target_calories']);
        $this->db->bind(':target_protein', $data['target_protein']);
        $this->db->bind(':target_carbs', $data['target_carbs']);
        $this->db->bind(':target_fats', $data['target_fats']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        $hashed_password = $row->password;
        if (password_verify($password, $hashed_password)) {
            return $row;
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Find user by username
    public function findUserByUsername($username)
    {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get User by ID
    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    // Update Password
    public function updatePassword($id, $password)
    {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':password', $password);
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update Goal
    public function updateGoal($id, $goal, $macros)
    {
        $this->db->query('UPDATE users SET goal = :goal, target_calories = :target_calories, target_protein = :target_protein, target_carbs = :target_carbs, target_fats = :target_fats WHERE id = :id');
        $this->db->bind(':goal', $goal);
        $this->db->bind(':target_calories', $macros['target_calories']);
        $this->db->bind(':target_protein', $macros['target_protein']);
        $this->db->bind(':target_carbs', $macros['target_carbs']);
        $this->db->bind(':target_fats', $macros['target_fats']);
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update Macros
    public function updateMacros($id, $macros)
    {
        $this->db->query('UPDATE users SET target_calories = :target_calories, target_protein = :target_protein, target_carbs = :target_carbs, target_fats = :target_fats WHERE id = :id');
        $this->db->bind(':target_calories', $macros['target_calories']);
        $this->db->bind(':target_protein', $macros['target_protein']);
        $this->db->bind(':target_carbs', $macros['target_carbs']);
        $this->db->bind(':target_fats', $macros['target_fats']);
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get Stats
    public function getStats($id)
    {
        $stats = [];
        // Recipe count
        $this->db->query('SELECT COUNT(*) as count FROM recipes WHERE author_id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        $stats['recipes'] = $row->count;

        // Days tracked 
        try {
            $this->db->query('SELECT COUNT(DISTINCT log_date) as count FROM daily_logs WHERE user_id = :id');
            $this->db->bind(':id', $id);
            $row = $this->db->single();
            $stats['days_tracked'] = $row->count;
        } catch (PDOException $e) {
            $stats['days_tracked'] = 0;
        }

        return $stats;
    }
}
