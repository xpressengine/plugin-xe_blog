<?php

namespace Xpressengine\Plugins\Post\Models;

use Xpressengine\Category\Models\CategoryItem;
use Xpressengine\Database\Eloquent\DynamicModel;

class BlogTaxonomy extends DynamicModel
{
    protected $table = 'blog_taxonomy';

    public $timestamps = false;

    protected $fillable = ['blog_id', 'taxonomy_item_id'];

    public function taxonomyItem()
    {
        return $this->belongsTo(CategoryItem::class, 'taxonomy_item_id');
    }
}
