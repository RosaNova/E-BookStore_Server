<?php 
namespace App\Repositories;
use App\Config\DatabaseConnection;
use App\Models\BookModel;
use PDO;
use PDOException;
use RuntimeException;

class BookRepository{
      private PDO $conn;

      public function __construct()
      {
        $this->conn =  DatabaseConnection::getInstance();
      }


    /*
        Create Book
    */
      public function saveBook(BookModel $book) :bool{
      try{
          $sql = "CALL createBook(:title,:author_id,:category_id,:price, :stock,:description, :published_date,:book_img)";
        
           $ps = $this->conn->prepare($sql); 

           $ps->bindValue(":title" , $book->getTitle() , PDO::PARAM_STR);
           $ps->bindValue(":author_id", $book->getAuthorId() , PDO::PARAM_INT);
           $ps->bindValue(":category_id", $book->getCategoryId());
           $ps->bindValue(":price",$book->getPrice());
           $ps->bindValue(":stock",$book->getStock());
           $ps->bindValue(":description",$book->getDescription());
           $ps->bindValue(":published_date",$book->getPublishedDate());
           $ps->bindValue(":book_img", $book->getBookImage());

           return $ps->execute();
      }catch(PDOException $err){
           throw new RuntimeException(
                'Failed to save book: ' . $err->getMessage()
            );
         }    
      }
}