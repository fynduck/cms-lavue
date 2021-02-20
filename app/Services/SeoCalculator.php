<?php

/**
 * Created by PhpStorm.
 * User: stass
 * Date: 24.05.2016
 * Time: 22:56
 * rename to calculatorSeo
 */

namespace App\Services;

class SeoCalculator
{

    /**
     * @param $category
     * @param $nrLang
     * @return mixed
     */
    static function calcSeo($category, $nrLang)
    {
        $fieldsCompleted = 0;
        if ($category) {
            foreach ($category as $item) {
                if ($item->meta_title != '') {
                    $fieldsCompleted++;
                }
                if ($item->meta_description != '') {
                    $fieldsCompleted++;
                }
                if ($item->meta_keywords != '') {
                    $fieldsCompleted++;
                }
            }
        }

        return intval((100 * $fieldsCompleted) / ($nrLang * 3));
    }

    static function collectionCalcSeo($collection)
    {
        $fieldsCompleted = 0;
        if ($collection->meta_title != '') {
            $fieldsCompleted++;
        }
        if ($collection->meta_description != '') {
            $fieldsCompleted++;
        }
        if ($collection->meta_keywords != '') {
            $fieldsCompleted++;
        }

        return intval((100 * $fieldsCompleted) / 3);
    }
}
