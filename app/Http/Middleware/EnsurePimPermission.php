<?php

namespace App\Http\Middleware;

use App\Services\Pim\PimService;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePimPermission
{
    private PimService $pimService;

    public function __construct(PimService $pimService)
    {
        $this->pimService = $pimService;
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$this->pimService->isEnabled()) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user) {
            throw new AuthorizationException('Authentication required for privileged operations.');
        }

        if (empty($permissions)) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($user->hasActivePimPermission($permission)) {
                return $next($request);
            }
        }

        throw new AuthorizationException('Active PIM activation required for this operation.');
    }
}
