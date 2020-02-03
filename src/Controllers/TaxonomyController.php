<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogFavoriteHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Services\BlogService;

class TaxonomyController extends Controller
{
    /** @var BlogService $blogService */
    protected $blogService;

    /** @var BlogTaxonomyHandler $taxonomyHandler */
    protected $taxonomyHandler;

    public function __construct(BlogService $blogService, BlogTaxonomyHandler $taxonomyHandler)
    {
        $this->blogService = $blogService;
        $this->taxonomyHandler = $taxonomyHandler;
    }

    protected function getTaxonomyConfigBySlug($segment)
    {
        $taxonomyUrls = $this->taxonomyHandler->getTaxonomyUseUrls();

        $taxonomyId = array_search($segment, $taxonomyUrls);
        if ($taxonomyId === false) {
            return null;
        }

        return $this->taxonomyHandler->getTaxonomyInstanceConfig($taxonomyId);
    }

    public function index(Request $request, $slug)
    {
        $taxonomyConfig = $this->getTaxonomyConfigBySlug($request->segment(1));

        $blogs = $this->blogService->getItems([
            'taxonomy_id' => $taxonomyConfig->get('taxonomy_id'),
            'taxonomy_item_id' => $slug
        ]);

        $taxonomies = $this->taxonomyHandler->getTaxonomies();

        $blogConfig = app('xe.blog.configHandler')->getBlogConfig();

        XePresenter::share('metaDataHandler', new BlogMetaDataHandler());
        XePresenter::share('favoriteHandler', new BlogFavoriteHandler());
        XePresenter::share('taxonomyHandler', $this->taxonomyHandler);

        return \XePresenter::make('xe_blog::views.blog.index', compact('blogs', 'taxonomies', 'blogConfig'));
    }
}
