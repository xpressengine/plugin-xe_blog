<?php

namespace Xpressengine\Plugins\Post\Controllers;

use Auth;
use XePresenter;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\PostFavoriteHandler;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;
use Xpressengine\Plugins\Post\Plugin;
use Xpressengine\Plugins\Post\Services\PostService;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;

class PostController extends Controller
{
    /** @var PostService $postService */
    protected $postService;

    /** @var PostHandler $postHandler */
    protected $postHandler;

    /** @var PostFavoriteHandler $postFavoriteHandler */
    protected $postFavoriteHandler;

    public function __construct(PostService $postService, PostHandler $postHandler)
    {
        $this->postService = $postService;
        $this->postHandler = $postHandler;

        $this->postFavoriteHandler = new PostFavoriteHandler();

        XePresenter::share('metaDataHandler', new PostMetaDataHandler());
    }

    public function create(Request $request)
    {
        $redirectUrl = $request->session()->pull('url.intended') ?: url()->previous();
        if ($redirectUrl !== $request->url()) {
            $request->session()->put('url.intended', $redirectUrl);
        }

        return XePresenter::make('post::views.post.create');
    }

    public function store(Request $request)
    {
        $this->postService->store($request, Plugin::getId());

        return redirect()->intended();
    }

    public function show(Request $request, $postId)
    {
        $redirectUrl = $request->session()->pull('url.intended') ?: url()->previous();
        if ($redirectUrl !== $request->url()) {
            $request->session()->put('url.intended', $redirectUrl);
        }

        $post = $this->postHandler->get($postId);

        $post->setCanonical(route('post.show', ['postId' => $post->id]));

        return XePresenter::make('post::views.post.show', compact('post'));
    }

    public function edit(Request $request, $postId)
    {
        $redirectUrl = $request->session()->pull('url.intended') ?: url()->previous();
        if ($redirectUrl !== $request->url()) {
            $request->session()->put('url.intended', $redirectUrl);
        }

        $post = $this->postHandler->get($postId);

        return XePresenter::make('post::views.post.edit', compact('post'));
    }

    public function update(Request $request)
    {
        $postId = $request->get('postId');
        $post = $this->postHandler->get($postId, Plugin::getId());

        $this->postService->update($request, $post);

        return redirect()->intended();
    }

    public function delete(Request $request, $postId)
    {
        $post = $this->postHandler->get($postId, Plugin::getId());

        $this->postService->delete($post, 'post');

        return redirect()->intended();
    }

    public function setFavoriteState(Request $request)
    {
        if (Auth::check() === false) {
            throw new AccessDeniedHttpException;
        }

        $user = Auth::user();
        $postId = $request->get('postId');
        $postItem = $this->postHandler->get($postId, 'post');

        $favorite = false;
        if ($this->postFavoriteHandler->isFavoritePost($postItem, $user) === false) {
            $this->postFavoriteHandler->setFavoritePost($postItem, $user);
            $favorite = true;
        } else {
            $this->postFavoriteHandler->unsetFavoritePost($postItem, $user);
        }

        return XePresenter::makeApi(['favorite' => $favorite]);
    }
}
