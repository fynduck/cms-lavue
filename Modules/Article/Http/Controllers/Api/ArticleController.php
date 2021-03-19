<?php

namespace Modules\Article\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleSettings;
use Modules\Article\Http\Requests\SizeValidate;
use Modules\Article\Services\ArticleService;
use Modules\Article\Http\Requests\ArticleValidate;
use Modules\Article\Transformers\ArticleFormResource;
use Modules\Article\Transformers\ArticleListResource;
use Modules\Language\Entities\Language;

class ArticleController extends AdminController
{

    protected $types;

    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->middleware('admin');

        $this->articleService = $articleService;

        foreach (Article::getTypes() as $key => $type) {
            $this->types[] = [
                'value' => $key,
                'text'  => $type,
            ];
        }
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $articles = Article::leftJoin('article_trans', 'articles.id', '=', 'article_trans.article_id')
            ->filter($request)
            ->orderBy('priority')
            ->orderBy('updated_at', 'DESC')->paginate(25);

        $additional = [
            'languages' => Language::whereActive(1)->pluck('name', 'id'),
            'settings'  => $this->articleService->settings()
        ];

        return ArticleListResource::collection($articles)->additional($additional);
    }

    /**
     * Store a newly created resource in storage.
     * @param ArticleValidate $request
     * @return bool
     * @throws \Exception
     */
    public function store(ArticleValidate $request)
    {
        /**
         * Save image(s)
         */
        $nameImages = $this->articleService->saveImages($request);
        /**
         * Save article
         */
        DB::beginTransaction();
        $article = $this->articleService->addUpdate($request, $nameImages);

        $this->articleService->addUpdateTrans($article->id, $request->get('items'));
        DB::commit();

        return true;
    }

    /**
     * @param $id
     * @return ArticleFormResource
     */
    public function show($id)
    {
        $item = Article::find($id);

        if (!$item) {
            $item = new Article();
        }

        $additional = [
            'types' => $this->types
        ];

        return (new ArticleFormResource($item))->additional($additional);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ArticleValidate $request
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function update(ArticleValidate $request, $id)
    {
        /**
         * Save image(s)
         */
        $nameImages = $this->articleService->saveImages($request);

        /**
         * Save article
         */
        DB::beginTransaction();
        $article = $this->articleService->addUpdate($request, $nameImages, $id);

        $this->articleService->addUpdateTrans($article->id, $request->get('items'));
        DB::commit();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param Article $article
     * @return bool
     * @throws \Exception
     */
    public function destroy(Request $request, Article $article)
    {
        if ($request->get('image')) {
            $this->articleService->deleteImages($article->image);
            $this->articleService->deleteOriginalImage($article->image);
            $article->image = null;
            $article->save();
        } else {
            return $article->delete();
        }

        return true;
    }

    /**
     * Save menus settings
     * @param SizeValidate $request
     * @return bool
     */
    public function saveSettings(SizeValidate $request): bool
    {
        $data = $this->articleService->prepareSizeSettingsToSave($request);

        ArticleSettings::updateOrCreate(['name' => 'sizes'], ['data' => $data]);

        return true;
    }
}