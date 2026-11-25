<?php

namespace App\Support\Crud;

/**
 * Declarative table column definitions used by the shared crud index view.
 */
class Column
{
    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    protected static function make(string $key, string $label, string $type, array $extra = []): array
    {
        return array_merge([
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'map' => [],
            'sortable' => false,
            'muted' => false,
        ], $extra);
    }

    public static function text(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'text', $extra);
    }

    /** Bold identifier column, e.g. a document number. */
    public static function code(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'code', $extra);
    }

    public static function date(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'date', $extra);
    }

    public static function datetime(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'datetime', $extra);
    }

    public static function money(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'money', $extra);
    }

    public static function number(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'number', $extra);
    }

    /**
     * Status pill. $map converts the stored value to a readable label.
     *
     * @param  array<string, string>  $map
     */
    public static function badge(string $key, string $label, array $map = [], array $extra = []): array
    {
        return self::make($key, $label, 'badge', array_merge(['map' => $map], $extra));
    }

    public static function boolean(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'boolean', $extra);
    }

    /** Dot-notation value pulled from a relation, e.g. client.company_name. */
    public static function relation(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'relation', $extra);
    }

    public static function image(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'image', $extra);
    }

    public static function progress(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'progress', $extra);
    }

    /** Fully custom cell rendered by a closure receiving the record. */
    public static function custom(string $key, string $label, callable $render, array $extra = []): array
    {
        return self::make($key, $label, 'custom', array_merge(['render' => $render], $extra));
    }
}
