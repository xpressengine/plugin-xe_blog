<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
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

        $blogItems = $this->blogService->getItems([
            'taxonomy_id' => $taxonomyConfig->get('taxonomy_id'),
            'taxonomy_item_id' => $slug
        ]);

        return $blogItems;
    }
}
