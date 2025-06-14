<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Models\EmailVerification;
use Illuminate\Support\Str;
class EmailValidatorService
{
    public static function validate(string $email): array
    {
        $validator = Validator::make(['email' => $email], ['email' => 'required|email']);
    
        if ($validator->fails()) {
            return ['status' => false, 'message' => 'Invalid email format.'];
        }
    
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, 'MX')) {
            return ['status' => false, 'message' => 'Invalid email domain (no MX record).'];
        }
    
        $token = Str::uuid();
    
        EmailVerification::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'verified' => false]
        );
    
        try {
            Mail::to($email)->send(new VerifyEmail($email, $token));
    
            return [
                'status' => true,
                'message' => 'Valid email. Verification link sent.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Valid email but failed to send verification.',
                'error' => $e->getMessage()
            ];
        }
    }

    protected static function checkDomain(string $domain): bool
    {
        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            return false;
        }

        if (self::isDisposableEmail($domain)) {
            return false;
        }

        return true;
    }

    protected static function isDisposableEmail(string $domain): bool
    {
        $disposableDomains = [
            'tempmail.com', 'mailinator.com', '10minutemail.com',
            'guerrillamail.com', 'yopmail.com', 'temp-mail.org'
        ];

        return in_array(strtolower($domain), $disposableDomains);
    }
}