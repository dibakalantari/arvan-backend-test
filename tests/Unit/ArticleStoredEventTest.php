<?php

namespace Tests\Unit;

use App\Article;
use App\Events\ArticleStored;
use App\Factor;
use App\Services\SettingService;
use App\Setting;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ArticleStoredEventTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function firing_article_stored_event_decreases_user_credit()
    {
        $user = factory(User::class)->create([
            'credit' => 20000
        ]);

        $article = factory(Article::class)->create([
            'user_id' => $user->id
        ]);

        event(new ArticleStored($article));

        $articleFee = (new SettingService())->getSettingValue(Setting::ARTICLE_FEE);

        $this->assertEquals($user->fresh()->credit,$user->credit - $articleFee);
    }

    /** @test */
    public function firing_article_stored_event_create_new_transaction()
    {
        $user = factory(User::class)->create([
            'credit' => 20000
        ]);

        $article = factory(Article::class)->create([
            'user_id' => $user->id
        ]);

        event(new ArticleStored($article));

        $articleFee = (new SettingService())->getSettingValue(Setting::ARTICLE_FEE);

        $this->assertDatabaseHas((new Transaction())->getTable(),[
           'amount' => $articleFee,
           'user_id' => $user->id,
           'increment' => false,
        ]);
    }

    /** @test */
    public function firing_article_stored_event_create_new_factor()
    {
        $user = factory(User::class)->create([
            'credit' => 20000
        ]);

        $article = factory(Article::class)->create([
            'user_id' => $user->id
        ]);

        event(new ArticleStored($article));

        $articleFee = (new SettingService())->getSettingValue(Setting::ARTICLE_FEE);

        $transaction = Transaction::query()->where([
            'user_id' => $user->id,
            'amount' => $articleFee,
            'increment' => false,
        ])->latest()->first();

        $this->assertDatabaseHas((new Factor())->getTable(),[
            'transaction_id' => $transaction->id,
            'purchasable_id' => $article->id,
            'purchasable_type' => Article::class,
        ]);
    }
}
