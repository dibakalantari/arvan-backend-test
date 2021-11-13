<?php

namespace App\Services;

use App\Article;
use App\Events\ArticleStored;
use App\User;

class ArticleService
{
    public function store(User $user,array $data): Article
    {
        /** @var Article $article */
        $article = $user->articles()->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'body' => $data['body'],
        ]);

        (new TagService())->attachArticleTags($article, $data['tags']);

        event(new ArticleStored($article));

        return $article;
    }
}