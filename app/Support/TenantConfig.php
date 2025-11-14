<?php

namespace App\Support;

/**
 * Tenant Configuration (Development Only)
 *
 * Simple session-based tenant selection for local development.
 * Production uses subdomain-based resolution (handled separately).
 */
class TenantConfig
{
    /**
     * Session keys
     */
    public const SESSION_TENANT_ID = 'tenant_id';
    public const SESSION_TENANT_SUBDOMAIN = 'tenant_subdomain';

    /**
     * Check if running in local development mode
     */
    public static function isLocalMode(): bool
    {
        return app()->environment('local');
    }

    /**
     * Get all session keys as array
     */
    public static function sessionKeys(): array
    {
        return [
            self::SESSION_TENANT_ID,
            self::SESSION_TENANT_SUBDOMAIN,
        ];
    }
}
