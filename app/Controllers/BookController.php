<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Repositories\BookRepository;
use RuntimeException;

class BookController
{
    private BookRepository $repo;

    public function __construct()
    {
        $this->repo = new BookRepository();
    }

    /**
     * CREATE BOOK (POST)
     */
    public function save(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $book = new BookModel(
                $data['title'],
                $data['author_id'],
                $data['category_id'],
                $data['price'],
                $data['stock'],
                $data['description'],
                $data['published_date'],
                $data['book_img'] ?? null
            );

            $this->repo->saveBook($book);

            http_response_code(201); // Created
            echo json_encode([
                'status' => 'success',
                'message' => 'Book created successfully'
            ]);

        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET ALL BOOKS (GET)
     */
    public function index(): void
    {
        try {
            $books = $this->repo->getAll();
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $books
            ]);
        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET BOOK BY ID (GET /?id=1)
     */
    public function show(int $id): void
    {
        try {
            $book = $this->repo->getById($id);

            if (!$book) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Book not found'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $book
            ]);

        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * UPDATE BOOK (PUT)
     */
    public function update(int $id): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $book = new BookModel(
                $data['title'],
                $data['author_id'],
                $data['category_id'],
                $data['price'],
                $data['stock'],
                $data['description'],
                $data['published_date'],
                $data['book_img'] ?? null
            );
            $this->repo->updateBook($id, $book);
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Book updated successfully'
            ]);

        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * DELETE BOOK (DELETE)
     */
    public function delete(int $id): void
    {
        try {
            $this->repo->deleteBook($id);

            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Book deleted successfully'
            ]);

        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
