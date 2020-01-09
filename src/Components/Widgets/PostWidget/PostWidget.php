<?php

namespace Xpressengine\Plugins\Post\Components\Widgets\PostWidget;

use Xpressengine\Widget\AbstractWidget;

class PostWidget extends AbstractWidget
{
    protected static $path = 'post/src/Components/Widgets/PostWidget';

    public function render()
    {
        // TODO: Implement render() method.
    }

    public function renderSetting(array $args = [])
    {
        return \View::make(sprintf('%s/views/setting', static::$path));
    }
}
