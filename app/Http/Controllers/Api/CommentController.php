<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Comment;
use App\Http\Requests\Api\CreateComment;
use App\Http\Requests\Api\DeleteComment;
use App\RealWorld\Transformers\CommentTransformer;
use App\Services\CommentService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends ApiController
{
    /**
     * CommentController constructor.
     *
     * @param  CommentTransformer  $transformer
     */
    public function __construct(CommentTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except('index');
        $this->middleware('auth.api:optional')->only('index');
        $this->middleware('inactive.user')->only(['store']);
    }

    /**
     * Get all the comments of the article given by its slug.
     *
     * @param  Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Article $article)
    {
        $comments = $article->comments()->get();

        return $this->respondWithTransformer($comments);
    }

    /**
     * Add a comment to the article given by its slug and return the comment if successful.
     *
     * @param  CreateComment  $request
     * @param  Article  $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateComment $request, Article $article)
    {
        DB::beginTransaction();
        try {
            $comment = app(CommentService::class)->storeAndReturnComment(auth()->user(), $article->id,
                $request->input('comment.body'));

            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
            Log::error("Error on storing article for article with id {$article->id} with this error :".$exception->getMessage());
            return $this->respondInternalError();
        }

        return $this->respondWithTransformer($comment);
    }

    /**
     * Delete the comment given by its id.
     *
     * @param  DeleteComment  $request
     * @param $article
     * @param  Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteComment $request, $article, Comment $comment)
    {
        $comment->delete();

        return $this->respondSuccess();
    }
}
