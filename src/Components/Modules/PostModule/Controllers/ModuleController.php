<?php

namespace Xpressengine\Plugins\Post\Components\Modules\PostModule\Controllers;

use Auth;
use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\PostFavoriteHandler;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;
use Xpressengine\Plugins\Post\Services\PostService;
use Xpressengine\Routing\InstanceConfig;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;

class ModuleController extends Controller
{
    protected $instanceId;

    /** @var PostHandler $handler */
    protected $handler;

    /** @var PostService $postService */
    protected $postService;

    /** @var PostFavoriteHandler $postFavoriteHandler */
    protected $postFavoriteHandler;

    public function __construct()
    {
        $instanceId = InstanceConfig::instance()->getInstanceId();
        $this->instanceId = $instanceId;

        $handler = app('xe.post.handler');
        $this->handler = $handler;

        $this->postService = app('xe.post.service');

        $this->postFavoriteHandler = new PostFavoriteHandler();

        XePresenter::share('handler', $handler);
    }

    public function index()
    {
        $items = $this->handler->getItems(['instanceId' => $this->instanceId]);

        return XePresenter::make('post::src.Components.Modules.PostModule.views.index', compact('items'));
    }

    public function show(Request $request, $instance, $id)
    {
        $item = $this->handler->get($id, $this->instanceId);

        $item->setCanonical(instance_route('show', ['id' => $id], $this->instanceId));

        return XePresenter::make('post::src.Components.Modules.PostModule.views.show', compact('item'));
    }
}
