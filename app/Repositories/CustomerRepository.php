<?php

namespace App\Repositories;

use App\Config\DatabaseConnection;
use PDO;
use App\Models\CustomerModel;

class CustomerRepository{
    private PDO $db;
    public function __construct(){
        $this->db = DatabaseConnection::getInstance();
    }

    /** Get all customers */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM customers ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Find customer by ID */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Create customer */
    public function create(CustomerModel $customer): bool
    {
        $sql = "INSERT INTO customers 
                (first_name, last_name, email, phone, address , password)
                VALUES (:first_name, :last_name, :email, :phone, :address , :password)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'first_name' => $customer->first_name,
            'last_name'  => $customer->last_name,
            'email'      => $customer->email,
            'phone'      => $customer->phone,
            'address'    => $customer->address,
            'password'   => $customer->password
        ]);
    }

    /** Update customer */
    public function update(int $id, CustomerModel $customer): bool
    {
        $sql = "UPDATE customers SET
                first_name = :first_name,
                last_name  = :last_name,
                email      = :email,
                phone      = :phone,
                address    = :address,
                password   = :password
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id'         => $id,
            'first_name' => $customer->first_name,
            'last_name'  => $customer->last_name,
            'email'      => $customer->email,
            'phone'      => $customer->phone,
            'address'    => $customer->address,
            'password'   => $customer->password
        ]);
    }

    /*
        Find by Email :
    */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Delete customer */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /** Save reset token */
    public function saveResetToken(string $email, string $token, string $expiry): bool
    {
        $sql = "UPDATE customers SET reset_token = :token, reset_token_expires = :expiry WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'token'  => $token,
            'expiry' => $expiry,
            'email'  => $email
        ]);
    } 

    //  Logout
    public function logout(int $id): bool
    {
        $sql = "UPDATE customers SET reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }


    /** Find customer by reset token */
    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM customers WHERE reset_token = :token AND reset_token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Update password */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $sql = "UPDATE customers SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id'       => $id
        ]);
    }

    /** Clear reset token */
    public function clearResetToken(int $id): bool
    {
        $sql = "UPDATE customers SET reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
