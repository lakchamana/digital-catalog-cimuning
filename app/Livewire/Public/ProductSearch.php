<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'umkm', except: '')]
    public string $umkm = '';

    #[Url(as: 'price', except: 'all')]
    public string $price = 'all';

    #[Url(as: 'sort', except: 'latest')]
    public string $sort = 'latest';

    #[Url(as: 'perPage', except: 9)]
    public int $perPage = 9;

    public function updated(string $property): void
    {
        if ($property !== 'page') {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'umkm']);
        $this->price = 'all';
        $this->sort = 'latest';
        $this->perPage = 9;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.public.product-search', [
            'categories' => $this->categories(),
            'umkms' => $this->umkmOptions(),
            'products' => $this->products(),
            'activeFilterCount' => $this->activeFilterCount(),
        ]);
    }

    protected function categories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->whereHas('products', fn (Builder $query) => $query->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    protected function umkmOptions(): Collection
    {
        return Umkm::query()
            ->where('is_active', true)
            ->where('status', 'verified')
            ->whereHas('products', fn (Builder $query) => $query->where('is_active', true))
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    protected function products(): LengthAwarePaginator
    {
        $search = trim($this->search);

        return Product::query()
            ->with(['category', 'umkm', 'images'])
            ->where('is_active', true)
            ->whereHas('umkm', fn (Builder $query) => $query->where('is_active', true)->where('status', 'verified'))
            ->when($this->category !== '', fn (Builder $query) => $query->whereHas(
                'category',
                fn (Builder $categoryQuery) => $categoryQuery->where('slug', $this->category),
            ))
            ->when($this->umkm !== '', fn (Builder $query) => $query->whereHas(
                'umkm',
                fn (Builder $umkmQuery) => $umkmQuery->where('slug', $this->umkm),
            ))
            ->when($this->price === 'priced', fn (Builder $query) => $query->whereNotNull('price'))
            ->when($this->price === 'contact', fn (Builder $query) => $query->whereNull('price'))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('umkm', fn (Builder $umkmQuery) => $umkmQuery->where('name', 'like', "%{$search}%")->orWhere('rw', 'like', "%{$search}%"));
                });
            })
            ->tap(fn (Builder $query) => $this->applySort($query))
            ->paginate($this->validatedPerPage());
    }

    protected function applySort(Builder $query): void
    {
        match ($this->sort) {
            'az' => $query->orderBy('name'),
            'price_low' => $query->orderByRaw('price is null')->orderBy('price')->orderBy('name'),
            'price_high' => $query->orderByRaw('price is null')->orderByDesc('price')->orderBy('name'),
            default => $query->latest(),
        };
    }

    protected function validatedPerPage(): int
    {
        return in_array($this->perPage, [9, 18, 27], true) ? $this->perPage : 9;
    }

    protected function activeFilterCount(): int
    {
        return collect([
            $this->search !== '',
            $this->category !== '',
            $this->umkm !== '',
            $this->price !== 'all',
            $this->sort !== 'latest',
            $this->perPage !== 9,
        ])->filter()->count();
    }
}
