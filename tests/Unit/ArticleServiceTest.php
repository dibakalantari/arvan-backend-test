<?php

namespace Tests\Unit;

use App\Article;
use App\Events\ArticleStored;
use App\Services\ArticleService;
use App\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    /** @test */
    public function store_method_adds_new_article_and_its_tags_to_database_and_fire_ArticleStored_event()
    {
        Event::fake();

        $article = factory(Article::class)->make();
        $tags = factory(Tag::class,5)->create();

        (new ArticleService())->store($this->user,[
            'title' => $article->title,
            'description' => $article->description,
            'body' => $article->body,
            'tags' => $tags->pluck('name')->toArray(),
        ]);

        Event::assertDispatched(ArticleStored::class);

        $this->assertDatabaseHas((new Article())->getTable(),[
           'title' => $article->title,
           'description' => $article->description,
           'body' => $article->body,
        ]);

        $article = $this->user->articles()->where([
            'title' => $article->title,
            'description' => $article->description,
            'body' => $article->body,
        ])->latest()->first();

        $this->assertDatabaseHas('article_tag',[
            'article_id' => $article->id,
            'tag_id' => $tags[0]->id,
        ]);
    }
}
