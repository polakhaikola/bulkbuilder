<?php
class Recipe
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getRecipes($search = '')
    {
        $sql = "SELECT * FROM recipes WHERE status = 'approved'";
        if ($search) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
        }

        $this->db->query($sql);

        if ($search) {
            $this->db->bind(':search', '%' . $search . '%');
        }

        return $this->db->resultSet();
    }

    public function getRecipeById($id)
    {
        $this->db->query("SELECT r.*, u.username FROM recipes r JOIN users u ON r.author_id = u.id WHERE r.id = :id");
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    public function addRecipe($data)
    {
        $this->db->query("INSERT INTO recipes (title, description, ingredients, instructions, calories, protein, carbs, fats, image, author_id, status) VALUES (:title, :description, :ingredients, :instructions, :calories, :protein, :carbs, :fats, :image, :author_id, :status)");

        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':ingredients', $data['ingredients']);
        $this->db->bind(':instructions', $data['instructions']);
        $this->db->bind(':calories', $data['calories']);
        $this->db->bind(':protein', $data['protein']);
        $this->db->bind(':carbs', $data['carbs']);
        $this->db->bind(':fats', $data['fats']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':author_id', $data['author_id']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
