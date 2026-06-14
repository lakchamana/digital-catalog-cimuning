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
    public int|string $perPage = 9;

    public function mount(): void
    {
        $this->normalizeFilters();
    }

    public function updated(string $property): void
    {
        $this->normalizeFilters();

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
            ->where(function (Builder $query) {
                $query
                    ->whereHas('products', fn (Builder $productQuery) => $this->applyPublicProductScope($productQuery))
                    ->orWhereHas('umkms', fn (Builder $umkmQuery) => $umkmQuery
                        ->where('is_active', true)
                        ->where('status', 'verified')
                        ->whereHas('products', fn (Builder $productQuery) => $productQuery->where('is_active', true)));
            })
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

        $this->normalizeFilters();

        return $this->applyPublicProductScope(Product::query())
            ->with(['category', 'umkm', 'images'])
            ->when($this->category !== '', fn (Builder $query) => $query->where(function (Builder $categoryScope) {
                $categoryScope
                    ->whereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('slug', $this->category))
                    ->orWhereHas('umkm.category', fn (Builder $categoryQuery) => $categoryQuery->where('slug', $this->category));
            }))
            ->when($this->umkm !== '', fn (Builder $query) => $query->whereHas(
                'umkm',
                fn (Builder $umkmQuery) => $umkmQuery->where('slug', $this->umkm),
            ))
            ->when($this->price === 'priced', fn (Builder $query) => $query->where('price', '>', 0))
            ->when($this->price === 'contact', fn (Builder $query) => $query->where(function (Builder $priceQuery) {
                $priceQuery->whereNull('price')->orWhere('price', '<=', 0);
            }))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('umkm.category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
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
        $perPage = (int) $this->perPage;

        return in_array($perPage, [9, 18, 27], true) ? $perPage : 9;
    }

    protected function activeFilterCount(): int
    {
        return collect([
            $this->search !== '',
            $this->category !== '',
            $this->umkm !== '',
            $this->price !== 'all',
            $this->sort !== 'latest',
            (int) $this->perPage !== 9,
        ])->filter()->count();
    }

    protected function applyPublicProductScope(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('umkm', fn (Builder $umkmQuery) => $umkmQuery->where('is_active', true)->where('status', 'verified'));
    }

    protected function normalizeFilters(): void
    {
        $this->search = trim($this->search);

        if (! in_array($this->price, ['all', 'priced', 'contact'], true)) {
            $this->price = 'all';
        }

        if (! in_array($this->sort, ['latest', 'az', 'price_low', 'price_high'], true)) {
            $this->sort = 'latest';
        }

        $this->perPage = $this->validatedPerPage();

        if ($this->category !== '' && ! $this->validCategorySlug($this->category)) {
            $this->category = '';
        }

        if ($this->umkm !== '' && ! $this->validUmkmSlug($this->umkm)) {
            $this->umkm = '';
        }
    }

    protected function validCategorySlug(string $slug): bool
    {
        return Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query
                    ->whereHas('products', fn (Builder $productQuery) => $this->applyPublicProductScope($productQuery))
                    ->orWhereHas('umkms', fn (Builder $umkmQuery) => $umkmQuery
                        ->where('is_active', true)
                        ->where('status', 'verified')
                        ->whereHas('products', fn (Builder $productQuery) => $productQuery->where('is_active', true)));
            })
            ->exists();
    }

    protected function validUmkmSlug(string $slug): bool
    {
        return Umkm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'verified')
            ->whereHas('products', fn (Builder $query) => $query->where('is_active', true))
            ->exists();
    }
}
