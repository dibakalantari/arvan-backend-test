<?php

namespace App\Services;

use App\Article;
use App\Tag;

class TagService
{
    public function attachArticleTags(Article $article, array $tagNames = []): void
    {
        if (empty($tagNames)) {
            return;
        }

        $tags = array_map(function ($name) {
            return Tag::query()->firstOrCreate(['name' => $name])->id;
        }, $tagNames);

        $article->tags()->attach($tags);
    }
}