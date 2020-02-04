<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\XeBlog\Interfaces\Jsonable;
use Xpressengine\Plugins\XeBlog\Interfaces\Orderable;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Plugin;

class BlogHandler extends DocumentHandler implements Searchable, Jsonable, Orderable
{
    protected $model = Blog::class;

    public function store($attributes)
    {
        $attributes['instance_id'] = Plugin::getId();
        $attributes['type'] = Plugin::getId();

        if ($attributes['published_at'] === '') {
            $attributes['published_at'] = date('Y-m-d H:i:s');
        }

        //TODO 상수 변경
        if (isset($attributes['blog_status']) === true) {
            switch ($attributes['blog_status']) {
                case 'public':
                    $attributes['status'] = Blog::STATUS_PUBLIC;
                    $attributes['approved'] = Blog::APPROVED_APPROVED;
                    $attributes['display'] = Blog::DISPLAY_VISIBLE;
                    break;

                case 'private':
                    $attributes['status'] = Blog::STATUS_PRIVATE;
                    $attributes['approved'] = Blog::APPROVED_APPROVED;
                    $attributes['display'] = Blog::DISPLAY_SECRET;
                    break;

                case 'temp':
                    $attributes['status'] = Blog::STATUS_TEMP;
                    $attributes['approved'] = Blog::APPROVED_WAITING;
                    $attributes['display'] = Blog::DISPLAY_HIDDEN;
                    break;
            }
        }

        return parent::add($attributes);
    }

    public function getItems($query, $attributes)
    {
        if (isset($attributes['force']) === false || $attributes['force'] === false) {
            $query = $query->visible();
        }

        if (isset($attributes['titleWithContent']) === true) {
            $query = $query->where('title', 'like', '%' . $attributes['titleWithContent'] . '%')
                ->orWhere('content', 'like', '%' . $attributes['titleWithContent'] . '%');
        }

        if (isset($attributes['title']) === true) {
            $query = $query->where('title', 'like', '%' . $attributes['title'] . '%');
        }

        return $query;
    }

    public function getOrder($query, $attributes)
    {
        $blogConfigHandler = app('xe.blog.configHandler');
        $blogConfig = $blogConfigHandler->getBlogConfig();

        $orderType = $blogConfig->get('orderType');
        if (isset($attributes['orderType']) === true) {
            $orderType = $attributes['orderType'];
        }

        switch ($orderType) {
            case BlogConfigHandler::ORDER_TYPE_PUBLISH:
                $query = $query->orderByDesc('published_at');
                break;

            case BlogConfigHandler::ORDER_TYPE_NEW:
                $query = $query->orderByDesc('created_at');
                break;

            case BlogConfigHandler::ORDER_TYPE_UPDATE:
                $query = $query->orderByDesc('updated_at');
                break;

            case BlogConfigHandler::ORDER_TYPE_RECOMMEND:
                $query = $query->orderByDesc('assent_count');
                break;
        }

        return $query;
    }

    public function getJsonData(Blog $blog)
    {
        $blogConfigHandler = app('xe.blog.configHandler');
        $blogConfig = $blogConfigHandler->getBlogConfig();

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'content' => $blog->content,
            'read_count' => $blog->read_count,
            'comment_count' => $blog->comment_count,
            'assent_count' => $blog->assent_count,
            'dissent_count' => $blog->dissent_count,
            'is_new' => $blog->isNew($blogConfig->get('newBlogTime')) === true,
            'created_at' => $blog->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $blog->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function getTypeName()
    {
        return 'blog';
    }

    public function update($blog, $inputs)
    {
        $attributes = $blog->getAttributes();

        foreach ($inputs as $name => $value) {
            if (array_key_exists($name, $attributes)) {
                $blog->{$name} = $value;
            }
        }

        return parent::put($blog);
    }

    public function trashBlog($blog)
    {
        $blog->delete();
    }

    public function restoreBlog($blog)
    {
        $blog->restore();
    }

    public function dropBlog($blog)
    {
        $blog->forceDelete();
    }

    public function setBlogPublic($blog)
    {
        $blog->setPublic();
    }

    public function setBlogPrivate($blog)
    {
        $blog->setPrivate();
    }

    public function setBlogTemp($blog)
    {
        $blog->setTemp();
    }
}
