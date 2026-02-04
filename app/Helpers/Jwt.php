<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken{
    private static string $secret = 'YOUR_SECRET_KEY'; // put in .env

    public static function generate(array $payload, int $expiry = 3600): string
    {
        $payload['exp'] = time() + $expiry;
        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public static function verify(string $token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key(self::$secret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
