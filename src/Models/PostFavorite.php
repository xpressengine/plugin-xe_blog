<?php

namespace Xpressengine\Plugins\Post\Models;

use Xpressengine\Database\Eloquent\DynamicModel;

class PostFavorite extends DynamicModel
{
    protected $table = 'post_favorites';

    protected $fillable = ['post_id', 'user_id'];
}
