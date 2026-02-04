<?php
namespace App\Controllers;

use App\Helpers\JwtToken;
use App\Models\CustomerModel;
use App\Repositories\CustomerRepository;

class CustomerController{
    private CustomerRepository $repository;

    public function __construct()
    {
        $this->repository = new CustomerRepository();
    }

    /** GET /customers */
    public function index()
    {
        echo json_encode([
            "status" => "success",
            "data" => $this->repository->all()
            ]);
    }

    /** GET /customers/{id} */
    public function show(int $id)
    {
        $customer = $this->repository->find($id);

        if (!$customer) {
            http_response_code(404);
            echo json_encode(['message' => 'Customer not found']);
            return;
        }

        echo json_encode([
            "status" => "success",
            "data" => $customer
            ]);
    }

    /** POST /customers */
    public function store()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // check if email exists
        if ($this->repository->findByEmail($data['email'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Email already exists']);
            return;
        }

        // hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $customer = new CustomerModel($data);

        if ($this->repository->create($customer)) {
            http_response_code(201);
            echo json_encode(['message' => 'Customer created']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Create failed']);
        }
    }

       /** LOGIN */
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $customerData = $this->repository->findByEmail($data['email']);
        if (!$customerData) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            return;
        }

        // verify password
        if (!password_verify($data['password'], $customerData['password'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            return;
        }

        // generate JWT
        $token = JwtToken::generate([
            'id' => $customerData['id'],
            'email' => $customerData['email'],
            'role' => $customerData['role'] ?? 'customer'
        ]);

        echo json_encode(['token' => $token]);
    }

    /** PUT /customers/{id} */
    public function update(int $id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $customer = new CustomerModel($data);

        if ($this->repository->update($id, $customer)) {
            echo json_encode(['message' => 'Customer updated']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Update failed']);
        }
    }

    /** DELETE /customers/{id} */
    public function delete(int $id)
    {
        if ($this->repository->delete($id)) {
            echo json_encode(['message' => 'Customer deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Delete failed']);
        }
    }
}
