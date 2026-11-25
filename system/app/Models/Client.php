<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * One client record serves both the CRM and the website "our clients" section —
 * logo, description and status drive the frontend, the rest drives the CRM.
 */
#[Fillable([
    'client_code', 'type', 'lead_id', 'name', 'company_name', 'contact_person',
    'email', 'phone', 'whatsapp', 'address', 'city', 'country', 'postal_code',
    'tax_number', 'tax_name', 'industry', 'currency', 'website', 'logo',
    'description', 'notes', 'status',
])]
class Client extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const TYPE_INDIVIDUAL = 'individual';

    public const TYPE_COMPANY = 'company';

    public const TYPES = [
        self::TYPE_INDIVIDUAL => 'Individual',
        self::TYPE_COMPANY => 'Company',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            $client->client_code = $client->client_code
                ?: app(DocumentNumberGenerator::class)->next('clients', 'client_code', 'client_prefix', 'CL');
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ClientApproval::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function displayName(): string
    {
        return $this->company_name ?: $this->name;
    }

    public function activityLabel(): string
    {
        return $this->displayName();
    }

    /** Total invoiced minus what has been received. */
    public function outstandingBalance(): float
    {
        return round((float) $this->invoices()->whereNotIn('status', ['draft', 'cancelled'])->sum('balance'), 2);
    }
}
