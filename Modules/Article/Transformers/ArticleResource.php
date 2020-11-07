<?php

namespace Modules\Article\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Article\Entities\Article;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->generateDescription(),
            'imgObj'            => $this->imgObj(),
            'srcset'            => $this->srcset(),
            'link'              => $this->generateLink(),
            'date'              => $this->date,
            'show_date'         => $this->date->format('d.m.Y'),
            'date_to'           => $this->date_to,
            'promo_finish_date' => $this->date_to && $this->date_to->isFuture() ? $this->date_to->timestamp : null
        ];
    }

    private function imgObj($size = 'xs')
    {
        $data = [];
        if ($this->image) {
            $data = [
                'src'     => asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $this->image),
                'loading' => asset('storage/' . Article::FOLDER_IMG . '/50/' . $this->image)
            ];
        }

        return $data;
    }

    private function generateLink()
    {
        $params = [
            count(config('app.locales')) > 1 ? config('app.locale') : null,
            array_key_exists($this->type, cache('urls_pages_' . config('app.locale_id'))) ? cache('urls_pages_' . config('app.locale_id'))[$this->type] : '',
            $this->slug
        ];
        return route('pages', $params, false);
    }

    private function srcset()
    {
        $srcset = [];
        if ($this->image) {
            foreach (Article::getSizes() as $size => $sizes) {
                if ($size != 50)
                    $srcset[] = asset('storage/' . Article::FOLDER_IMG . '/' . $size . '/' . $this->image) . ' ' . $sizes['width'] . 'w';
            }
        }
        return $srcset;
    }

    private function getTz(Request $request)
    {
        $tz = config('app.timezone');
        if ($request->get('tz'))
            $tz = $request->get('tz');

        return $tz;
    }

    private function generateDescription()
    {
        $description = null;
        if ($this->short_desc)
            $description = $this->short_desc;
        else
            $description = $this->description;

        return html_entity_decode(\Str::limit(strip_tags($description), 160));
    }
}
