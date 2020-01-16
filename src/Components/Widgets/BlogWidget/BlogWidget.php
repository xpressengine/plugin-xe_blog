<?php

namespace Xpressengine\Plugins\XeBlog\Components\Widgets\BlogWidget;

use Route;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Widget\AbstractWidget;

class BlogWidget extends AbstractWidget
{
    protected static $path = 'xe_blog/src/Components/Widgets/BlogWidget';

    public function render()
    {
        /** @var BlogService $blogService */
        $blogService = app('xe.blog.service');

        $metaDataHandler = new BlogMetaDataHandler();
        $taxonomyHandler = app('xe.blog.taxonomyHandler');
        $blogConfigHandler = app('xe.blog.configHandler');

        $blogs = $blogService->getItems([]);
        $blogConfig = $blogConfigHandler->getBlogConfig();
        $taxonomies = $taxonomyHandler->getTaxonomies();

        return $this->renderSkin([
            'blogs' => $blogs,
            'blogConfig' => $blogConfig,
            'metaDataHandler' => $metaDataHandler,
            'taxonomyHandler' => $taxonomyHandler,
            'taxonomies' => $taxonomies
        ]);
    }

    public function renderSetting(array $args = [])
    {
        return \View::make(sprintf('%s/views/setting', static::$path));
    }
}
