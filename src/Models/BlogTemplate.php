<?php

namespace Xpressengine\Plugins\XeBlog\Models;

use Xpressengine\Database\Eloquent\DynamicModel;

class BlogTemplate extends DynamicModel
{
    protected $table = 'blog_templates';

    protected $fillable = ['user_id', 'title', 'content'];
}
