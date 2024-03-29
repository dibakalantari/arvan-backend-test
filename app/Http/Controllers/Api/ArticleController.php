<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Events\ArticleStored;
use App\Http\Requests\Api\CreateArticle;
use App\Http\Requests\Api\DeleteArticle;
use App\Http\Requests\Api\UpdateArticle;
use App\RealWorld\Filters\ArticleFilter;
use App\RealWorld\Paginate\Paginate;
use App\RealWorld\Transformers\ArticleTransformer;
use App\Services\ArticleService;
use App\Tag;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleController extends ApiController
{
    /**
     * ArticleController constructor.
     *
     * @param  ArticleTransformer  $transformer
     */
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except(['index', 'show']);
        $this->middleware('auth.api:optional')->only(['index', 'show']);
        $this->middleware('inactive.user')->only(['store']);
    }

    /**
     * Get all the articles.
     *
     * @param  ArticleFilter  $filter
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ArticleFilter $filter)
    {
        $articles = new Paginate(Article::loadRelations()->filter($filter));

        return $this->respondWithPagination($articles);
    }

    /**
     * Create a new article and return the article if successful.
     *
     * @param  CreateArticle  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateArticle $request)
    {
        /** @var User $user */
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $article = (new ArticleService())->store($user,[
                'title' => $request->input('article.title'),
                'description' => $request->input('article.description'),
                'body' => $request->input('article.body'),
                'tags' => $request->input('article.tagList') ?? []
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error("Error on storing article for user with id {$user->id} with this error :".$exception->getMessage());
            return $this->respondInternalError();
        }

        return $this->respondWithTransformer($article);
    }

    /**
     * Get the article given by its slug.
     *
     * @param  Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Article $article)
    {
        return $this->respondWithTransformer($article);
    }

    /**
     * Update the article given by its slug and return the article if successful.
     *
     * @param  UpdateArticle  $request
     * @param  Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateArticle $request, Article $article)
    {
        if ($request->has('article')) {
            $article->update($request->get('article'));
        }

        return $this->respondWithTransformer($article);
    }

    /**
     * Delete the article given by its slug.
     *
     * @param  DeleteArticle  $request
     * @param  Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteArticle $request, Article $article)
    {
        $article->delete();

        return $this->respondSuccess();
    }
}
