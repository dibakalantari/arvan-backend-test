<?php

namespace App\Listeners;

use App\Article;
use App\Events\ArticleStored;
use App\Services\FactorService;
use App\Services\SettingService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseArticle
{
    /**
     * Handle the event.
     *
     * @param  ArticleStored  $event
     * @return void
     */
    public function handle(ArticleStored $event)
    {
        $articleFee = app(SettingService::class)->getSettingValue(Setting::ARTICLE_FEE);

        app(UserService::class)->decreaseUserBalance($event->article->user, $articleFee);

        $transaction = app(TransactionService::class)->createAndReturnTransaction($event->article->user->id, $articleFee);

        app(FactorService::class)->createFactor($transaction->id, $event->article->id,Article::class);
    }
}
