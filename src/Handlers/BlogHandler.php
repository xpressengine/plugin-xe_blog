<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\XeBlog\Components\Modules\BlogModule\BlogModule;
use Xpressengine\Plugins\XeBlog\Models\Blog;

class BlogHandler extends DocumentHandler
{
    protected $model = Blog::class;

    public function store($attributes, $instanceId)
    {
        $attributes['instance_id'] = $instanceId;
        $attributes['type'] = BlogModule::getId();

        return parent::add($attributes);
    }

    public function getItems($attributes)
    {
        $model = Blog::division($attributes['instanceId']);
        $query = $model->where('instance_id', $attributes['instanceId']);
        $query = $query->visible();

        $query->with(['favorite' => function ($query) {
            $query->where('user_id', auth()->user()->getId());
        }]);

        return $query->get();
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
