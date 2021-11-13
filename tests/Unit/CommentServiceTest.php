<?php

namespace Tests\Unit;

use App\Article;
use App\Comment;
use App\Services\CommentService;
use App\Services\SettingService;
use App\Setting;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    /** @test */
    public function store_method_adds_new_comment_to_article()
    {
        $article = factory(Article::class)->create();

        (new CommentService())->store($article,[
            'body' => $article->body,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas((new Comment())->getTable(),[
            'article_id' => $article->id,
            'body' => $article->body,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_shouldnt_pay_for_the_first_comments()
    {
        $article = factory(Article::class)->create();

        (new CommentService())->store($article,[
            'body' => $article->body,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($this->user->credit,$this->user->fresh()->credit);
    }

    /** @test */
    public function user_should_pay_for_comments_after_his_free_comments_are_finished()
    {
        /** @var int $commentFee */
        $commentFee = (new SettingService())->getSettingValue(Setting::COMMENT_FEE);

        $this->user->update([
            'credit' => $commentFee + 10000
        ]);

        /** @var int $freeCommentCount */
        $freeCommentCount = (new SettingService())->getSettingValue(Setting::FREE_COMMENTS_COUNT);

        $articles = factory(Article::class,$freeCommentCount + 1)->create();

        foreach($articles as $article) {
            (new CommentService())->store($article,[
                'body' => $article->body,
                'user_id' => $this->user->id,
            ]);
        }

        $this->assertEquals(10000,$this->user->fresh()->credit);
    }
}
