<?php
namespace Laragrad\Codifier;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class CodifierService
{

    protected $sections = [];

    protected $config;

    /**
     */
    public function __construct()
    {
        $this->config = config('laragrad.codifier.config');
    }

    /**
     * Get handled section data
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