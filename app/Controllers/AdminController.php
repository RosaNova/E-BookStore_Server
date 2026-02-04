<?php
namespace App\Controllers;
use App\Models\AdminModel;
use App\Repositories\AdminRepository;

class AdminController{
    private AdminRepository $repository;
    public function __construct()
    {
        $this->repository = new AdminRepository();
    }
  
    /* 
     Register Admin
    */

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
        $admin = new AdminModel($data);

        if ($this->repository->create($admin)) {
            http_response_code(201);
            echo json_encode(['message' => 'Admin create Successfully !']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Admin create failed! ']);
        }
    }

    /*
     login  : 
    */
    public function login(){
        $data = json_decode(file_get_contents("php://input"), true);
           $AdminData = $this->repository->login($data['email']);
        if (!$AdminData){
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

            http_response_code(201);
            echo json_encode(['message' => 'Login Successfully !']);
    }
     
} 