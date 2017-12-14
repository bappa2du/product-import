<?php

namespace BigBridge\ProductImport\Model\Resource\Resolver;

/**
 * @author Patrick van Bergen
 */
class NameToUrlKeyConverter
{
    public function createUrlKeyFromName(string $name)
    {
        $key = $name;
        $key = strtolower($key);
        $key = iconv('UTF-8', 'ASCII//TRANSLIT', $key);
        $key = preg_replace("/[^a-z0-9]/", "-", $key);
        $key = preg_replace("/-{2,}/", "-", $key);
        $key = trim($key, '-');

        return $key;
    }
}