<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use App\Http\Sections\DynamicFieldSection;
use App\Http\Sections\SkinSection;
use XePresenter;
use XeFrontend;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Support\Exceptions\InvalidArgumentHttpException;

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
        \XeFrontend::css(Plugin::asset('assets/css/admin-setting-xe-blog.css'))->load();
        \XeFrontend::css('assets/core/xe-ui/css/xe-ui-without-base.css')->load();

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
        $taxonomies = $this->taxonomyHandler->getTaxonomies();

        return XePresenter::make(
            'xe_blog::views.setting.setting',
            compact('type', 'skinSection', 'config', 'dynamicFieldSection', 'perms', 'taxonomies')
        );
    }

    public function editTaxonomyConfig($taxonomyId)
    {
        $taxonomy = \XeCategory::cates()->find($taxonomyId);

        if ($taxonomy === null) {
            throw new InvalidArgumentHttpException;
        }

        $taxonomyConfig = $this->taxonomyHandler->getTaxonomyInstanceConfig($taxonomyId);

        XeFrontend::css(
            [
                '/assets/core/lang/langEditorBox.css',
                '/assets/core/xe-ui-component/xe-ui-component.css'
            ]
        )->load();

        XeFrontend::js(
            [
                '/assets/core/lang/langEditorBox.bundle.js'
            ]
        )->appendTo('head')->load();

        XeFrontend::js('/assets/core/common/js/xe.tree.js')->appendTo('body')->load();
        XeFrontend::js('/assets/core/category/Category.js')->appendTo('body')->load();

        XeFrontend::translation([
            'xe::required',
            'xe::addItem',
            'xe::create',
            'xe::createChild',
            'xe::edit',
            'xe::unknown',
            'xe::word',
            'xe::description',
            'xe::save',
            'xe::delete',
            'xe::close',
            'xe::subCategoryDestroy',
            'xe::confirmDelete',
        ]);

        return XePresenter::make('xe_blog::views.setting.edit_taxonomy', compact('taxonomy', 'taxonomyConfig'));
    }

    public function updateTaxonomyConfig(Request $request)
    {
        $taxonomyConfig = $this->taxonomyHandler->getTaxonomyInstanceConfig($request->get('taxonomyId'));

        $this->taxonomyHandler->updateTaxonomyInstanceConfig($taxonomyConfig, $request->except(['_token', 'taxonomyId']));

        return redirect()->back();
    }

    public function deleteTaxonomy(Request $request)
    {
        $this->taxonomyHandler->deleteTaxonomy($request->get('taxonomyId'));

        return redirect()->route('blog.setting.setting', ['type' => 'taxonomy']);
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
