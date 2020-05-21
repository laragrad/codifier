<?php
namespace Laragrad\Codifier\Handlers;

abstract class AbstractHandler
{

    /**
     *
     * @param string $key
     * @param array $value
     * @param string $field
     * @param string $baseTransPath
     * @param string $locale
     */
    protected static function transField(string $key, array &$value, string $field, string $baseTransPath, string $locale)
    {
        $transPath = $value[$field] ?? "{$baseTransPath}.{$key}.{$field}";

        $value[$field] = self::trans($transPath, $value[$field] ?? null, $locale);
    }

    /**
     *
     * @param string $key
     * @param string $default
     * @param string $locale
     * @return string|unknown
     */
    protected static function trans(string $key, string $default = null, string $locale)
    {
        $trans = trans($key, [], $locale);

        return ($trans == $key) ? ($default ?? $key) : $trans;
    }
}