<?php
namespace App\Repositories;
use PDO;
use App\Config\Database;
class BookCategoriesRepository{
        private PDO $db;

        public function __construct() {
             $this->db = Database::getInstance()->getConnection();
        }

        // get All Book Category
        public function getAllCategory(){
           $sql = "SELECT * FROM categories ORDER BY id ASC";
           $stmt = $this->db->prepare($sql);
           $stmt->execute();
           return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}