<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use App\Http\Sections\DynamicFieldSection;
use App\Http\Sections\SkinSection;
use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Plugins\XeBlog\Services\BlogService;

class BlogSettingController extends Controller
{
    /** @var BlogService $blogService */
    protected $blogService;

    /** @var BlogConfigHandler $configHandler */
    protected $configHandler;

    /** @var BlogTaxonomyHandler $taxonomyHandler */
    protected $taxonomyHandler;

    /** @var BlogPermissionHandler $blogPermissionHandler */
    protected $blogPermissionHandler;

    public function __construct(
        BlogService $blogService,
        BlogConfigHandler $configHandler,
        BlogTaxonomyHandler $taxonomyHandler,
        BlogPermissionHandler $blogPermissionHandler
    ) {
        $this->blogService = $blogService;
        $this->configHandler = $configHandler;
        $this->taxonomyHandler = $taxonomyHandler;
        $this->blogPermissionHandler = $blogPermissionHandler;

        XePresenter::share('metaDataHandler', new BlogMetaDataHandler());
        XePresenter::share('taxonomyHandler', $taxonomyHandler);
    }

    public function blogs(Request $request)
    {
        $blogs = $this->blogService->getItems($request->all());
        $taxonomies = $this->taxonomyHandler->getTaxonomies();

        return XePresenter::make('xe_blog::views.setting.blogs', compact('blogs', 'taxonomies'));
    }

    public function editSetting(Request $request, $type = 'config')
    {
        $config = $this->configHandler->getBlogConfig();
        $skinSection = new SkinSection('blog/show');

        $dynamicFieldSection = new DynamicFieldSection(
            'documents_' . Plugin::getId(),
            \XeDB::connection(),
            true
        );

        $perms = $this->blogPermissionHandler->getPerms();

        return XePresenter::make(
            'xe_blog::views.setting.setting',
            compact('type', 'skinSection', 'config', 'dynamicFieldSection', 'perms')
        );
    }

    public function updateSetting(Request $request)
    {
        $this->configHandler->updateBlogConfig($request->except('_token'));

        return redirect()->back();
    }

    public function storeTaxonomy(Request $request)
    {
        $taxonomyAttribute = $request->except('_token');

        $taxonomyItem = $this->taxonomyHandler->createTaxonomy($taxonomyAttribute);

        return redirect()->route('manage.category.show', ['id' => $taxonomyItem->id]);
    }

    public function updatePermission(Request $request, BlogPermissionHandler $blogPermissionHandler)
    {
        $blogPermissionHandler->updatePermission($request);

        return redirect()->back();
    }

    public function connectTaxonomySetting(Request $request)
    {
        $taxonomyId = $request->segment(count($request->segments()));

        return redirect()->route('manage.category.show', ['id' => $taxonomyId]);
    }
}
