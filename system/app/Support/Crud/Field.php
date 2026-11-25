<?php

namespace App\Support\Crud;

/**
 * Declarative form field definitions. Controllers describe their form once and
 * the shared crud form view renders it, so no module needs its own markup for
 * an ordinary input.
 */
class Field
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
            'required' => false,
            'full' => false,
            'help' => null,
            'options' => [],
            'placeholder' => null,
            'attributes' => [],
        ], $extra);
    }

    public static function text(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'text', $extra);
    }

    public static function email(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'email', $extra);
    }

    public static function tel(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'tel', $extra);
    }

    public static function url(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'url', $extra);
    }

    /** Never echoes the stored value back into the form. */
    public static function password(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'password', $extra);
    }

    public static function number(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'number', $extra);
    }

    public static function money(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'money', $extra);
    }

    public static function date(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'date', $extra);
    }

    public static function datetime(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'datetime', $extra);
    }

    public static function time(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'time', $extra);
    }

    /**
     * @param  array<string, string>  $options
     */
    public static function select(string $key, string $label, array $options, array $extra = []): array
    {
        return self::make($key, $label, 'select', array_merge(['options' => $options], $extra));
    }

    /**
     * @param  array<string, string>  $options
     */
    public static function multiselect(string $key, string $label, array $options, array $extra = []): array
    {
        return self::make($key, $label, 'multiselect', array_merge(['options' => $options, 'full' => true], $extra));
    }

    public static function textarea(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'textarea', array_merge(['full' => true], $extra));
    }

    public static function editor(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'editor', array_merge(['full' => true, 'rows' => 12], $extra));
    }

    public static function checkbox(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'checkbox', $extra);
    }

    public static function file(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'file', array_merge(['full' => true], $extra));
    }

    /** Comma separated list stored as a JSON array column. */
    public static function list(string $key, string $label, array $extra = []): array
    {
        return self::make($key, $label, 'list', array_merge(['full' => true], $extra));
    }

    /** Section divider inside the form grid. */
    public static function heading(string $label): array
    {
        return self::make('__heading_'.md5($label), $label, 'heading', ['full' => true]);
    }
}
