<?php

namespace Xpressengine\Plugins\Post\Components\Widgets\PostWidget;

use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Plugin;
use Xpressengine\Widget\AbstractWidget;

class PostWidget extends AbstractWidget
{
    protected static $path = 'post/src/Components/Widgets/PostWidget';

    public function render()
    {
        /** @var PostHandler $postHandler */
        $postHandler = app('xe.post.handler');

        $posts = $postHandler->getItems(['instanceId' => Plugin::getId()]);

        return $this->renderSkin(['posts' => $posts]);
    }

    public function renderSetting(array $args = [])
    {
        return \View::make(sprintf('%s/views/setting', static::$path));
    }
}
