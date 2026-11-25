<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Thin cached accessor over the settings table. Nothing in the app reads
 * agency details from constants — they all come through here.
 */
class Settings
{
    protected const CACHE_KEY = 'app.settings';

    /** @var array<string, string|null>|null */
    protected ?array $items = null;

    public function all(): array
    {
        if ($this->items === null) {
            $this->items = Cache::rememberForever(
                self::CACHE_KEY,
                fn () => Setting::query()->pluck('value', 'key')->all()
            );
        }

        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->all()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value, 'group' => $group, 'type' => $type],
        );

        $this->flush();
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value, 'group' => $group],
            );
        }

        $this->flush();
    }

    public function currency(): string
    {
        return (string) $this->get('currency', 'USD');
    }

    public function currencySymbol(): string
    {
        return (string) $this->get('currency_symbol', '$');
    }

    public function taxPercentage(): float
    {
        return (float) $this->get('tax_percentage', 0);
    }

    public function money(float|int|null $amount, ?string $currency = null): string
    {
        $symbol = $currency && $currency !== $this->currency() ? $currency.' ' : $this->currencySymbol();

        return $symbol.number_format((float) $amount, 2);
    }

    public function flush(): void
    {
        $this->items = null;
        Cache::forget(self::CACHE_KEY);
    }
}
