<?php
namespace App\Controllers;

use App\Models\BookModel;
use App\Repositories\BookRepository;

class BookController
{
    private BookRepository $bookRepo;
    public function __construct()
    {
        // Initialize repository
        $this->bookRepo = new BookRepository();
    }

    /**
     * Create a new book
     *
     * @param array $data associative array with book data
     * @return bool
     */
    public function create(array $data): bool
    {
        // Create BookModel from input data
        $book = new BookModel(
            $data['title'],
            $data['author_id'],
            $data['category_id'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $data['published_date']
        );

        // Save book via repository
        return $this->bookRepo->save($book);
    }
}
