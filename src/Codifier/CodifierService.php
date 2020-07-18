<?php
namespace Laragrad\Codifier;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class CodifierService
{

    protected $sections = [];

    protected $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = config('laragrad.codifier.config');
    }

    /**
     * Get handled $section data element with $path
     *
     * @param string $section
     * @param string|NULL $path
     * @param string|NULL $locale - you can define locale
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
     * Get many or all sections data as array
     *
     * @param array|NULL $sections
     * @param string|NULL $locale - you can define locale
     * @return array[]
     */
    public function getSections(array $sections = null, string $locale = null)
    {
        $sections = $sections ?? array_keys($this->config['sections']);

        $data = [];
        foreach ($sections as $section) {
            $data[$section] = $this->getSection($section, $locale);
        }

        return $data;
    }

    /**
     * Get $section data
     *
     * @param string $section
     * @param string|NULL $locale
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
     * Warm cache
     *
     * @param string|string[]|null $sections
     * @param string|string[]|null $locales
     * @return string[]
     */
    public function cache($sections = null, $locales = null)
    {
        if (!$this->useCache()) {
            return [
                'cache using is disabled',
            ];
        }

        $sections = $this->prepareSections($sections);
        $locales = $this->prepareLocales($locales);

        $result = [];
        foreach ($locales as $locale => $localeName) {
            foreach ($sections as $section) {

                $this->getSection($section, $locale);
                $cacheKey = $this->getCacheKey($section, $locale);
                $result[] = "cache key {$cacheKey} warmed";

            }
        }

        return $result;
    }

    /**
     * Clear cache
     *
     * @param string|string[]|null $sections
     * @param string|string[]|null $locales
     * @return string[]
     */
    public function clear($sections = null, $locales = null)
    {
        if (!$this->useCache()) {
            return [
                'cache using is disabled',
            ];
        }

        $sections = $this->prepareSections($sections);
        $locales = $this->prepareLocales($locales);

        $result = [];
        foreach ($locales as $locale => $localeName) {
            foreach ($sections as $section) {

                $cacheKey = $this->getCacheKey($section, $locale);

                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $result[] = "cache key {$cacheKey} cleared";
                }

                unset($this->sections[$section][$locale]);
            }
        }

        return $result;
    }

    private function prepareSections($sections = null)
    {
        $sections = $sections ?? array_keys($this->config['sections']);
        if (is_string($sections)) {
            $sections = [$sections];
        }

        return $sections;
    }

    private function prepareLocales($locales = null)
    {
        $locales = $locales ?? config('app.available_locales', [app()->getLocale()]);
        if (is_string($locales)) {
            $locales = [$locales];
        }

        return $locales;
    }

    /**
     * Load section data to local storage from cache or by handler
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
     * Get section data by handler
     *
     * @param string $section
     * @return mixed
     */
    protected function getSectionData(string $section, string $locale)
    {
        $sectionConfig = $this->getSectionConfig($section);

        $handlerClass = $sectionConfig['handler'];
        $handler = \App::make($handlerClass);

        if ($handler instanceof \Laragrad\Codifier\Handlers\HandlerInterface) {

            return $handler->load($sectionConfig, $locale);

        } else {

            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_class_not_implements_interface', [
                'section' => $section,
                'class' => $handlerClass,
            ]));

        }
    }

    /**
     * Get section configuration
     *
     * @param string $section
     * @throws \Exception
     * @return array
     */
    protected function getSectionConfig(string $section)
    {
        $this->checkSectionConfigExists($section);

        $sectionConfig = Arr::get($this->config, "sections.{$section}");

        $this->validateSectionConfig($section, $sectionConfig);

        return $sectionConfig;
    }

    /**
     * Get use_cache configuration
     *
     * @return boolean
     */
    protected function useCache()
    {
        return Arr::get($this->config, 'use_cache', false);
    }

    /**
     * Get cache key
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
    protected function checkSectionConfigExists(string $section)
    {
        if (! isset($this->config['sections'][$section])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_config_not_exists', [
                'section' => $section
            ]));
        }

        return true;
    }

    /**
     * Section configuration validation
     *
     * @param array $config
     * @throws \Exception
     */
    protected function validateSectionConfig(string $section, array $sectionConfig)
    {
        if (! isset($sectionConfig['data_path']) || ! is_string($sectionConfig['data_path'])) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_data_path_config_error', [
                'section' => $section
            ]));
        }

        $handler = $sectionConfig['handler'] ?? null;

        if (empty($handler) || ! is_string($handler)) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_config_error', [
                'section' => $section
            ]));
        }

        if (!class_exists($handler)) {
            throw new \Exception(trans('laragrad/codifier::messages.errors.section_handler_class_not_exists', [
                'section' => $section,
                'class' => $handler
            ]));
        }
    }
}