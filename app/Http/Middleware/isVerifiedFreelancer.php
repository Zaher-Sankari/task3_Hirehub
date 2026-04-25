<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isVerifiedFreelancer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
// A BEFORE middleware to check if the user is a freelancer and is verified or not:
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->type === 'freelancer' && !$user->freelancerProfile?->verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account must be verified to perform this action.'
            ], 403);
        }
        return $next($request);
    }
}
