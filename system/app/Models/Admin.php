<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Notifications\AdminPasswordReset;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'phone', 'avatar', 'status'])]
#[Hidden(['password', 'remember_token'])]
class Admin extends Authenticatable
{
    use LogsActivity, Notifiable, SoftDeletes;

    /** @var array<int, string>|null */
    protected ?array $permissionCache = null;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->role?->isSuperAdmin();
    }

    /**
     * Permission slugs are loaded once per request and reused for every check.
     *
     * @return array<int, string>
     */
    public function permissionSlugs(): array
    {
        if ($this->permissionCache === null) {
            $this->permissionCache = $this->role
                ? $this->role->permissions()->pluck('slug')->all()
                : [];
        }

        return $this->permissionCache;
    }

    public function hasPermission(string $slug): bool
    {
        return $this->isSuperAdmin() || in_array($slug, $this->permissionSlugs(), true);
    }

    /** Reset links must point at the admin panel, not the public site. */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminPasswordReset($token));
    }

    public function activityLabel(): string
    {
        return $this->name;
    }
}
