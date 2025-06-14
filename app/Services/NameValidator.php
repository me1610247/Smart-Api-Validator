<?php

namespace App\Services;

class NameValidator
{
    public static function validate(string $name): array
    {
        $isSafe = self::isSafeName($name);
        $isArabicOrEnglish = preg_match('/^[\p{Arabic}\p{Latin}\s\-]+$/u', $name);

        return [
            'is_safe' => $isSafe,
            'is_valid_format' => (bool)$isArabicOrEnglish,
        ];
    }

    private static function isSafeName(string $name): bool
    {
        if (strlen($name) < 2 || strlen($name) > 50) {
            return false;
        }
    
        if ($name !== htmlspecialchars($name, ENT_QUOTES, 'UTF-8')) {
            return false;
        }
    
        $dangerousPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/(javascript:|on\w+=|eval\(|alert\(|document\.|window\.)/i',
            '/<\?(php)?/i',
            '/(\%27|\')/i',
            '/(union|select|insert|delete|update|drop)/i'
        ];
    
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return false;
            }
        }
    
        return true;
    }
}
