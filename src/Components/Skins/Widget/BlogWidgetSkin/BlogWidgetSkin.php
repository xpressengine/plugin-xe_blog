<?php

namespace Xpressengine\Plugins\XeBlog\Components\Skins\Widget\BlogWidgetSkin;

use Xpressengine\Permission\Instance;
use Xpressengine\Plugins\XeBlog\Components\Skins\Blog\BlogCommonSkin\BlogCommonSkin;
use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;
use Xpressengine\Skin\GenericSkin;

class BlogWidgetSkin extends GenericSkin
{
    protected static $path = 'xe_blog/src/Components/Skins/Widget/BlogWidgetSkin';

    public function render()
    {
        \XeFrontend::css(BlogCommonSkin::asset('css/bootstrap.css'))->load();
        \XeFrontend::css(BlogCommonSkin::asset('css/boldjournal-widget.css'))->load();
        \XeFrontend::css(self::asset('css/boldjournal-theme.css'))->load();
        
        /** @var BlogPermissionHandler $blogPermissionHandler */
        $blogPermissionHandler = app('xe.blog.permissionHandler');

        $this->data['isCreatable'] = \Gate::allows(BlogPermissionHandler::ACTION_CREATE, new Instance($blogPermissionHandler->getPermissionName()));

        return parent::render();
    }
}
