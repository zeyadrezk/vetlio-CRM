<?php

namespace App\Services;

use App\Models\Organisation;
use App\Repositories\TenantRepository;
use App\Support\TenantConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Tenant Resolver Service (Development Only)
 *
 * Simple session-based tenant resolution for local development.
 * Allows developers to select and switch between organisations easily.
 */
class TenantResolverService
{
    public function __construct(
        private readonly TenantRepository $repository
    ) {}

    /**
     * Resolve the current tenant from session
     *
     * In local mode: Returns tenant from session
     * In production: Returns null (subdomain resolution happens elsewhere)
     */
    public function resolve(Request $request): ?Organisation
    {
        // Only work in local development
        if (! TenantConfig::isLocalMode()) {
            return null;
        }

        $tenantId = session(TenantConfig::SESSION_TENANT_ID);

        if (! $tenantId) {
            Log::debug('No tenant in session');
            return null;
        }

        $tenant = $this->repository->findById($tenantId);

        if ($tenant) {
            Log::debug('Tenant resolved from session', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
            ]);
        } else {
            Log::warning('Tenant ID in session but not found in database', [
                'tenant_id' => $tenantId,
            ]);
        }

        return $tenant;
    }

    /**
     * Store tenant in session (development only)
     */
    public function storeInSession(Organisation $tenant): void
    {
        session([
            TenantConfig::SESSION_TENANT_ID => $tenant->id,
            TenantConfig::SESSION_TENANT_SUBDOMAIN => $tenant->subdomain,
        ]);

        Log::info('Tenant stored in session (dev mode)', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
        ]);
    }

    /**
     * Clear tenant from session
     */
    public function clearSession(): void
    {
        session()->forget(TenantConfig::sessionKeys());

        Log::info('Tenant session cleared (dev mode)');
    }
}
