<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTemplateHandler;

class BlogTemplateController extends Controller
{
    protected $templateHandler;

    public function __construct()
    {
        $this->templateHandler = new BlogTemplateHandler();
    }

    public function index(Request $request)
    {
        $templateItems = $this->templateHandler->getItems($request->all());

        return XePresenter::makeApi(['templateItems' => $templateItems]);
    }

    public function getItem($templateId)
    {
        $templateItem = $this->templateHandler->getItem($templateId);

        return XePresenter::makeApi(['templateItem' => $templateItem]);
    }

    public function store(Request $request)
    {
        $attributes = $request->except('_token');

        if (isset($attributes['user_id']) === false) {
            $attributes['user_id'] = \Auth::user()->getId();
        }

        $this->templateHandler->store($attributes);

        return XePresenter::makeApi(['result' => true]);
    }

    public function delete($templateId)
    {
        $templateItem = $this->templateHandler->getItem($templateId);

        if ($templateItem !== null) {
            $this->templateHandler->drop($templateItem);
        }

        return XePresenter::makeApi(['result' => true]);
    }
}
