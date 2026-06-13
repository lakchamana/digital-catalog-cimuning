<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\Umkm;
use App\Support\UmkmVerificationWorkflow;
use App\Support\UniqueSlug;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class UmkmRegistrationForm extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $category_id = '';

    public string $description = '';

    public string $owner_name = '';

    public string $whatsapp = '';

    public string $email = '';

    public string $address = '';

    public string $rw = '';

    public bool $service_delivery = false;

    public bool $service_cod = false;

    public bool $service_custom_order = false;

    public bool $has_physical_store = false;

    public $logo = null;

    public $cover = null;

    public bool $submitted = false;

    public ?string $submittedName = null;

    public function submit(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'description' => ['required', 'string', 'min:20', 'max:1500'],
            'owner_name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'rw' => ['nullable', 'string', 'max:10'],
            'service_delivery' => ['boolean'],
            'service_cod' => ['boolean'],
            'service_custom_order' => ['boolean'],
            'has_physical_store' => ['boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'description.min' => 'Ceritakan usaha minimal 20 karakter agar admin mudah meninjau.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, tanda -, dan kurung.',
        ]);

        $logoPath = $this->logo?->store('umkms/logos', 'public');
        $coverPath = $this->cover?->store('umkms/covers', 'public');

        $umkm = Umkm::query()->create([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => UniqueSlug::make($validated['name'], Umkm::class),
            'description' => $validated['description'],
            'owner_name' => $validated['owner_name'],
            'phone' => $validated['whatsapp'],
            'whatsapp' => $validated['whatsapp'],
            'email' => $validated['email'] ?: null,
            'address' => $validated['address'],
            'rw' => $validated['rw'] ?: null,
            'logo_image' => $logoPath,
            'cover_image' => $coverPath,
            'status' => 'pending',
            'is_featured' => false,
            'is_active' => false,
            'service_delivery' => $validated['service_delivery'],
            'service_cod' => $validated['service_cod'],
            'service_custom_order' => $validated['service_custom_order'],
            'has_physical_store' => $validated['has_physical_store'],
            'view_count' => 0,
        ]);

        UmkmVerificationWorkflow::notifyAdminsOfRegistration($umkm);

        $this->submitted = true;
        $this->submittedName = $validated['name'];

        $this->reset([
            'name',
            'category_id',
            'description',
            'owner_name',
            'whatsapp',
            'email',
            'address',
            'rw',
            'service_delivery',
            'service_cod',
            'service_custom_order',
            'has_physical_store',
            'logo',
            'cover',
        ]);
    }

    public function createAnother(): void
    {
        $this->submitted = false;
        $this->submittedName = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.public.umkm-registration-form', [
            'categories' => $this->categories(),
        ]);
    }

    protected function categories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
