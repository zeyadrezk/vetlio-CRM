<?php

namespace App\Repositories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Tenant Repository
 *
 * Handles all database queries related to tenant (Organisation) retrieval.
 * Implements caching to reduce database queries for frequently accessed tenants.
 */
class TenantRepository
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Find an active tenant by ID
     */
    public function findById(int $id): ?Organisation
    {
        return Cache::remember(
            $this->getCacheKey('id', $id),
            self::CACHE_TTL,
            fn () => Organisation::query()
                ->whereActive(true)
                ->find($id)
        );
    }

    /**
     * Find an active tenant by subdomain
     */
    public function findBySubdomain(string $subdomain): ?Organisation
    {
        return Cache::remember(
            $this->getCacheKey('subdomain', $subdomain),
            self::CACHE_TTL,
            fn () => Organisation::query()
                ->whereActive(true)
                ->where('subdomain', $subdomain)
                ->first()
        );
    }

    /**
     * Get all active tenants with their branches
     */
    public function getAllWithBranches(): Collection
    {
        return Organisation::query()
            ->whereActive(true)
            ->with('branches')
            ->has('branches')
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if a tenant exists and is active
     */
    public function exists(int $id): bool
    {
        return $this->findById($id) !== null;
    }

    /**
     * Clear cached tenant data
     */
    public function clearCache(int $id): void
    {
        Cache::forget($this->getCacheKey('id', $id));
    }

    /**
     * Generate cache key for tenant lookup
     */
    private function getCacheKey(string $type, string|int $value): string
    {
        return "tenant:{$type}:{$value}";
    }
}
