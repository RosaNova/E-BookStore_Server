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
     * CREATE BOOK
     */
    public function saveBook(BookModel $book): bool
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
                    :published_date,
                    :book_img
                )
            ";

            $ps = $this->conn->prepare($sql);

            $ps->bindValue(':title', $book->getTitle(), PDO::PARAM_STR);
            $ps->bindValue(':author_id', $book->getAuthorId(), PDO::PARAM_INT);
            $ps->bindValue(':category_id', $book->getCategoryId(), PDO::PARAM_INT);
            $ps->bindValue(':price', $book->getPrice());
            $ps->bindValue(':stock', $book->getStock(), PDO::PARAM_INT);
            $ps->bindValue(':description', $book->getDescription(), PDO::PARAM_STR);
            $ps->bindValue(':published_date', $book->getPublishedDate());
            $ps->bindValue(':book_img', $book->getBookImage(), PDO::PARAM_STR);

            return $ps->execute();

        } catch (PDOException $e) {
            throw new RuntimeException('Failed to save book: ' . $e->getMessage());
        }
    }

    /**
     * GET ALL BOOKS
     */
    public function getAll(): array
    {
        try {
            $sql = "SELECT * FROM books ORDER BY id DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch books: ' . $e->getMessage());
        }
    }

    /**
     * GET BOOK BY ID
     */
    public function getById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM books WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            return $book ?: null;

        } catch (PDOException $e) {
            throw new RuntimeException('Failed to fetch book: ' . $e->getMessage());
        }
    }

    /**
     * UPDATE BOOK
     */
    public function updateBook(int $id, BookModel $book): bool
    {
        try {
            $sql = "
                UPDATE books SET
                    title = :title,
                    author_id = :author_id,
                    category_id = :category_id,
                    price = :price,
                    stock = :stock,
                    description = :description,
                    published_date = :published_date,
                    book_img = :book_img
                WHERE id = :id
            ";

            $ps = $this->conn->prepare($sql);

            $ps->bindValue(':id', $id, PDO::PARAM_INT);
            $ps->bindValue(':title', $book->getTitle());
            $ps->bindValue(':author_id', $book->getAuthorId(), PDO::PARAM_INT);
            $ps->bindValue(':category_id', $book->getCategoryId(), PDO::PARAM_INT);
            $ps->bindValue(':price', $book->getPrice());
            $ps->bindValue(':stock', $book->getStock(), PDO::PARAM_INT);
            $ps->bindValue(':description', $book->getDescription());
            $ps->bindValue(':published_date', $book->getPublishedDate());
            $ps->bindValue(':book_img', $book->getBookImage());

            return $ps->execute();

        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update book: ' . $e->getMessage());
        }
    }

    /**
     * DELETE BOOK
     */
    public function deleteBook(int $id): bool
    {
        try {
            $sql = "DELETE FROM books WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            throw new RuntimeException('Failed to delete book: ' . $e->getMessage());
        }
    }
}
