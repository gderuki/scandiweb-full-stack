<?php

namespace Repositories;


use Utils\Database;
use PDO;
use Repositories\Interfaces\ICategoryRepository;

class CategoryRepository implements ICategoryRepository
{
    public function get($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Categories WHERE name = :name LIMIT 1");
        $stmt->bindParam(':name', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM Categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}