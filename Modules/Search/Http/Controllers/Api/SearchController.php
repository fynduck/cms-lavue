<?php

namespace Modules\Search\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Search\Entities\Search;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function searchData()
    {
        $searchData = Search::get(['query', 'count']);

        $data['labels'] = $searchData->pluck('query');
        $backgroundColor = [];
        for ($i = 0; $i <= $searchData->count(); $i++)
            $backgroundColor[] = $this->generateRandomColor();

        $data['datasets'] = [
            'backgroundColor' => $backgroundColor,
            'data'            => $searchData->pluck('count')
        ];

        return $data;
    }

    private function generateRandomColor()
    {
        $color = '#';
        $colorHexLighter = ["9", "A", "B", "C", "D", "E", "F"];

        for ($x = 0; $x < 6; $x++)
            $color .= $colorHexLighter[array_rand($colorHexLighter, 1)];

        return substr($color, 0, 7);
    }
}
