<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganisationApplySettings
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if (!$panel || !auth()->check()) return $next($request);

        if ($panel->getId() == 'app') {
            $organisation = auth()->user()->organisation;

            Filament::getCurrentPanel()->brandName($organisation->name);
        }

        return $next($request);
    }
}
