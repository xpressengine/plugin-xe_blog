<?php

namespace Xpressengine\Plugins\Post\Handlers;

use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\Post\Components\Modules\PostModule\PostModule;
use Xpressengine\Plugins\Post\Models\Post;

class PostHandler extends DocumentHandler
{
    protected $model = Post::class;

    public function store($attributes, $instanceId)
    {
        $attributes['instance_id'] = $instanceId;
        $attributes['type'] = PostModule::getId();

        return parent::add($attributes);
    }

    public function getItems($attributes)
    {
        $model = Post::division($attributes['instanceId']);
        $query = $model->where('instance_id', $attributes['instanceId']);
        $query = $query->visible();

        return $query->get();
    }

    public function update($post, $inputs)
    {
        $attributes = $post->getAttributes();

        foreach ($inputs as $name => $value) {
            if (array_key_exists($name, $attributes)) {
                $post->{$name} = $value;
            }
        }

        return parent::put($post);
    }

    public function trashPost($post)
    {
        $post->delete();
    }

    public function dropPost($post)
    {
        $post->forceDelete();
    }
}
