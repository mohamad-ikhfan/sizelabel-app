@once
    <x-dialog-modal wire:model="isModalOpen" maxWidth="xl">
        <x-slot name="title">
            {{ $title }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="material_group" value="Material Group" />
                    <x-input id="material_group" wire:model="material_group" class="block mt-1 w-full capitalize" type="text"
                        autocomplete="material_group" placeholder="(required)" />
                    <x-input-error for="material_group"></x-input-error>
                </div>
                <div>
                    <x-label for="name" value="Material" />
                    <x-input id="name" wire:model="name" class="block mt-1 w-full capitalize" type="text"
                        autocomplete="name" placeholder="(required)" />
                    <x-input-error for="name"></x-input-error>
                </div>
                <div>
                    <x-label for="code" value="Code" />
                    <x-input id="code" wire:model="code" class="block mt-1 w-full uppercase" type="text"
                        autocomplete="code" placeholder="(required)" />
                    <x-input-error for="code"></x-input-error>
                </div>
                <div>
                    <x-label for="description" value="Description" />
                    <x-input id="description" wire:model="description" class="block mt-1 w-full capitalize" type="text"
                        autocomplete="description" />
                    <x-input-error for="description"></x-input-error>
                </div>
                <x-spinner target="store,update,closeModal"></x-spinner>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                Cancel
            </x-secondary-button>

            @if ($content === 'Add')
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
