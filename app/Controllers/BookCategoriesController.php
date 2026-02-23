<?php
namespace App\Controllers;
use App\Repositories\BookCategoriesRepository;

class BookCategoriesController{
      private BookCategoriesRepository $repo;
      public function __construct() {
        $this->repo = new BookCategoriesRepository();
      }

      public function index(){
        $data = $this->repo->getAllCategory();
        http_response_code(200);
        echo json_encode($data);
      }
}