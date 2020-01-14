<?php

namespace Xpressengine\Plugins\Post\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Category\CategoryHandler;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\PostConfigHandler;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;
use Xpressengine\Plugins\Post\Plugin;

class PostSettingController extends Controller
{
    /** @var PostHandler $postHandler */
    protected $postHandler;

    /** @var CategoryHandler $categoryHandler */
    protected $categoryHandler;

    /** @var PostConfigHandler $configHandler */
    protected $configHandler;

    public function __construct(PostHandler $postHandler, CategoryHandler $categoryHandler, PostConfigHandler $configHandler)
    {
        $this->postHandler = $postHandler;
        $this->categoryHandler = $categoryHandler;
        $this->configHandler = $configHandler;

        XePresenter::share('metaDataHandler', new PostMetaDataHandler());
    }

    public function posts(Request $request)
    {
        $tempAttributes = $request->all();
        if (isset($tempAttributes['instanceId']) === false) {
            $tempAttributes['instanceId'] = Plugin::getId();
        }
        $posts = $this->postHandler->getItems($tempAttributes);

        return XePresenter::make('post::views.setting.posts', compact('posts'));
    }

    public function editSetting(Request $request)
    {
        return XePresenter::make('post::views.setting.setting');
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
