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
                (first_name, last_name, email, phone, address)
                VALUES (:first_name, :last_name, :email, :phone, :address)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'first_name' => $customer->first_name,
            'last_name'  => $customer->last_name,
            'email'      => $customer->email,
            'phone'      => $customer->phone,
            'address'    => $customer->address
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
                address    = :address
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id'         => $id,
            'first_name' => $customer->first_name,
            'last_name'  => $customer->last_name,
            'email'      => $customer->email,
            'phone'      => $customer->phone,
            'address'    => $customer->address
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
}
