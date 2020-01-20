<?php

namespace Xpressengine\Plugins\XeBlog\Models;

use Xpressengine\Database\Eloquent\DynamicModel;

class BlogSlug extends DynamicModel
{
    protected $table = 'blog_slug';

    public $timestamps = false;

    protected $fillable = ['target_id', 'instance_id', 'slug', 'title'];

    public function blog()
    {
        return $this->hasOne(Blog::class, 'id', 'target_id');
    }
}
