<?php

namespace App\Http\Responses;

use App\Filament\Resources\Umkms\UmkmResource;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class OwnerRegistrationResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            return redirect()->to(UmkmResource::getUrl('create'));
        }

        return redirect()->intended(Filament::getUrl());
    }
}
