<x-spinner target="materialIn,takeMaterial,edit,delete"></x-spinner>
<div class="border-b border-gray-200 dark:border-gray-600 flex justify-between mb-4">
    <div>
        <h3 class="flex items-center mb-4 text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('Stock Materials') }}
        </h3>
    </div>
    <div class="flex justify-end justify-items-center mb-4 space-x-4">
        <x-button type="button" wire:click="materialIn()">Material In</x-button>
        <x-button type="button" wire:click="takeMaterial()">Take Stock</x-button>
    </div>
</div>
