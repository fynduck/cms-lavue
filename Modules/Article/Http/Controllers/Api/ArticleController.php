<?php

namespace Modules\Article\Http\Controllers\Api;

use App\Http\Controllers\AdminController;
use Fynduck\FilesUpload\PrepareFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Modules\Article\Entities\Article;
use Modules\Article\Services\ArticleService;
use Modules\Article\Http\Requests\ArticleValidate;
use Modules\Article\Transformers\ArticleFormResource;
use Modules\Article\Transformers\ArticleListResource;

class ArticleController extends AdminController
{

    protected $types;

    public function __construct()
    {
        $this->middleware('admin');

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

        return ArticleListResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     * @param ArticleValidate $request
     * @param ArticleService $articleService
     * @return bool
     * @throws \Exception
     */
    public function store(ArticleValidate $request, ArticleService $articleService)
    {
        /**
         * Save image(s)
         */
        $nameImages = $articleService->saveImages($request);
        /**
         * Save article
         */
        \DB::beginTransaction();
        $article = $articleService->addUpdate($request, $nameImages);

        $articleService->addUpdateTrans($article->id, $request->get('items'));
        \DB::commit();

        return true;
    }

    /**
     * @param $id
     * @return ArticleFormResource
     */
    public function show($id)
    {
        $item = Article::find($id);

        if (!$item)
            $item = new Article();

        $additional = [
            'types' => $this->types
        ];

        return (new ArticleFormResource($item))->additional($additional);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ArticleValidate $request
     * @param ArticleService $articleService
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function update(ArticleValidate $request, ArticleService $articleService, $id)
    {
        /**
         * Save image(s)
         */
        $nameImages = $articleService->saveImages($request);

        /**
         * Save article
         */
        \DB::beginTransaction();
        $article = $articleService->addUpdate($request, $nameImages, $id);

        $articleService->addUpdateTrans($article->id, $request->get('items'));
        \DB::commit();

        return true;
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $article = Article::find($id);
        if ($request->get('image')) {
            $checkIcon = explode('_', $request->get('image'))[0];
            if ($checkIcon == 'icon') {
                PrepareFile::deleteImages(Article::FOLDER_IMG, $article->icon, Article::getSizes());
                $article->icon = null;
            } else {
                PrepareFile::deleteImages(Article::FOLDER_IMG, $article->image, Article::getSizes());
                $article->image = null;
            }
            $article->save();
        } else {
            return $article->delete();
        }

        return true;
    }
}