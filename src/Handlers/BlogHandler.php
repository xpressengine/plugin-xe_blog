<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Plugin;

class BlogHandler extends DocumentHandler implements Searchable
{
    protected $model = Blog::class;

    public function store($attributes, $instanceId)
    {
        $attributes['instance_id'] = $instanceId;
        $attributes['type'] = Plugin::getId();

        return parent::add($attributes);
    }

    public function getItems($query, $attributes)
    {
        if (isset($attributes['force']) === true && $attributes['force'] === true) {
            $query = $query->visible();
        }

        if (isset($attributes['title']) === true) {
            $query = $query->where('title', 'like', '%' . $attributes['title']);
        }

        return $query;
    }

    public function update($blog, $inputs)
    {
        $attributes = $blog->getAttributes();

        foreach ($inputs as $name => $value) {
            if (array_key_exists($name, $attributes)) {
                $blog->{$name} = $value;
            }
        }

        return parent::put($blog);
    }

    public function trashBlog($blog)
    {
        $blog->delete();
    }

    public function dropBlog($blog)
    {
        $blog->forceDelete();
    }
}
