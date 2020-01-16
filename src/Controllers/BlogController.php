<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use Auth;
use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogFavoriteHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;

class BlogController extends Controller
{
    /** @var BlogService $blogService */
    protected $blogService;

    /** @var BlogHandler $blogHandler */
    protected $blogHandler;

    /** @var BlogFavoriteHandler $blogFavoriteHandler */
    protected $blogFavoriteHandler;

    public function __construct(BlogService $blogService, BlogHandler $blogHandler)
    {
        $this->blogService = $blogService;
        $this->blogHandler = $blogHandler;

        $this->blogFavoriteHandler = new BlogFavoriteHandler();

        XePresenter::share('metaDataHandler', new BlogMetaDataHandler());
    }

    public function getItemsForJson(Request $request)
    {
        $items = $this->blogService->getItemsJson($request->all());

        return XePresenter::makeApi($items);
    }

    public function create(Request $request)
    {
        app('xe.theme')->selectBlankTheme();

        $redirectUrl = $request->session()->pull('url.intended') ?: url()->previous();
        if ($redirectUrl !== $request->url()) {
            $request->session()->put('url.intended', $redirectUrl);
        }

        $taxonomyGroups = app('xe.blog.taxonomyHandler')->getTaxonomyGroups();

        return XePresenter::make('xe_blog::views.blog.create', compact('taxonomyGroups'));
    }

    public function store(Request $request)
    {
        $this->blogService->store($request, Plugin::getId());

        return redirect()->intended();
    }

    public function show(Request $request, $blogId)
    {
        $redirectUrl = $request->session()->pull('url.intended') ?: url()->previous();
        if ($redirectUrl !== $request->url()) {
            $request->session()->put('url.intended', $redirectUrl);
        }

        $blog = $this->blogHandler->get($blogId);

        $blog->setCanonical(route('blog.show', ['blogId' => $blog->id]));

        return XePresenter::make('xe_blog::views.blog.show', compact('blog'));
    }

    public function edit(Request $request, $blogId)
    {
        app('xe.theme')->selectBlankTheme();

        $redirectUrl = $request->session()->pull('url.intended') ?: url()->previous();
        if ($redirectUrl !== $request->url()) {
            $request->session()->put('url.intended', $redirectUrl);
        }

        $blog = $this->blogHandler->get($blogId);

        return XePresenter::make('xe_blog::views.blog.edit', compact('blog'));
    }

    public function update(Request $request)
    {
        $blogId = $request->get('blogId');
        $blog = $this->blogHandler->get($blogId, Plugin::getId());

        $this->blogService->update($request, $blog);

        return redirect()->intended();
    }

    public function delete(Request $request, $blogId)
    {
        $blog = $this->blogHandler->get($blogId, Plugin::getId());

        $this->blogService->delete($blog, 'blog');

        return redirect()->intended();
    }

    public function setFavoriteState(Request $request)
    {
        if (Auth::check() === false) {
            throw new AccessDeniedHttpException;
        }

        $user = Auth::user();
        $blogId = $request->get('blogId');
        $blogItem = $this->blogHandler->get($blogId, 'blog');

        $favorite = false;
        if ($this->blogFavoriteHandler->isFavoriteBlog($blogItem, $user) === false) {
            $this->blogFavoriteHandler->setFavoriteBlog($blogItem, $user);
            $favorite = true;
        } else {
            $this->blogFavoriteHandler->unsetFavoriteBlog($blogItem, $user);
        }

        return XePresenter::makeApi(['favorite' => $favorite]);
    }
}
