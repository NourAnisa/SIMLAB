<?php

namespace App\Http\Middleware;

use App\ProfileValidationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompletionMiddleware
{
    public function __construct(
        private ProfileValidationService $validationService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika tidak ada user atau bukan mahasiswa, lanjutkan
        if (!$user || !$user->isMahasiswa()) {
            return $next($request);
        }

        // Cek apakah profil lengkap
        $check = $this->validationService->canAccessFeatures($user);

        if (!$check['can_access']) {
            // Jika akses dari JSON/API, return error
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Profile belum lengkap',
                    'missing_fields' => $check['missing_fields'] ?? [],
                ], 403);
            }

            // Jika akses dari web, redirect ke profile dengan pesan warning
            return redirect()
                ->route('profile.edit')
                ->with('warning', $check['message'])
                ->with('incomplete_profile', true);
        }

        return $next($request);
    }
}
