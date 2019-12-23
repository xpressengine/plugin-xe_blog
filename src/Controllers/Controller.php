<?php

namespace Xpressengine\Plugins\Post\Controllers;

use XeFrontend;
use XePresenter;
use App\Http\Controllers\Controller as BaseController;
use Xpressengine\Plugins\Post\Plugin;

class Controller extends BaseController
{
    public function index()
    {
        $title = 'Post';

        // set browser title
        XeFrontend::title($title);

        // load css file
        XeFrontend::css(Plugin::asset('assets/style.css'))->load();

        // output
        return XePresenter::make('post::views.index', ['title' => $title]);
    }
}
