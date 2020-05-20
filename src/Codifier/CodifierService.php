<?php

namespace Laragrad\Codifier;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class CodifierService
{

    protected $sections = [];

    protected $config;

    /**
     *
     */
    public function __construct()
    {
        $this->config = config('laragrad.codifier.config');
    }

    /**
     *
     * @param string $section
     * @param string $path
     * @return mixed
     */
    public function get(string $section, string $path = null, string $locale = null)
    {
        $data = $this->getSection($section, $locale);

        if ($path) {
            $data = Arr::get($data, $path);
        }

        return $data;
    }

    /**
     *
     * @param string $section
     * @return array|null
     */
    public function getSection(string $section, string $locale = null)
    {
        $this->checkSectionConfigExists($section);

        $locale = $locale ?? app()->getLocale();

        if (! isset($this->sections[$section][$locale])) {
            $this->loadSection($section, $locale);
        }

        return $this->sections[$section][$locale];
    }

    /**
     *
     * @param string $section
     */
    protected function loadSection(string $section, string $locale)
    {
        $useCache = $this->useCache();

        if ($useCache) {

            $cacheKey = $this->getCacheKey($section, $locale);

            if (Cache::has($cacheKey)) {
                $data = Cache::get($cacheKey);
            }
        }

        if (empty($data)) {

            $data = $this->getSectionData($section, $locale);

            if ($useCache) {
                Cache::put($cacheKey, $data);
            }
        }

        $this->sections[$section][$locale] = $data;
    }

    /**
     *
     * @param string $section
     * @return mixed
     */
    protected function getSectionData(string $section, string $locale)
    {
        $sectionConfig = Arr::get($this->config, "sections.{$section}");

        $loader = $sectionConfig['handler'] ?? null;

        $data = $loader[0]::{$loader[1]}($sectionConfig, $locale);

        return $data;
    }

    /**
     *
     * @param string $section
     * @throws \Exception
     * @return array
     */
    protected function getSectionConfig(string $section)
    {
        $sectionConfig = Arr::get($this->config, "sections.{$section}");

        if (! isset($sectionConfig['data_path']) || ! is_string($sectionConfig['data_path'])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_data_path_is_not_configured', [
                'section' => $section
            ]));
        }

        $loader = $sectionConfig['handler'] ?? null;

        if (empty($loader) || ! is_array($loader)) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_is_not_configured', [
                'section' => $section
            ]));
        }

        if (! isset($loader[0]) || ! is_string($loader[0])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_class_not_configured', [
                'section' => $section
            ]));
        } elseif (! class_exists($loader[0])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_class_is_not_exists', [
                'section' => $section,
                'class' => $loader[0]
            ]));
        }

        if (! isset($loader[1]) || ! is_string($loader[1])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_method_not_configured', [
                'section' => $section
            ]));
        } elseif (! method_exists($loader[0], $loader[1])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_method_is_not_exists', [
                'section' => $section,
                'class' => $loader[0],
                'method' => $loader[1]
            ]));
        }

        return $sectionConfig;

    }

    /**
     *
     * @return boolean
     */
    protected function useCache()
    {
        return Arr::get($this->config, 'use_cache', false);
    }

    /**
     *
     * @param string $section
     * @return string
     */
    protected function getCacheKey(string $section, string $locale)
    {
        return "laragrad.codifier.{$section}.{$locale}";
    }

    /**
     * Check for section config exists
     *
     * @param string $section
     * @throws \Exception
     * @return boolean
     */
    public function checkSectionConfigExists(string $section)
    {
        if (!Arr::has($this->config, "sections.{$section}")) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_configuration_not_exists', [
                'section' => $section,
            ]));
        }

        return true;
    }
}