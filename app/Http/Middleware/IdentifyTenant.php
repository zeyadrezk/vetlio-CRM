<?php

namespace App\Http\Middleware;

use App\Models\Organisation;
use App\Services\TenantResolverService;
use App\Support\TenantConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identify Tenant Middleware (Development Only)
 *
 * In local development:
 * - Resolves tenant from session
 * - Redirects to /select-tenant if no tenant selected
 * - Allows easy switching between organisations
 *
 * In production:
 * - Bypasses this middleware entirely (subdomain resolution used instead)
 */
class IdentifyTenant
{
    public function __construct(
        private readonly TenantResolverService $resolver
    ) {}

    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only work in local development
        if (! TenantConfig::isLocalMode()) {
            return $next($request);
        }

        // Skip tenant selector routes
        if ($request->is('select-tenant') || $request->is('select-tenant/*')) {
            return $next($request);
        }

        // Resolve tenant from session
        $tenant = $this->resolver->resolve($request);

        // No tenant? Redirect to selector
        if (! $tenant) {
            return redirect()->route('select-tenant')
                ->with('info', 'Please select an organisation to continue.');
        }

        // Inject tenant into request and app
        $request->merge(['tenant' => $tenant]);
        app()->instance('tenant', $tenant);

        // Ensure session persists
        $this->resolver->storeInSession($tenant);

        return $next($request);
    }
}

