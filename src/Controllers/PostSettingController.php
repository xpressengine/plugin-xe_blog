<?php

namespace Xpressengine\Plugins\Post\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;
use Xpressengine\Plugins\Post\Plugin;

class PostSettingController extends Controller
{
    protected $postHandler;

    public function __construct(PostHandler $postHandler)
    {
        $this->postHandler = $postHandler;

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
}
