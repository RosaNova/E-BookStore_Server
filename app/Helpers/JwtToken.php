<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken
{
    private static function secret(): string
    {
        return $_ENV['JWT_SECRET'];
    }

    /**
     * HEADER  : { "alg": "HS256", "typ": "JWT" }
     * PAYLOAD : user data + exp
     * SIGN    : created using JWT_SECRET
     */
    
    public static function generate(array $payload): string
    {
        return JWT::encode(
            $payload,                // Payload
            self::secret(),          // Signature key
            'HS256'                  // Algorithm (Header)
        );
    }

    public static function validate(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::secret(), 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
