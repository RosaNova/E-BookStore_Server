<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Repositories\AdminRepository;
use App\Helpers\JwtToken;
use Exception;

class AdminController
{
    private AdminRepository $repository;
    public function __construct()
    {
        $this->repository = new AdminRepository();
    }

    /* 
     Register Admin
    */
public function store()
{
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        // 1. Check JSON
        if (!$data) {
            throw new Exception("Invalid JSON body");
        }

        // 2. Validate fields
        if (empty($data['email'])) {
            throw new Exception("Email is required");
        }

        if (empty($data['password'])) {
            throw new Exception("Password is required");
        }

        // 3. Check email exists
        if ($this->repository->findByEmail($data['email'])) {
            throw new Exception("Email already exists");
        }
        
        // 4. Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $admin = new AdminModel($data);

        // 5. Save
        if (!$this->repository->create($admin)) {
            throw new Exception("Admin create failed");
        }

        // Success response
        http_response_code(201);
        echo json_encode([
            "message" => "Admin created successfully"
        ]);

    } catch (Exception $e) {
        // All errors go here as JSON
        http_response_code(400);
        echo json_encode([
            "error" => $e->getMessage()
        ]);
    }
}

    /*
     login  : 
    */
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $AdminData = $this->repository->findByEmail($data['email']);
        if (!$AdminData) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            return;
        }
        // verify password
        if (!password_verify($data['password'], $AdminData['password'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
            return;
        }
        // check role       
         if ($AdminData['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            return;
        }

        // 3️⃣ Generate JWT (Payload = identity)
        $token = JwtToken::generate([
            'id'    => $AdminData['id'],
            'email' => $AdminData['email'],
        ]);

        // // Set cookie
        // setcookie(
        //     "access_token",
        //     $token,
        //     [
        //         "expires"  => time() + 3600, // 1 hour
        //         "path"     => "/",
        //         "secure"   => true,         // HTTPS only
        //         "httponly" => true,         // JS cannot access
        //         "samesite" => "Strict"      // CSRF protection
        //     ]
        // );

        // 4️⃣ Response
        http_response_code(200);
        echo json_encode([
            // 'message' => 'Login successful',
            'token' => $token,
            'admin' => [
                'id' => $AdminData['id'],
                'email' => $AdminData['email'],
                'first_name' => $AdminData['first_name'],
                'last_name' => $AdminData['last_name'],
                'role' => $AdminData['role'],   
            ]
        ]);
    }
}
