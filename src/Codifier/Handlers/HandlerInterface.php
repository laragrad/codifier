<?php

namespace Laragrad\Codifier\Handlers;

interface HandlerInterface
{
    /**
     * Section loader
     *
     * @param array $sectionConfig
     * @param string $locale
     * @return array
     */
    public function load(array $sectionConfig, string $locale);

}