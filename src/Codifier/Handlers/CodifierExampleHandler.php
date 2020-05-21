<?php
namespace Laragrad\Codifier\Handlers;

use Laragrad\Codifier\Handlers\AbstractHandler;

class CodifierExampleHandler extends AbstractHandler
{

    /**
     *
     * @param string $sectionConfig
     * @param string $locale
     * @return array
     */
    public static function load(string $sectionConfig, string $locale)
    {
        $data = config($sectionConfig['data_path'], []);

        $baseTransPath = $sectionConfig['trans_base_path']; // @todo

        foreach ($data as $key => &$value) {

            self::transField($key, $value, 'title', $baseTransPath, $locale);
            self::transField($key, $value, 'desc', $baseTransPath, $locale);
        }

        return $data;
    }
}