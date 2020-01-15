<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Plugin;

class BlogSettingController extends Controller
{
    /** @var BlogHandler $blogHandler */
    protected $blogHandler;

    /** @var BlogConfigHandler $configHandler */
    protected $configHandler;

    /** @var BlogTaxonomyHandler $taxonomyHandler */
    protected $taxonomyHandler;

    public function __construct(
        BlogHandler $blogHandler,
        BlogConfigHandler $configHandler,
        BlogTaxonomyHandler $taxonomyHandler
    ) {
        $this->blogHandler = $blogHandler;
        $this->configHandler = $configHandler;
        $this->taxonomyHandler = $taxonomyHandler;

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

        $taxonomyItem = $this->taxonomyHandler->createTaxonomy($taxonomyAttribute);

        return redirect()->route('manage.category.show', ['id' => $taxonomyItem->id]);
    }

    public function connectTaxonomySetting(Request $request)
    {
        $taxonomyId = $request->segment(count($request->segments()));

        return redirect()->route('manage.category.show', ['id' => $taxonomyId]);
    }
}
