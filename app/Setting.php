<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name','value'];

    const REGISTERED_USER_CHARGE = 'registered_user_charge';
    const ARTICLE_FEE = 'article_fee';
    const FREE_COMMENTS_COUNT = 'free_comments_count';
    const COMMENT_FEE = 'comment_fee';
    const RECHARGE_NEEDED_CREDIT = 'recharge_needed_credit';
    const WAITING_HOURS_BEFORE_DELETING_USER = 'waiting_hours_before_deleting_user';

    public static $names = [
        self::REGISTERED_USER_CHARGE,
        self::ARTICLE_FEE,
        self::FREE_COMMENTS_COUNT,
        self::COMMENT_FEE,
        self::RECHARGE_NEEDED_CREDIT,
        self::WAITING_HOURS_BEFORE_DELETING_USER,
    ];
}
