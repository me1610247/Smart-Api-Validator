<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NameValidator;
use App\Services\NationalIdValidator;
use App\Http\Requests\SecurePasswordRequest;
use App\Services\EmailValidatorService;
use App\Models\EmailVerification;

class ValidationController extends Controller
{
    public function validateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $result = NameValidator::validate($request->name);

        if (!$result['is_safe'] || !$result['is_valid_format']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or unsafe name input.',
                'result' => $result
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Name is valid and safe.',
            'result' => $result
        ], 200);
    }

    public function validateNationalId(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string'
        ]);

        $result = NationalIdValidator::validate($request->national_id);

        if (!$result['is_valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Valid National ID',
            'data' => $result
        ]);
    }

    public function validatePassword(SecurePasswordRequest $request)
    {
        try {
            $validated = $request->secureValidated();

            return response()->json([
                'success' => true,
                'message' => 'Password is secure.',
                'data' => $validated
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password validation failed.',
                'errors' => $e->errors()
            ], 422);
        }
    }
    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $result = EmailValidatorService::validate($request->email);

        return response()->json([
            'input' => $request->email,
            'result' => $result
        ]);
    }
    public function confirmEmail(Request $request)
    {
        $token = $request->query('token');

        $record = EmailVerification::where('token', $token)->first();

        if (!$record) {
            return response()->json(['status' => false, 'message' => 'Invalid token.'], 404);
        }

        if ($record->verified) {
            return response()->json(['status' => true, 'message' => 'Email already verified.']);
        }

        $record->update(['verified' => true]);

        return response()->json(['status' => true, 'message' => 'Email successfully verified.']);
    }
}
