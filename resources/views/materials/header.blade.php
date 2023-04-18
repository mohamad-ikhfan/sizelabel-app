<x-spinner target="create,edit,delete"></x-spinner>
<div class="border-b border-gray-200 dark:border-gray-600 flex justify-between mb-4">
    <div>
        <h3 class="flex items-center mb-4 text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('Materials') }}
        </h3>
    </div>
    <div>
        <x-button type="button" wire:click="create()">Add Material</x-button>
    </div>
</div>
