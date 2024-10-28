<?php

namespace App\Livewire;

use App\Models\Items;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Welcome extends Component
{
    use Toast, WithPagination;

    public $search = '';
    
    public bool $drawer = false;
    public bool $myModal1 = false;
    public bool $isCreating = false;
    
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    
    public $editingItem = [
        'item_id' => '',
        'name' => '',
        'price' => '',
        'quantity' => '',
        'with_tax' => '',
        'href' => ''
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => ['column' => 'name', 'direction' => 'asc']]
    ];

    protected $rules = [
        'editingItem.name' => 'required',
        'editingItem.price' => 'required|numeric',
        'editingItem.quantity' => 'required|integer',
        'editingItem.href' => 'required|url'
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Clear filters
    public function clear(): void
    {
        $this->reset('search', 'sortBy');
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function create(): void
    {
        $this->reset('editingItem');
        $this->isCreating = true;
        $this->myModal1 = true;
    }

    public function update($id): void
    {
        $item = Items::find($id);
        $this->editingItem = $item->toArray();
        $this->isCreating = false;
        $this->myModal1 = true;
    }

    public function delete($id): void
    {
        DB::beginTransaction();
        try {
            Items::where('item_id', $id)->delete();
            DB::commit();
            Cache::tags(['items'])->flush();
            $this->warning("Deleted #$id", position: 'toast-bottom');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to delete item.', position: 'toast-bottom');
        }
    }

    public function saveItem()
    {
        $this->validate();
        
        DB::beginTransaction();
        try {
            if ($this->isCreating) {
                Items::create([
                    'name' => $this->editingItem['name'],
                    'price' => $this->editingItem['price'],
                    'quantity' => $this->editingItem['quantity'],
                    'href' => $this->editingItem['href']
                ]);
                $message = 'Item created successfully.';
            } else {
                Items::where('item_id', $this->editingItem['item_id'])
                    ->update([
                        'name' => $this->editingItem['name'],
                        'price' => $this->editingItem['price'],
                        'quantity' => $this->editingItem['quantity'],
                        'href' => $this->editingItem['href']
                    ]);
                $message = 'Item updated successfully.';
            }
            
            DB::commit();
            Cache::tags(['items'])->flush();
            $this->myModal1 = false;
            $this->success($message, position: 'toast-bottom');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred while saving.', position: 'toast-bottom');
        }
    }

    public function headers(): array
    {
        return Cache::remember('table_headers', now()->addDay(), function () {
            return [
                ['key' => 'item_id', 'label' => '#', 'class' => 'w-1'],
                ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
                ['key' => 'price', 'label' => 'Price', 'class' => 'w-20'],
                ['key' => 'quantity', 'label' => 'Quantity', 'class' => 'w-32'],
                ['key' => 'with_tax', 'label' => 'With tax', 'class' => 'w-20'],
                ['key' => 'href', 'label' => 'Link', 'class' => 'w-64', 'sortable' => false],
            ];
        });
    }

    public function users()
    {
        $cacheKey = 'users_' . $this->search . '_' . json_encode($this->sortBy) . '_' . $this->getPage();
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return Items::query()
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
                ->paginate(10);
        });
    }

    public function render()
    {
        return view('livewire.welcome', [
            'users' => $this->users(),
            'headers' => $this->headers()
        ]);
    }
}