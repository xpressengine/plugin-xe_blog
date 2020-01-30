<?php

namespace Xpressengine\Plugins\XeBlog\Components\Skins\Widget\BlogWidgetSkin;

use Xpressengine\Permission\Instance;
use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;
use Xpressengine\Skin\GenericSkin;

class BlogWidgetSkin extends GenericSkin
{
    protected static $path = 'xe_blog/src/Components/Skins/Widget/BlogWidgetSkin';

    public function render()
    {
        /** @var BlogPermissionHandler $blogPermissionHandler */
        $blogPermissionHandler = app('xe.blog.permissionHandler');

        $this->data['isCreatable'] = \Gate::allows(BlogPermissionHandler::ACTION_CREATE, new Instance($blogPermissionHandler->getPermissionName()));

        return parent::render();
    }
}
