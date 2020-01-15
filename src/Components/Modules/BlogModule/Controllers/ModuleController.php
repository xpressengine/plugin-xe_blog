<?php

namespace Xpressengine\Plugins\XeBlog\Components\Modules\BlogModule\Controllers;

use Auth;
use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogFavoriteHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Routing\InstanceConfig;

class ModuleController extends Controller
{
    protected $instanceId;

    /** @var BlogHandler $handler */
    protected $handler;

    /** @var BlogService $blogService */
    protected $blogService;

    /** @var BlogFavoriteHandler $blogFavoriteHandler */
    protected $blogFavoriteHandler;

    public function __construct()
    {
        $instanceId = InstanceConfig::instance()->getInstanceId();
        $this->instanceId = $instanceId;

        $handler = app('xe.blog.handler');
        $this->handler = $handler;

        $this->blogService = app('xe.blog.service');

        $this->blogFavoriteHandler = new BlogFavoriteHandler();

        XePresenter::share('handler', $handler);
    }

    public function index()
    {
        $items = $this->handler->getItems(['instanceId' => $this->instanceId]);

        return XePresenter::make('xe_blog::src.Components.Modules.BlogModule.views.index', compact('items'));
    }

    public function show(Request $request, $instance, $id)
    {
        $item = $this->handler->get($id, $this->instanceId);

        $item->setCanonical(instance_route('show', ['id' => $id], $this->instanceId));

        return XePresenter::make('xe_blog::src.Components.Modules.BlogModule.views.show', compact('item'));
    }
}
