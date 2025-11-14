<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Organisation - Vetlio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/50 to-slate-50 min-h-screen antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center space-y-4">
                <!-- Logo / Brand Icon -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <div class="w-20 h-20 bg-gradient-to-br from-[#1561a7] to-[#0e4374] rounded-2xl shadow-xl flex items-center justify-center transform hover:scale-105 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                        Select Your Organisation
                    </h1>
                    <p class="mt-3 text-base text-gray-600 max-w-2xl mx-auto">
                        Choose an organisation to access your workspace
                    </p>
                </div>

                <!-- Development Mode Badge -->
                <div class="flex justify-center">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 border border-amber-200 shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                        </svg>
                        Development Mode
                    </span>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="rounded-lg bg-green-50 p-4 border border-green-200" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="show = false" class="inline-flex text-green-400 hover:text-green-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('warning') || session('info'))
                <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">{{ session('warning') ?? session('info') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="show = false" class="inline-flex text-yellow-400 hover:text-yellow-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg bg-red-50 p-4 border border-red-200" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your selection:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="show = false" class="inline-flex text-red-400 hover:text-red-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="max-w-3xl mx-auto" x-data="{ search: '', loading: false }">

                <!-- Organisations Grid -->
                <div class="mt-10 flex flex-wrap justify-center gap-6">
                    @forelse($organisations as $organisation)
                        <div
                            x-show="search === '' || '{{ strtolower($organisation->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($organisation->subdomain) }}'.includes(search.toLowerCase())"
                            x-transition
                            class="relative"
                        >
                            <form
                                action="{{ route('select-tenant.select') }}"
                                method="POST"
                                @submit="loading = true"
                            >
                                @csrf
                                <input type="hidden" name="organisation_id" value="{{ $organisation->id }}">
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="relative w-64 text-left bg-white rounded-xl p-6 border-4 transition-all duration-300 group disabled:opacity-50 disabled:cursor-not-allowed overflow-hidden cursor-pointer {{ $currentTenantId === $organisation->id ? 'border-[#1561a7] shadow-xl' : 'border-gray-500 shadow-md hover:border-[#1561a7] hover:shadow-xl hover:-translate-y-1' }}"
                                    aria-label="Select {{ $organisation->name }}"
                                >
                                    <!-- Background Gradient Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-br from-[#1561a7]/5 via-blue-50/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    <div class="relative">
                                        @if($currentTenantId === $organisation->id)
                                            <div class="absolute -top-3 -right-3">
                                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-gradient-to-r from-[#1561a7] to-[#0e4374] text-white shadow-lg">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    ACTIVE
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Organisation Logo/Icon -->
                                        <div class="flex justify-center mb-6">
                                            <div class="flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-[#1561a7] to-[#0e4374] text-white font-bold text-3xl shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                                {{ strtoupper(substr($organisation->name, 0, 1)) }}
                                            </div>
                                        </div>

                                        <!-- Organisation Name -->
                                        <h3 class="text-2xl font-extrabold text-gray-900 mb-3 text-center group-hover:text-[#1561a7] transition-colors">
                                            {{ $organisation->name }}
                                        </h3>

                                        <!-- Subdomain Card -->
                                        <div class="mb-6 bg-gradient-to-r from-gray-50 to-gray-100 group-hover:from-blue-50 group-hover:to-indigo-50 border-2 border-gray-200 group-hover:border-[#1561a7] rounded-xl p-4 transition-all duration-300">
                                            <div class="flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500 group-hover:text-[#1561a7] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                                </svg>
                                                <span class="text-sm font-bold text-gray-700 group-hover:text-[#1561a7] transition-colors">
                                                    {{ $organisation->subdomain }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Branches Count Card -->
                                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 group-hover:from-blue-50 group-hover:to-indigo-50 border-2 border-gray-200 group-hover:border-[#1561a7] rounded-xl p-4 transition-all duration-300">
                                            <div class="flex items-center justify-center gap-3">
                                                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-white shadow-md">
                                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-[#1561a7] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-900 group-hover:text-[#1561a7] transition-colors">
                                                        {{ $organisation->branches->count() }}
                                                    </div>
                                                    <div class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                                        {{ Str::plural('Branch', $organisation->branches->count()) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Click Indicator -->
                                        <div class="mt-6 flex justify-center">
                                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-500 group-hover:text-[#1561a7] transition-colors">
                                                <span>Click to Select</span>
                                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Loading Spinner Overlay -->
                                    <div x-show="loading" x-cloak class="absolute inset-0 bg-white/90 backdrop-blur-sm flex items-center justify-center rounded-2xl">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg class="animate-spin h-8 w-8 text-[#1561a7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Connecting...</span>
                                        </div>
                                    </div>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">No organisations available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Info Box -->
            <div class="max-w-2xl mx-auto mt-10">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#1561a7]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Development Workflow</h3>
                            <ol class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-white rounded-full flex items-center justify-center text-xs font-semibold text-[#1561a7] mt-0.5">1</span>
                                    <span>Select an organisation from the cards above</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-white rounded-full flex items-center justify-center text-xs font-semibold text-[#1561a7] mt-0.5">2</span>
                                    <span>You'll be redirected to the login page</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-white rounded-full flex items-center justify-center text-xs font-semibold text-[#1561a7] mt-0.5">3</span>
                                    <span>After authentication, access your dashboard</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="flex-shrink-0 w-5 h-5 bg-white rounded-full flex items-center justify-center text-xs font-semibold text-[#1561a7] mt-0.5">4</span>
                                    <span>Switch organisations anytime from the user menu</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="text-center mt-8 space-y-4">
                @if($currentTenantId)
                    <form action="{{ route('select-tenant.clear') }}" method="POST" x-data="{ clearing: false }" @submit="clearing = true">
                        @csrf
                        <button
                            type="submit"
                            :disabled="clearing"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span x-show="!clearing">Clear Current Selection</span>
                            <span x-show="clearing" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Clearing...
                            </span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
