<?php

namespace App\Services;

use App\Article;
use App\Comment;
use App\Setting;
use App\User;

class CommentService
{
    public function storeAndReturnComment(User $user,int $article_id,string $body) // TODO: refactor this to DTO
    {
        $comment = Comment::query()->create([
            'article_id' => $article_id,
            'body' => $body,
            'user_id' => $user->id,
        ]);

        $freeCommentCount = app(SettingService::class)->getSettingValue(Setting::FREE_COMMENTS_COUNT);

        if($user->comments->count() > $freeCommentCount) {
            $commentFee = app(SettingService::class)->getSettingValue(Setting::COMMENT_FEE);

            app(UserService::class)->decreaseUserBalance($user,$commentFee);

            $transaction = app(TransactionService::class)->createAndReturnTransaction($user->id, $commentFee);

            app(FactorService::class)->createFactor($transaction->id, $comment->id,Comment::class);
        }

        return $comment;
    }
}