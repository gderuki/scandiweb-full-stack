<?php

namespace Repositories;

use PDO;
use Repositories\Interfaces\ICategoryRepository;
use Utils\Database;

class CategoryRepository implements ICategoryRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function get($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM Categories WHERE name = :name LIMIT 1");
        $stmt->bindParam(':name', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM Categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
