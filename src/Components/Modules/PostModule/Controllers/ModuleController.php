<?php

namespace Xpressengine\Plugins\Post\Components\Modules\PostModule\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\Handler;
use Xpressengine\Routing\InstanceConfig;

class ModuleController extends Controller
{
    protected $instanceId;

    /** @var Handler $handler */
    protected $handler;

    public function __construct()
    {
        $instanceId = InstanceConfig::instance()->getInstanceId();
        $this->instanceId = $instanceId;

        $handler = app('xe.post.handler');
        $this->handler = $handler;

        XePresenter::share('instanceId', $instanceId);
        XePresenter::share('handler', $handler);
    }

    public function index()
    {
        $items = $this->handler->getItems(['instanceId' => $this->instanceId]);

        return XePresenter::make('post::src.Components.Modules.PostModule.views.index', compact('items'));
    }

    public function create()
    {
        return XePresenter::make('post::src.Components.Modules.PostModule.views.create');
    }

    public function store(Request $request)
    {
        $this->handler->store($request->originAll(), $this->instanceId);

        return redirect(instance_route('index', [], $this->instanceId));
    }

    public function show(Request $request, $instance, $id)
    {
        $item = $this->handler->get($id, $this->instanceId);

        return XePresenter::make('post::src.Components.Modules.PostModule.views.show', compact('item'));
    }

    public function edit(Request $request, $instance, $id)
    {
        $item = $this->handler->get($id, $this->instanceId);

        return XePresenter::make('post::src.Components.Modules.PostModule.views.edit', compact('item'));
    }

    public function update(Request $request, $instance)
    {
        $id = $request->get('postId');
        $item = $this->handler->get($id, $this->instanceId);

        $this->handler->update($item, $request->originAll());

        return redirect(instance_route('show', ['id' => $id], $this->instanceId));
    }
}
