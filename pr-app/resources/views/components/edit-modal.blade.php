<x-modal wire:model="myModal1" title="Edit Item #{{ $editingItem['item_id'] }}" separator class="lg:w-1/3">
    <div class="space-y-4 p-2">
        <!-- Name Input -->
        <div>
            <x-input 
                label="Name" 
                wire:model="editingItem.name" 
                placeholder="Enter item name"
                hint="@error('editingItem.name') {{ $message }} @enderror"
            />
        </div>

        <!-- Price and Quantity Row -->
        <div class="grid grid-cols-2 gap-4">
            <x-input 
                label="Price" 
                wire:model="editingItem.price"
                type="number"
                step="0.01"
                placeholder="0.00"
                hint="@error('editingItem.price') {{ $message }} @enderror"
            />
            
            <x-input 
                label="Quantity" 
                wire:model="editingItem.quantity"
                type="number"
                placeholder="0"
                hint="@error('editingItem.quantity') {{ $message }} @enderror"
            />
        </div>

        <!-- Link Input -->
        <div>
            <x-input 
                label="Link" 
                wire:model="editingItem.href"
                placeholder="https://"
                hint="@error('editingItem.href') {{ $message }} @enderror"
            />
        </div>
    </div>

    <x-slot:actions>
        <div class="flex justify-end gap-2">
            <x-button 
                label="Cancel" 
                icon="o-x-mark"
                @click="$wire.myModal1 = false" 
            />
            <x-button 
                label="Save Changes" 
                icon="o-check"
                wire:click="saveItem" 
                class="btn-primary"
                spinner
            />
        </div>
    </x-slot:actions>
</x-modal>