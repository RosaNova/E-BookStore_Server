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
    
    public static function generate(array $payload, int $expiresIn = 3600): string
    {
        $payload['exp'] = time() + $expiresIn;
        return JWT::encode(
            $payload,                // Payload
            self::secret(),          // Signature key
            'HS256'                  // Algorithm (Header)
        );
    }

    /**
     * Verify signature + expiration
     */
    public static function verify(string $token): ?array
    {
        try {
            return (array) JWT::decode(
                $token,
                new Key(self::secret(), 'HS256')
            );
        } catch (\Throwable $e) {
            return null; // invalid / expired token
        }
    }
}
