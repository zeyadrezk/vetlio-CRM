<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectTenantRequest;
use App\Repositories\TenantRepository;
use App\Services\TenantResolverService;
use App\Support\TenantConfig;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Tenant Selector Controller (Development Only)
 *
 * Simple UI to select organisation in local development.
 * Makes it easy to switch between organisations without subdomains.
 *
 * Flow:
 * 1. Visit /select-tenant
 * 2. Choose organisation
 * 3. Redirected to /app/login
 * 4. After login, access dashboard with selected tenant
 */
class TenantSelectorController extends Controller
{
    public function __construct(
        private readonly TenantRepository $repository,
        private readonly TenantResolverService $resolver
    ) {
        // Only allow in local environment
        abort_unless(app()->environment('local'), 404);
    }

    /**
     * Display organisation selector
     */
    public function index(): View
    {
        $organisations = $this->repository->getAllWithBranches();
        $currentTenantId = session(TenantConfig::SESSION_TENANT_ID);

        return view('tenant-selector', [
            'organisations' => $organisations,
            'currentTenantId' => $currentTenantId,
        ]);
    }

    /**
     * Store selected organisation in session
     */
    public function select(SelectTenantRequest $request): RedirectResponse
    {
        $organisation = $request->getOrganisation();

        $this->resolver->storeInSession($organisation);

        return redirect('/app')
            ->with('success', "Switched to: {$organisation->name}");
    }

    /**
     * Clear organisation selection
     */
    public function clear(): RedirectResponse
    {
        $this->resolver->clearSession();

        return redirect()->route('select-tenant')
            ->with('info', 'Selection cleared. Choose an organisation.');
    }
}

