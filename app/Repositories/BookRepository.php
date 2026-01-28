<?php
namespace App\Repositories;

use App\Config\DatabaseConnection;
use App\Models\BookModel;
use PDO;
use PDOException;
use RuntimeException;

class BookRepository
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance();
    }

    /**
     * Save a BookModel into the database using stored procedure
     */
    public function save(BookModel $book): bool
    {
        try {
            $sql = "
                CALL createBook(
                    :title,
                    :author_id,
                    :category_id,
                    :price,
                    :stock,
                    :description,
                    :published_date
                )
            ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(':title', $book->getTitle());
            $stmt->bindValue(':author_id', $book->getAuthorId(), PDO::PARAM_INT);
            $stmt->bindValue(':category_id', $book->getCategoryId(), PDO::PARAM_INT);
            $stmt->bindValue(':price', $book->getPrice());
            $stmt->bindValue(':stock', $book->getStock(), PDO::PARAM_INT);
            $stmt->bindValue(':description', $book->getDescription());
            $stmt->bindValue(':published_date', $book->getPublishedDate());

            return $stmt->execute();

        } catch (PDOException $e) {
            throw new RuntimeException(
                'Failed to save book: ' . $e->getMessage()
            );
        }
    }
}
