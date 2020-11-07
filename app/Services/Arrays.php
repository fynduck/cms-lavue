<?php

namespace App\Services;

class Arrays
{

    /**
     * set two keys
     * @param array $items
     * @param string $key1
     * @param string $key2
     * @return array
     * @internal param array $items
     * @internal param string $key1
     * @internal param string $key2
     */
    static function setTwoKeys($items, $key1, $key2)
    {
        $results = [];
        if ($items) {
            foreach ($items as $item)
                $results[$item->{$key1}][$item->{$key2}] = $item;
        }

        return $results;
    }
}
