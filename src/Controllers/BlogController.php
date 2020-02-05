<?php

namespace Xpressengine\Plugins\XeBlog\Controllers;

use Auth;
use XePresenter;
use Gate;
use App\Http\Controllers\Controller;
use Xpressengine\DynamicField\ConfigHandler;
use Xpressengine\Http\Request;
use Xpressengine\Permission\Instance;
use Xpressengine\Plugins\XeBlog\Exceptions\NotFoundBlogException;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogFavoriteHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogValidatorHandler;
use Xpressengine\Plugins\XeBlog\Models\BlogSlug;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;

class BlogController extends Controller
{
    /** @var BlogService $blogService */
    protected $blogService;

    /** @var BlogHandler $blogHandler */
    protected $blogHandler;

    /** @var BlogValidatorHandler $blogValidationHandler */
    protected $blogValidationHandler;

    /** @var BlogFavoriteHandler $blogFavoriteHandler */
    protected $blogFavoriteHandler;

    /** @var ConfigHandler $dynamicFieldConfigHandler */
    protected $dynamicFieldConfigHandler;

    /** @var BlogPermissionHandler $blogPermissionHandler */
    protected $blogPermissionHandler;

    /** @var BlogConfigHandler $blogConfigHandler */
    protected $blogConfigHandler;

    public function __construct(BlogService $blogService, BlogHandler $blogHandler, BlogValidatorHandler $blogValidationHandler)
    {
        $this->blogService = $blogService;
        $this->blogHandler = $blogHandler;

        $favoriteHandler = new BlogFavoriteHandler();
        $this->blogFavoriteHandler = $favoriteHandler;
        $this->blogValidationHandler = $blogValidationHandler;
        $this->dynamicFieldConfigHandler = app('xe.dynamicField');
        $this->blogPermissionHandler = app('xe.blog.permissionHandler');
        $this->blogConfigHandler = app('xe.blog.configHandler');

        XePresenter::share('metaDataHandler', new BlogMetaDataHandler());
        XePresenter::share('favoriteHandler', $favoriteHandler);
    }

    private function checkAllowPermission($action)
    {
        return Gate::allows($action, new Instance($this->blogPermissionHandler->getPermissionName()));
    }

    public function getItemsForJson(Request $request)
    {
        if ($this->checkAllowPermission(BlogPermissionHandler::ACTION_LIST) === false) {
            throw new AccessDeniedHttpException;
        }

        $items = $this->blogService->getItemsJson($request->all());

        return XePresenter::makeApi($items);
    }

    public function create(Request $request)
    {
        if ($this->checkAllowPermission(BlogPermissionHandler::ACTION_CREATE) === false) {
            throw new AccessDeniedHttpException;
        }

        app('xe.theme')->selectBlankTheme();

        $taxonomies = app('xe.blog.taxonomyHandler')->getTaxonomies();

        $dynamicFields = $this->dynamicFieldConfigHandler->gets('documents_' . Plugin::getId());

        return XePresenter::make('xe_blog::views.blog.create', compact('taxonomies', 'dynamicFields'));
    }

    public function store(Request $request)
    {
        if ($this->checkAllowPermission(BlogPermissionHandler::ACTION_CREATE) === false) {
            throw new AccessDeniedHttpException;
        }

        $rules = $this->blogValidationHandler->getRules(Auth::user(), $this->blogConfigHandler->getBlogConfig());
        $this->validate($request, $rules);

        $blog = $this->blogService->store($request);

        return redirect()->route('blog.show', ['blogId' => $blog->id]);
    }

    public function showId(Request $request, $blogId)
    {
        $blog = $this->blogService->getItem($blogId);
        if ($blog === null) {
            throw new NotFoundBlogException;
        }

        $blog->setCanonical(route('blog.show', ['blogId' => $blogId]));

        if ($blog->slug !== null) {
            return redirect()->route('blog.show_slug', ['slug' => $blog->slug['slug']]);
        }

        return $this->show($request, $blog);
    }

    public function showSlug(Request $request, $slug)
    {
        $blogSlug = BlogSlug::where('slug', $slug)->first();
        if ($blogSlug === null) {
            throw new NotFoundBlogException;
        }

        $blog = $this->blogService->getItem($blogSlug->target_id);
        $blog->setCanonical(route('blog.show_slug', ['slug' => $slug]));

        return $this->show($request, $blog);
    }

    private function show(Request $request, $blog)
    {
        if ($this->checkAllowPermission(BlogPermissionHandler::ACTION_READ) === false) {
            throw new AccessDeniedHttpException;
        }

        XePresenter::setSkinTargetId('blog/show');

        $dynamicFields = $this->dynamicFieldConfigHandler->gets('documents_' . Plugin::getId());

        return XePresenter::make('show', compact('blog', 'dynamicFields'));
    }

    public function edit(Request $request, $blogId)
    {
        $blog = $this->blogService->getItem($blogId);

        if ($this->blogService->checkItemPermission(
                $blog,
                Auth::user(),
                $this->checkAllowPermission(BlogPermissionHandler::ACTION_MANAGE)
            ) === false) {
            throw new AccessDeniedHttpException;
        }

        app('xe.theme')->selectBlankTheme();

        $taxonomies = app('xe.blog.taxonomyHandler')->getTaxonomies();
        $dynamicFields = $this->dynamicFieldConfigHandler->gets('documents_' . Plugin::getId());

        return XePresenter::make('xe_blog::views.blog.edit', compact('blog', 'dynamicFields', 'taxonomies'));
    }

    public function update(Request $request)
    {
        $blogId = $request->get('blogId');
        $blog = $this->blogService->getItem($blogId);

        if ($this->blogService->checkItemPermission(
                $blog,
                Auth::user(),
                $this->checkAllowPermission(BlogPermissionHandler::ACTION_MANAGE)
            ) === false) {
            throw new AccessDeniedHttpException;
        }

        $rules = $this->blogValidationHandler->getRules(Auth::user(), $this->blogConfigHandler->getBlogConfig());
        $this->validate($request, $rules);

        $this->blogService->update($request, $blog);

        return redirect()->route('blog.show', ['blogId' => $blogId]);
    }

    public function delete(Request $request, $blogId)
    {
        $blog = $this->blogService->getItem($blogId);

        if ($this->blogService->checkItemPermission(
                $blog,
                Auth::user(),
                $this->checkAllowPermission(BlogPermissionHandler::ACTION_MANAGE)
            ) === false) {
            throw new AccessDeniedHttpException;
        }

        $this->blogService->delete($blog, 'blog');

        return redirect('/');
    }

    public function setFavoriteState(Request $request)
    {
        if (Auth::check() === false) {
            throw new AccessDeniedHttpException;
        }

        $user = Auth::user();
        $blogId = $request->get('blogId');
        $blogItem = $this->blogService->getItem($blogId);

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
