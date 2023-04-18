@once
    <x-dialog-modal wire:model="isModalOpen" maxWidth="xl">
        <x-slot name="title">
            {{ $title }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                @foreach ($materials as $value)
                    <div class="flex justify-between space-x-4">
                        <x-label class="block w-full my-auto" for="{{ 'material_quantities_' . $value->id }}"
                            value="{{ strtoupper($value->code) . ' | ' . ucwords($value->name) }}" />
                        <div class="w-full">
                            <x-input id="{{ 'material_quantities_' . $value->id }}" class="block w-full" type="number"
                                autocomplete="material_quantities" placeholder="Quantity"
                                wire:model="{{ 'material_quantities.' . $value->id }}" />
                            <x-input-error for="material_quantities"></x-input-error>
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-between space-x-4">
                    <x-label class="block w-full my-auto" for="date" value="Date" />
                    <div class="w-full">
                        <x-input id="date" class="block w-full" type="date" autocomplete="date" wire:model="date" />
                        <x-input-error for="date"></x-input-error>
                    </div>
                </div>
                <div class="flex justify-between space-x-4">
                    <x-label class="block w-full my-auto" for="description" value="Description" />
                    <div class="w-full">
                        <x-input id="description" class="block w-full" type="text" autocomplete="description"
                            wire:model="description" />
                        <x-input-error for="description"></x-input-error>
                    </div>
                </div>

                <x-spinner target="store,update,closeModal"></x-spinner>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                Cancel
            </x-secondary-button>

            @if ($content === 'Save')
                <x-button class="ml-2" wire:click="store()" wire:loading.attr="disabled">
                    {{ $content }}
                </x-button>
            @else
                <x-button class="ml-2" wire:click="update()" wire:loading.attr="disabled">
                    {{ $content }}
                </x-button>
            @endif
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="isModalConfirm" maxWidth="sm">
        <x-slot name="title"></x-slot>

        <x-slot name="content">
            <h5 class="text-center">{{ $title }}</h5>
            <x-spinner target="destroy,closeModal"></x-spinner>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                Cancel
            </x-secondary-button>

            <x-button class="ml-2" wire:click="destroy()" wire:loading.attr="disabled">
                {{ $content }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
@endonce
