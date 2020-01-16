<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\XeBlog\Interfaces\Jsonable;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Plugin;

class BlogHandler extends DocumentHandler implements Searchable, Jsonable
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

    public function getJsonData(Blog $blog)
    {
        return [
            'title' => $blog->title,
            'content' => $blog->content,
            'read_count' => $blog->read_count,
            'comment_count' => $blog->comment_count,
            'assent_count' => $blog->assent_count,
            'dissent_count' => $blog->dissent_count,
            'created_at' => $blog->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $blog->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function getTypeName()
    {
        return 'blog';
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
