<?php
namespace App\Controllers;

use App\Helpers\JwtToken;
use App\Models\CustomerModel;
use App\Repositories\CustomerRepository;
use RuntimeException;
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
        ]);

        // Set cookie
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

        // echo json_encode(['message' => 'Login successful'])
           echo json_encode([
            'token' => $token
        ]);
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

    /** POST /customers/forgot-password */
    public function forgotPassword()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? '';

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['message' => 'Email is required']);
            return;
        }

        $customer = $this->repository->findByEmail($email);
        if (!$customer) {
            // Security: don't reveal if email exists, but for this task we'll be helpful
            http_response_code(404);
            echo json_encode(['message' => 'Customer not found']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expiresIn = $_ENV['RESET_TOKEN_EXPIRY'] ?? '1 hour';
        $expiry = date('Y-m-d H:i:s', strtotime('+' . $expiresIn));

        if ($this->repository->saveResetToken($email, $token, $expiry)) {
            // Mock email sending
            echo json_encode([
                'message' => 'Reset token generated (Mock Email Sent)',
                'debug_token' => $token // Included for testing purposes
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to generate reset token']);
        }
    }

    /** POST /customers/reset-password */
    public function resetPassword()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $token = $data['token'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        if (empty($token) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['message' => 'Token and new password are required']);
            return;
        }

        $customer = $this->repository->findByResetToken($token);
        if (!$customer) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid or expired token']);
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($this->repository->updatePassword($customer['id'], $hashedPassword)) {
            $this->repository->clearResetToken($customer['id']);
            echo json_encode(['message' => 'Password reset successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to reset password']);
        }
    }


    //  Logout 
    public function logout()
    {
        // Clear cookie
        setcookie("access_token", "", [
            "expires" => time() - 3600,
            "path" => "/",
            "secure" => true,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        echo json_encode(['message' => 'User logged out successfully']);
    }


    //   Get Total Customers
    public function countCustomers(){
        try {
            $total = $this->repository->countCustomers();
            echo json_encode([
                'status' => 'success',
                'total_customers' => $total
            ]);
        } catch (RuntimeException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
