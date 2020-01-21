<?php

namespace Xpressengine\Plugins\XeBlog\Components\Widgets\BlogWidget;

use Route;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Widget\AbstractWidget;

class BlogWidget extends AbstractWidget
{
    const DEFAULT_PER_PAGE = 5;

    protected static $path = 'xe_blog/src/Components/Widgets/BlogWidget';

    public function render()
    {
        /** @var BlogService $blogService */
        $blogService = app('xe.blog.service');

        $metaDataHandler = new BlogMetaDataHandler();
        $taxonomyHandler = app('xe.blog.taxonomyHandler');
        $blogConfigHandler = app('xe.blog.configHandler');

        $widgetSetting = $this->setting();
        $perPage = self::DEFAULT_PER_PAGE;
        if (isset($widgetSetting['perPage']) === true && $widgetSetting['perPage'] !== '' && $widgetSetting['perPage'] !== null) {
            $perPage = $widgetSetting['perPage'];
        }

        $blogs = $blogService->getItems(['perPage' => $perPage]);
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
        return \View::make(sprintf('%s/views/setting', static::$path), ['config' => $args]);
    }
}
