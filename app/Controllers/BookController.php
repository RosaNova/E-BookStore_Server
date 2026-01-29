<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Repositories\BookRepository;

class BookController
{
    private BookRepository $repo;

    public function __construct()
    {
        $this->repo = new BookRepository();
    }

    /*
       *  @param array $data associative array with book data
       *  @return bool 
    */

    //   Save book
    public function save(): void
    {
      $data = json_decode(file_get_contents('php://input'), true);
        $book = new BookModel(
            $data['title'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['author_id'],
            $data['category_id'],
            $data['published_date'],
            $data['book_img']
        );
        if ($this->repo->saveBook($book)) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Book created successfully ✅',
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create book ❌'
            ]);
        }
    }
}
