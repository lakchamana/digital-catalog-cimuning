<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\User;
use App\Support\AdminActivityLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CategoryObserver
{
    private const AUDITED_FIELDS = [
        'name', 'slug', 'description', 'icon', 'is_active', 'sort_order',
    ];

    public function created(Category $category): void
    {
        $admin = $this->admin();

        if (! $admin) {
            return;
        }

        AdminActivityLogger::record(
            event: 'category_created',
            actor: $admin,
            subject: $category,
            after: $this->snapshot($category->attributesToArray()),
        );
    }

    public function updated(Category $category): void
    {
        $admin = $this->admin();
        $changes = Arr::only($category->getChanges(), self::AUDITED_FIELDS);

        if (! $admin || $changes === []) {
            return;
        }

        $fields = array_keys($changes);

        AdminActivityLogger::record(
            event: 'category_updated',
            actor: $admin,
            subject: $category,
            before: Arr::only($category->getPrevious(), $fields),
            after: Arr::only($category->attributesToArray(), $fields),
            metadata: ['changed_fields' => $fields],
        );
    }

    public function deleted(Category $category): void
    {
        $admin = $this->admin();

        if (! $admin) {
            return;
        }

        AdminActivityLogger::record(
            event: 'category_deleted',
            actor: $admin,
            subject: $category,
            subjectLabel: $category->name,
            before: $this->snapshot($category->attributesToArray()),
        );
    }

    private function admin(): ?User
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin() ? $user : null;
    }

    private function snapshot(array $attributes): array
    {
        return Arr::only($attributes, self::AUDITED_FIELDS);
    }
}
