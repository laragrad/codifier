<?php

namespace Laragrad\Codifier\Handlers;

abstract class AbstractHandler
{

    protected static function transField($key, &$value, $field, $baseTransPath, string $locale) {

        $transPath = $value[$field] ?? "{$baseTransPath}.{$key}.{$field}";
        $value[$field] = self::trans($transPath, $value[$field] ?? null, $locale);

    }

    protected static function trans($key, $default = null, string $locale)
    {
        $trans = trans($key, [], $locale);

        return ($trans == $key) ? ($default ?? $key) : $trans;
    }
}