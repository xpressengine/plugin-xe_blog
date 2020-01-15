<?php

namespace Xpressengine\Plugins\XeBlog\Models;

use Xpressengine\Database\Eloquent\DynamicModel;

class BlogFavorite extends DynamicModel
{
    protected $table = 'blog_favorites';

    protected $fillable = ['blog_id', 'user_id'];
}
