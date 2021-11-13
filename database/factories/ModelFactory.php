<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Article;
use App\Transaction;
use App\User;

$factory->define(App\User::class, function (\Faker\Generator $faker) {

    return [
        'username' => str_replace('.', '', $faker->unique()->userName),
        'email' => $faker->unique()->safeEmail,
        'password' => 'secret',
        'bio' => $faker->sentence,
        'status' => \App\User::ACTIVE_STATUS,
        'image' => 'https://cdn.worldvectorlogo.com/logos/laravel.svg',
    ];
});

$factory->define(App\Article::class, function (\Faker\Generator $faker) {

    static $reduce = 999;

    return [
        'title' => $faker->sentence,
        'description' => $faker->sentence(10),
        'body' => $faker->paragraphs($faker->numberBetween(1, 3), true),
        'user_id' => factory(\App\User::class)->create()->id,
        'created_at' => \Carbon\Carbon::now()->subSeconds($reduce--),
    ];
});

$factory->define(App\Comment::class, function (\Faker\Generator $faker) {

    static $users;
    static $reduce = 999;

    $users = $users ?: \App\User::all();

    return [
        'body' => $faker->paragraph($faker->numberBetween(1, 5)),
        'user_id' => $users->random()->id,
        'article_id' => factory(Article::class)->create()->id,
        'created_at' => \Carbon\Carbon::now()->subSeconds($reduce--),
    ];
});

$factory->define(App\Tag::class, function (\Faker\Generator $faker) {

    return [
        'name' => $faker->unique()->word,
    ];
});

$factory->define(App\Transaction::class, function (\Faker\Generator $faker) {

    return [
        'amount' => $faker->randomNumber(5),
        'user_id' => factory(User::class)->create()->id,
        'increment' => false,
    ];
});

$factory->define(App\Factor::class, function (\Faker\Generator $faker) {

    return [
        'transaction_id' => factory(Transaction::class)->create()->id,
        'purchasable_id' => factory(Article::class)->create()->id,
        'purchasable_type' => Article::class,
    ];
});
