<div class="flex flex-col items-start p-2 space-y-1 w-full overflow-hidden">
    <!-- Vrijeme i trajanje -->
    <div class="flex items-center space-x-1 w-full truncate">
        <!-- Ikona vremena -->
        <svg class="w-3 h-3 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs text-gray-700 truncate w-full">
            <span x-text="event.extendedProps.start"></span> -
            <span x-text="event.extendedProps.end"></span>
        </span>
    </div>

    <!-- Klijent -->
    <div class="flex items-center space-x-1 w-full truncate">
        <!-- Ikona klijenta -->
        <svg class="w-3 h-3 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.121 17.804z"/>
        </svg>
        <span class="text-2xs text-gray-700 truncate w-full" x-text="event.extendedProps.client"></span>
    </div>

    <!-- Usluga -->
    <div class="flex items-center space-x-1 w-full truncate">
        <!-- Ikona usluge -->
        <svg class="w-3 h-3 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6"/>
        </svg>
        <span class="text-2xs text-gray-700 truncate w-full" x-text="event.extendedProps.service"></span>
    </div>

    <!-- Lokacija -->
    <div class="flex items-center space-x-1 w-full truncate">
        <!-- Ikona lokacije -->
        <svg class="w-3 h-3 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4.5 8-11c0-4.418-3.582-8-8-8s-8 3.582-8 8c0 6.5 8 11 8 11z"/>
        </svg>
        <span class="text-2xs text-gray-700 truncate w-full" x-text="event.extendedProps.location"></span>
    </div>
</div>
