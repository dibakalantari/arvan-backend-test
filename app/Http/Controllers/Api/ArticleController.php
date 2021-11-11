<?php

namespace App\Http\Controllers\Api;

use App\Events\ArticleStored;
use App\Tag;
use App\Article;
use App\RealWorld\Paginate\Paginate;
use App\RealWorld\Filters\ArticleFilter;
use App\Http\Requests\Api\CreateArticle;
use App\Http\Requests\Api\UpdateArticle;
use App\Http\Requests\Api\DeleteArticle;
use App\RealWorld\Transformers\ArticleTransformer;
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
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $article = $user->articles()->create([
                'title' => $request->input('article.title'),
                'description' => $request->input('article.description'),
                'body' => $request->input('article.body'),
            ]);

            $inputTags = $request->input('article.tagList');

            if ($inputTags && !empty($inputTags)) {
                $tags = array_map(function ($name) {
                    return Tag::firstOrCreate(['name' => $name])->id;
                }, $inputTags);

                $article->tags()->attach($tags);
            }

            event(new ArticleStored($article));
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            if($exception->getCode() == 500) //TODO refactor this
            {
                Log::error("Error on storing article for user with id {$user->id} with this error :".$exception->getMessage());
                return $this->respondInternalError();
            }

            return $this->respondError($exception->getMessage(), $exception->getCode());
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
