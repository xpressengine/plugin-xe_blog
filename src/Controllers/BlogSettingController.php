<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Category\CategoryHandler;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Plugin;

class BlogSettingController extends Controller
{
    /** @var BlogHandler $blogHandler */
    protected $blogHandler;

    /** @var CategoryHandler $categoryHandler */
    protected $categoryHandler;

    /** @var BlogConfigHandler $configHandler */
    protected $configHandler;

    public function __construct(BlogHandler $blogHandler, CategoryHandler $categoryHandler, BlogConfigHandler $configHandler)
    {
        $this->blogHandler = $blogHandler;
        $this->categoryHandler = $categoryHandler;
        $this->configHandler = $configHandler;

        XePresenter::share('metaDataHandler', new BlogMetaDataHandler());
    }

    public function blogs(Request $request)
    {
        $tempAttributes = $request->all();
        if (isset($tempAttributes['instanceId']) === false) {
            $tempAttributes['instanceId'] = Plugin::getId();
        }
        $blogs = $this->blogHandler->getItems($tempAttributes);

        return XePresenter::make('xe_blog::views.setting.blogs', compact('blogs'));
    }

    public function editSetting(Request $request)
    {
        return XePresenter::make('xe_blog::views.setting.setting');
    }

    public function storeSetting(Request $request)
    {

    }

    public function storeTaxonomy(Request $request)
    {
        $taxonomyAttribute = $request->except('_token');

        \XeDB::beginTransaction();
        try {
            $taxonomyItem = $this->categoryHandler->createCate($taxonomyAttribute);

            $blogConfig = $this->configHandler->getBlogConfig()->getPureAll();
            $blogConfig['taxonomy'][] = $taxonomyItem->id;
            $this->configHandler->putConfig($blogConfig);
        } catch (\Exception $e) {
            \XeDB::rollback();

            throw $e;
        }
        \XeDB::commit();

        return redirect()->route('manage.category.show', ['id' => $taxonomyItem->id]);
    }

    public function connectTaxonomySetting(Request $request)
    {
        $taxonomyId = $request->segment(count($request->segments()));

        return redirect()->route('manage.category.show', ['id' => $taxonomyId]);
    }
}
