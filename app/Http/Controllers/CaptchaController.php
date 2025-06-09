<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function reload(): JsonResponse
    {
        try {
            $builder = new CaptchaBuilder;
            $builder->build();
            Session::put('captcha', $builder->getPhrase()); // Store phrase for validation
            return response()->json([
                'captcha' => $builder->inline() // Base64-encoded image
            ]);
        } catch (\Exception $e) {
            \Log::error('CAPTCHA generation error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to generate CAPTCHA'], 500);
        }
    }
}