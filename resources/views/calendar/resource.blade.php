<div class="flex items-center h-12 justify-center p-2 space-x-3 bg-white rounded-lg align-items-center">
    <!-- Slika ili placeholder -->
    <template x-if="resource.extendedProps.avatar">
        <img
            :src="resource.extendedProps.avatar"
            alt="Avatar" width="32" height="32"
            class="w-10 h-10 rounded-full object-contain"
        >
    </template>

    <template x-if="!resource.extendedProps.avatar">
        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold text-sm">
            <span x-text="resource.title.split(' ').map(word => word.charAt(0).toUpperCase()).join('')"></span>
        </div>
    </template>

    <!-- Tekstualni dio -->
    <div class="flex flex-col justify-center h-full">
        <div class="flex flex-col">
            <span class="font-medium text-gray-900 text-sm leading-tight" x-text="resource.title"></span>
            <span class="text-xs text-gray-500 leading-tight" x-text="resource.extendedProps.title">Primjer</span>
        </div>
    </div>
</div>
