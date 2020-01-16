<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Services\BlogService;

class BlogSettingController extends Controller
{
    /** @var BlogService $blogService */
    protected $blogService;

    /** @var BlogConfigHandler $configHandler */
    protected $configHandler;

    /** @var BlogTaxonomyHandler $taxonomyHandler */
    protected $taxonomyHandler;

    public function __construct(
        BlogService $blogService,
        BlogConfigHandler $configHandler,
        BlogTaxonomyHandler $taxonomyHandler
    ) {
        $this->blogService = $blogService;
        $this->configHandler = $configHandler;
        $this->taxonomyHandler = $taxonomyHandler;

        XePresenter::share('metaDataHandler', new BlogMetaDataHandler());
        XePresenter::share('taxonomyHandler', $taxonomyHandler);
    }

    public function blogs(Request $request)
    {
        $blogs = $this->blogService->getItems($request);
        $taxonomies = $this->taxonomyHandler->getTaxonomies();

        return XePresenter::make('xe_blog::views.setting.blogs', compact('blogs', 'taxonomies'));
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
