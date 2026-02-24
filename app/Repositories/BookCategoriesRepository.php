<?php
namespace App\Repositories;
use PDO;
use App\Config\DatabaseConnection;
class BookCategoriesRepository{
        private PDO $db;

        public function __construct() {
             $this->db = DatabaseConnection::getInstance();
        }

        // get All Book Category
        public function getAllCategory(){
           $sql = "SELECT * FROM categories ORDER BY id ASC";
           $stmt = $this->db->prepare($sql);
           $stmt->execute();
           return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}