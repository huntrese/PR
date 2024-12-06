<div>
    <!-- HEADER -->
    <x-header title="Steam Dota 2 Skins" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input 
                placeholder="Search..." 
                wire:model.lazy="search" 
                clearable 
                icon="o-magnifying-glass"
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-button 
                label="Create" 
                wire:click="create" 
                icon="o-plus" 
                class="btn-primary mr-2"
            />
            <x-button 
                label="Filters" 
                @click="$wire.drawer = true" 
                responsive 
                icon="o-funnel" 
                class="btn-primary" 
            />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy">
            @scope('actions', $user)
            <div class="flex flex-row">
                <x-button 
                    icon="o-pencil" 
                    wire:click="update({{ $user['item_id'] }})"
                    class="btn-ghost btn-sm text-yellow-400" 
                />    
                <x-button 
                    icon="o-trash" 
                    wire:click="delete({{ $user['item_id'] }})"
                    class="btn-ghost btn-sm text-red-500" 
                />
            </div>
            @endscope
        </x-table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer 
        wire:model="drawer" 
        title="Filters" 
        right 
        separator 
        with-close-button 
        class="lg:w-1/3"
    >
        <x-input 
            placeholder="Search..." 
            wire:model.lazy="search" 
            icon="o-magnifying-glass"
        />
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear"  />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

    <!-- MODAL -->
    @if($myModal1)
    <x-modal 
        wire:model="myModal1" 
        :title="$isCreating ? 'Create New Item' : 'Edit Item #' . $editingItem['item_id']" 
        separator 
        class="lg:w-1/3"
    >
        <div class="space-y-4 p-2">
            <x-input 
                label="Name" 
                wire:model.defer="editingItem.name" 
                placeholder="Enter item name"
                :hint="$errors->first('editingItem.name')"
            />
            <div class="grid grid-cols-2 gap-4">
                <x-input 
                    label="Price" 
                    wire:model.defer="editingItem.price"
                    type="number"
                    step="0.01"
                    placeholder="0.00"
                    :hint="$errors->first('editingItem.price')"
                />
                <x-input 
                    label="Quantity" 
                    wire:model.defer="editingItem.quantity"
                    type="number"
                    placeholder="0"
                    :hint="$errors->first('editingItem.quantity')"
                />
            </div>
            <x-input 
                label="Link" 
                wire:model.defer="editingItem.href"
                type="url"
                placeholder="https://example.com"
                :hint="$errors->first('editingItem.href')"
            />
        </div>
        <x-slot:actions>
            <x-button 
                label="Cancel" 
                @click="$wire.myModal1 = false" 
                icon="o-x-mark"
            />
            <x-button 
                label="Save" 
                icon="o-check" 
                class="btn-primary" 
                wire:click="saveItem" 
            />
        </x-slot:actions>
    </x-modal>
    @endif
</div>
