<?php
namespace App\Middlewares;

class RequestMiddleware
{
    private string $expectedMethod;

    public function __construct(string $expectedMethod)
    {
        $this->expectedMethod = strtoupper($expectedMethod);
    }

    /**
     * Check HTTP method
     */
    public function checkMethod(): bool
    {
        $currentMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($currentMethod !== $this->expectedMethod) {
            http_response_code(405); // Method Not Allowed
            echo json_encode([
                'error' => "Expected {$this->expectedMethod} request, got {$currentMethod}"
            ]);
            return false;
        }
        return true;
    }

    /**
     * Validate request data (for POST/PUT)
     */
    public function validate(array $data, array $requiredFields = []): array
    {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "$field is required";
            }
        }
        return $errors;
    }

    /**
     * Run middleware: check method + validate
     */
    public function handle(array $data = [], array $requiredFields = []): bool
    {
        if (!$this->checkMethod()) {
            return false;
        }

        $errors = $this->validate($data, $requiredFields);
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return false;
        }

        return true;
    }
}
