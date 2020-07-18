<?php
namespace Laragrad\Codifier\Handlers;

use Laragrad\Codifier\Handlers\HandlerInterface as CodifierHandlerInterface;

abstract class AbstractHandler implements CodifierHandlerInterface
{

    /**
     *
     * @param string $key
     * @param array $value
     * @param string $field
     * @param string $baseTransPath
     * @param string $locale
     */
    protected static function transField(string $key, array &$value, string $field, string $baseTransPath, string $locale, array $replaces = [])
    {
        $transPath = $value[$field] ?? "{$baseTransPath}.{$key}.{$field}";

        $value[$field] = self::trans($transPath, $value[$field] ?? null, $locale, $replaces);
    }

    /**
     *
     * @param string $key
     * @param string $default
     * @param string $locale
     * @return string|unknown
     */
    protected static function trans(string $key, string $default = null, string $locale, array $replaces = [])
    {
        $trans = trans($key, $replaces, $locale);

        return ($trans == $key) ? ($default ?? $key) : $trans;
    }

}