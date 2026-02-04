<?php
namespace App\Repositories;
use App\Config\DatabaseConnection;
use App\Models\AdminModel;
use PDO;

class AdminRepository
{
    private PDO $db;
    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
    }

    /*
     Get all Admin
    */

    /** Get all customers */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM admin ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    Register :
*/

    public function create(AdminModel $admin): bool
    {
        $sql = "INSERT INTO admin 
                (first_name, last_name, email, phone, address , password)
                VALUES (:first_name, :last_name, :email, :phone, :address , :password)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'first_name' => $admin->first_name,
            'last_name'  => $admin->last_name,
            'email'      => $admin->email,
            'phone'      => $admin->phone,
            'address'    => $admin->address,
            'password'  => $admin->password
        ]);
      }

    /*
       login :
    */
    public function login(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /*
     Find BY Eamil : 
    */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT email FROM admin WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }
}
