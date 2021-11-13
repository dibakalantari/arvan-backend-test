<?php

namespace App\Services;

use App\Article;
use App\Comment;
use App\Setting;
use App\Transaction;
use App\User;

class CommentService
{
    public function store(Article $article, array $data): Comment
    {
        /** @var Comment $comment */
        $comment = $article->comments()->create([
            'body' => $data['body'],
            'user_id' => $data['user_id'],
        ]);

        $user = User::find($data['user_id']);

        $this->checkIfCommentNeedsPayment($user, $comment);

        return $comment;
    }

    public function checkIfCommentNeedsPayment(User $user, Comment $comment): void
    {
        $freeCommentCount = (new SettingService())->getSettingValue(Setting::FREE_COMMENTS_COUNT);

        $userCommentsCount = $user->comments()->lockForUpdate()->count();

        if ($userCommentsCount <= $freeCommentCount) {
            return;
        }

        $commentFee = (new SettingService())->getSettingValue(Setting::COMMENT_FEE);

        (new PaymentService())->create($user,[
            'amount' => $commentFee,
            'purchasable_id' => $comment->id,
            'purchasable_type' => Comment::class,
        ]);
    }
}