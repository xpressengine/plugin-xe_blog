<?php

namespace Xpressengine\Plugins\Post\Handlers;

use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\Post\Components\Modules\PostModule\PostModule;
use Xpressengine\Plugins\Post\Models\Post;

class Handler extends DocumentHandler
{
    protected $model = Post::class;

    public function store($attributes, $instanceId)
    {
        if (isset($attributes['user_id']) === false) {
            $attributes['user_id'] = auth()->user()->getId();
        }

        if (isset($attributes['writer']) === false) {
            $attributes['writer'] = auth()->user()->getDisplayName();
        }

        $attributes['instance_id'] = $instanceId;
        $attributes['type'] = PostModule::getId();

        parent::add($attributes);
    }

    public function getItems($attributes)
    {
        $model = Post::division($attributes['instanceId']);
        $query = $model->where('instance_id', $attributes['instanceId']);

        return $query->get();
    }
}
