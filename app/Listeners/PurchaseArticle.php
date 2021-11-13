<?php

namespace App\Listeners;

use App\Article;
use App\Events\ArticleStored;
use App\Services\FactorService;
use App\Services\PaymentService;
use App\Services\SettingService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Setting;

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
        $articleFee = (new SettingService())->getSettingValue(Setting::ARTICLE_FEE);

        (new PaymentService())->create($event->article->user,[
            'amount' => $articleFee,
            'purchasable_id' => $event->article->id,
            'purchasable_type' => Article::class,
        ]);
    }
}
