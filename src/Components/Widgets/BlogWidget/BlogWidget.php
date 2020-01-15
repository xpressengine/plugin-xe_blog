<?php

namespace Xpressengine\Plugins\XeBlog\Components\Widgets\BlogWidget;

use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Widget\AbstractWidget;

class BlogWidget extends AbstractWidget
{
    protected static $path = 'xe_blog/src/Components/Widgets/BlogWidget';

    public function render()
    {
        /** @var BlogHandler $blogHandler */
        $blogHandler = app('xe.blog.handler');

        $blogs = $blogHandler->getItems(['instanceId' => Plugin::getId()]);

        return $this->renderSkin(['blogs' => $blogs]);
    }

    public function renderSetting(array $args = [])
    {
        return \View::make(sprintf('%s/views/setting', static::$path));
    }
}
