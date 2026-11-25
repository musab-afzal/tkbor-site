<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Builds sequential, prefixed, year scoped document numbers such as
 * QT-2026-0001 or INV-2026-0042. Prefixes come from Settings so the agency can
 * change them without a code change.
 */
class DocumentNumberGenerator
{
    public function __construct(protected Settings $settings) {}

    /**
     * Reserve the next number for a table/column pair. Runs inside a lock so two
     * concurrent creates cannot claim the same sequence value.
     */
    public function next(string $table, string $column, string $settingKey, string $fallbackPrefix, bool $withYear = true): string
    {
        $prefix = trim((string) $this->settings->get($settingKey, $fallbackPrefix)) ?: $fallbackPrefix;
        $segment = $withYear ? $prefix.'-'.now()->year.'-' : $prefix.'-';

        return DB::transaction(function () use ($table, $column, $segment) {
            $last = DB::table($table)
                ->where($column, 'like', $segment.'%')
                ->lockForUpdate()
                ->orderByDesc($column)
                ->value($column);

            $sequence = $last ? (int) substr((string) $last, strlen($segment)) : 0;

            return $segment.str_pad((string) ($sequence + 1), 4, '0', STR_PAD_LEFT);
        });
    }
}
